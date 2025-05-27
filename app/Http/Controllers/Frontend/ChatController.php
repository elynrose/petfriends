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
        $chats = Chat::with(['booking', 'booking.pet', 'booking.user'])
            ->whereHas('booking', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->orWhereHas('booking', function($query) {
                $query->where('pet_id', function($subquery) {
                    $subquery->select('pet_id')
                        ->from('bookings')
                        ->whereColumn('bookings.id', 'chats.booking_id')
                        ->where('user_id', Auth::id());
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontend.chats.index', compact('chats'));
    }

    public function create()
    {
        $bookings = Booking::with(['pet', 'user'])
            ->where('user_id', Auth::id())
            ->orWhereHas('pet', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->get();

        return view('frontend.chats.create', compact('bookings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'message' => 'required|string',
            'photo' => 'nullable|array',
            'photo.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $booking = Booking::findOrFail($request->booking_id);
        
        if ($booking->user_id !== Auth::id() && $booking->pet->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to chat in this booking.');
        }

        $chat = new Chat();
        $chat->booking_id = $request->booking_id;
        $chat->message = $request->message;
        $chat->from_id = Auth::id();
        $chat->to_id = $booking->user_id === Auth::id() ? $booking->pet->user_id : $booking->user_id;
        $chat->save();

        if ($request->hasFile('photo')) {
            foreach ($request->file('photo') as $file) {
                $chat->addMedia($file)->toMediaCollection('photo');
            }
        }

        broadcast(new NewChatMessage($chat))->toOthers();

        return redirect()->route('frontend.chats.show', $chat->id);
    }

    public function show(Chat $chat)
    {
        if ($chat->booking->user_id !== Auth::id() && $chat->booking->pet->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to view this chat.');
        }

        $chat->load(['booking', 'booking.pet', 'booking.user', 'media']);

        return view('frontend.chats.show', compact('chat'));
    }

    public function edit(Chat $chat)
    {
        if ($chat->from_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only edit your own messages.');
        }

        return view('frontend.chats.edit', compact('chat'));
    }

    public function update(Request $request, Chat $chat)
    {
        if ($chat->from_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only edit your own messages.');
        }

        $request->validate([
            'message' => 'required|string',
            'photo' => 'nullable|array',
            'photo.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $chat->message = $request->message;
        $chat->save();

        if ($request->hasFile('photo')) {
            $chat->clearMediaCollection('photo');
            foreach ($request->file('photo') as $file) {
                $chat->addMedia($file)->toMediaCollection('photo');
            }
        }

        return redirect()->route('frontend.chats.show', $chat->id);
    }

    public function destroy(Chat $chat)
    {
        if ($chat->from_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only delete your own messages.');
        }

        $chat->delete();

        return redirect()->route('frontend.chats.index');
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
        if ($booking->user_id !== Auth::id() && $booking->pet->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $booking->chats()
            ->with(['from', 'media'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id() && $booking->pet->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'message' => 'required|string'
        ]);

        $chat = new Chat();
        $chat->booking_id = $booking->id;
        $chat->message = $request->message;
        $chat->from_id = Auth::id();
        $chat->to_id = $booking->user_id === Auth::id() ? $booking->pet->user_id : $booking->user_id;
        $chat->save();

        broadcast(new NewChatMessage($chat))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $chat->load('from')
        ]);
    }

    public function markMessagesAsRead(Booking $booking)
    {
        if ($booking->user_id !== Auth::id() && $booking->pet->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $booking->chats()
            ->where('to_id', Auth::id())
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json(['success' => true]);
    }
}
