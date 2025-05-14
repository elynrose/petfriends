<?php

namespace App\Http\Controllers\Frontend;

use App\Events\NewChatMessage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyChatRequest;
use App\Http\Requests\StoreChatRequest;
use App\Http\Requests\UpdateChatRequest;
use App\Models\Booking;
use App\Models\Chat;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('chat_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $chats = Chat::with(['booking', 'from', 'media'])->get();

        return view('frontend.chats.index', compact('chats'));
    }

    public function create()
    {
        abort_if(Gate::denies('chat_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bookings = Booking::pluck('status', 'id')->prepend(trans('global.pleaseSelect'), '');

        $froms = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('frontend.chats.create', compact('bookings', 'froms'));
    }

    public function store(StoreChatRequest $request)
    {
        $chat = Chat::create($request->all());

        if ($request->input('photo', false)) {
            $chat->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $chat->id]);
        }

        return redirect()->route('frontend.chats.index');
    }

    public function edit(Chat $chat)
    {
        abort_if(Gate::denies('chat_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bookings = Booking::pluck('status', 'id')->prepend(trans('global.pleaseSelect'), '');

        $froms = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $chat->load('booking', 'from');

        return view('frontend.chats.edit', compact('bookings', 'chat', 'froms'));
    }

    public function update(UpdateChatRequest $request, Chat $chat)
    {
        $chat->update($request->all());

        if ($request->input('photo', false)) {
            if (! $chat->photo || $request->input('photo') !== $chat->photo->file_name) {
                if ($chat->photo) {
                    $chat->photo->delete();
                }
                $chat->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
            }
        } elseif ($chat->photo) {
            $chat->photo->delete();
        }

        return redirect()->route('frontend.chats.index');
    }

    public function show(Chat $chat)
    {
        abort_if(Gate::denies('chat_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $chat->load('booking', 'from');

        return view('frontend.chats.show', compact('chat'));
    }

    public function destroy(Chat $chat)
    {
        abort_if(Gate::denies('chat_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $chat->delete();

        return back();
    }

    public function massDestroy(MassDestroyChatRequest $request)
    {
        $chats = Chat::find(request('ids'));

        foreach ($chats as $chat) {
            $chat->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('chat_create') && Gate::denies('chat_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Chat();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function getMessages(Booking $booking)
    {
        // Check if user is authorized to view these messages
        if (Auth::id() !== $booking->user_id && Auth::id() !== $booking->pet->user_id) {
            abort(403);
        }

        \Log::info('Fetching messages for booking', ['booking_id' => $booking->id]);

        $messages = Chat::with(['from' => function($query) {
                $query->with('media');
            }])
            ->where('booking_id', $booking->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                $userPhoto = $message->from->getFirstMediaUrl('photo', 'thumb');
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'from_id' => $message->from_id,
                    'from_name' => $message->from->name,
                    'from_photo' => $userPhoto ?: null,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s')
                ];
            });

        \Log::info('Fetched messages', ['count' => $messages->count()]);

        return response()->json($messages);
    }

    public function sendMessage(Request $request, Booking $booking)
    {
        \Log::info('Received message request', [
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        // Check if user is authorized to send messages
        if (Auth::id() !== $booking->user_id && Auth::id() !== $booking->pet->user_id) {
            \Log::warning('Unauthorized message attempt', [
                'booking_id' => $booking->id,
                'user_id' => Auth::id()
            ]);
            abort(403);
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        \Log::info('Saving chat message', [
            'booking_id' => $booking->id,
            'from_id' => Auth::id(),
            'message' => $request->message
        ]);

        try {
            $chat = Chat::create([
                'booking_id' => $booking->id,
                'from_id' => Auth::id(),
                'message' => $request->message,
                'read' => false,
            ]);

            \Log::info('Chat message saved', ['chat_id' => $chat->id]);

            // Load the user relationship for the broadcast
            $chat->load('from');

            // Broadcast the new message
            broadcast(new NewChatMessage($chat))->toOthers();

            return response()->json($chat);
        } catch (\Exception $e) {
            \Log::error('Error saving chat message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to save message'], 500);
        }
    }

    public function markAsRead(Booking $booking)
    {
        // Check if user is authorized to mark messages as read
        if (Auth::id() !== $booking->user_id && Auth::id() !== $booking->pet->user_id) {
            abort(403);
        }

        Chat::where('booking_id', $booking->id)
            ->where('from_id', '!=', Auth::id())
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json(['success' => true]);
    }
}
