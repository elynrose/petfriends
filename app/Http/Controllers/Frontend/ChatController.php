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
        \Log::info('=== START: Get Messages ===');
        \Log::info('Fetching messages for booking', [
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'booking_user_id' => $booking->user_id,
            'pet_user_id' => $booking->pet->user_id
        ]);

        try {
            // Check if user is authorized to view these messages
            if (Auth::id() !== $booking->user_id && Auth::id() !== $booking->pet->user_id) {
                \Log::warning('Unauthorized access attempt to messages', [
                    'booking_id' => $booking->id,
                    'user_id' => Auth::id()
                ]);
                return response()->json(['error' => 'Unauthorized access'], 403);
            }

            // Check if user has premium access
            if (!Auth::user()->canUseChat()) {
                \Log::warning('Non-premium user attempting to access chat', [
                    'booking_id' => $booking->id,
                    'user_id' => Auth::id()
                ]);
                return response()->json(['error' => 'Chat is a premium feature. Please upgrade to access.'], 403);
            }

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
                        'from_photo' => $userPhoto,
                        'created_at' => $message->created_at->format('Y-m-d H:i:s')
                    ];
                });

            \Log::info('Messages fetched successfully', [
                'booking_id' => $booking->id,
                'message_count' => $messages->count(),
                'first_message' => $messages->first(),
                'last_message' => $messages->last()
            ]);

            return response()->json($messages);
        } catch (\Exception $e) {
            \Log::error('Error fetching messages', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'booking_id' => $booking->id,
                'user_id' => Auth::id()
            ]);
            return response()->json(['error' => 'Failed to fetch messages: ' . $e->getMessage()], 500);
        } finally {
            \Log::info('=== END: Get Messages ===');
        }
    }

    public function sendMessage(Request $request, Booking $booking)
    {
        \Log::info('=== START: Chat Message Process ===');
        \Log::info('Received message request', [
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'request_data' => $request->all(),
            'auth_check' => Auth::check(),
            'user' => Auth::user()->toArray()
        ]);

        // Check if user is authorized to send messages
        if (Auth::id() !== $booking->user_id && Auth::id() !== $booking->pet->user_id) {
            \Log::warning('Unauthorized message attempt', [
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'booking_user_id' => $booking->user_id,
                'pet_user_id' => $booking->pet->user_id
            ]);
            abort(403);
        }

        // Check if user has premium access
        if (!Auth::user()->canUseChat()) {
            \Log::warning('Non-premium user attempting to send message', [
                'booking_id' => $booking->id,
                'user_id' => Auth::id()
            ]);
            return response()->json(['error' => 'Chat is a premium feature. Please upgrade to access.'], 403);
        }

        try {
            $request->validate([
                'message' => 'required|string|max:1000',
            ]);

            \Log::info('Validation passed, preparing to save message', [
                'booking_id' => $booking->id,
                'from_id' => Auth::id(),
                'message' => $request->message,
                'auth_check' => Auth::check(),
                'user' => Auth::user()->toArray(),
                'request_data' => $request->all()
            ]);

            // Check if booking exists
            if (!$booking) {
                \Log::error('Booking not found', ['booking_id' => $booking->id]);
                return response()->json(['error' => 'Booking not found'], 404);
            }

            // Check if user is authenticated
            if (!Auth::check()) {
                \Log::error('User not authenticated');
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Create the chat message
            $chat = new Chat();
            $chat->booking_id = $booking->id;
            $chat->from_id = Auth::id();
            $chat->message = $request->message;
            $chat->read = false;
            $chat->save();

            \Log::info('Chat message saved successfully', [
                'chat_id' => $chat->id,
                'message' => $chat->message,
                'created_at' => $chat->created_at,
                'booking_id' => $chat->booking_id,
                'from_id' => $chat->from_id,
                'saved_data' => $chat->toArray()
            ]);

            // Load the user relationship for the broadcast
            $chat->load(['from' => function($query) {
                $query->with('media');
            }]);

            \Log::info('Broadcasting message', [
                'chat_id' => $chat->id,
                'channel' => 'booking.' . $booking->id
            ]);

            // Get the user's photo URL
            $userPhoto = $chat->from->getFirstMediaUrl('photo', 'thumb');

            // Broadcast the new message to all users
            broadcast(new NewChatMessage($chat));

            \Log::info('Message broadcast completed');

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $chat->id,
                    'message' => $chat->message,
                    'from_id' => $chat->from_id,
                    'from_name' => $chat->from->name,
                    'from_photo' => $userPhoto,
                    'created_at' => $chat->created_at->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in chat message process', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return response()->json([
                'error' => 'Failed to save message',
                'debug_info' => [
                    'error_message' => $e->getMessage(),
                    'error_time' => now()->toDateTimeString(),
                    'error_line' => $e->getLine(),
                    'error_file' => $e->getFile()
                ]
            ], 500);
        } finally {
            \Log::info('=== END: Chat Message Process ===');
        }
    }

    public function markAsRead(Booking $booking)
    {
        // Check if user is authorized to mark messages as read
        if (Auth::id() !== $booking->user_id && Auth::id() !== $booking->pet->user_id) {
            abort(403);
        }

        // Check if user has premium access
        if (!Auth::user()->canUseChat()) {
            return response()->json(['error' => 'Chat is a premium feature. Please upgrade to access.'], 403);
        }

        Chat::where('booking_id', $booking->id)
            ->where('from_id', '!=', Auth::id())
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json(['success' => true]);
    }
}
