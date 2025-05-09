<?php

namespace App\Http\Controllers\Admin;

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

class ChatController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('chat_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $chats = Chat::with(['booking', 'from', 'media'])->get();

        return view('admin.chats.index', compact('chats'));
    }

    public function create()
    {
        abort_if(Gate::denies('chat_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bookings = Booking::pluck('status', 'id')->prepend(trans('global.pleaseSelect'), '');

        $froms = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.chats.create', compact('bookings', 'froms'));
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

        return redirect()->route('admin.chats.index');
    }

    public function edit(Chat $chat)
    {
        abort_if(Gate::denies('chat_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bookings = Booking::pluck('status', 'id')->prepend(trans('global.pleaseSelect'), '');

        $froms = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $chat->load('booking', 'from');

        return view('admin.chats.edit', compact('bookings', 'chat', 'froms'));
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

        return redirect()->route('admin.chats.index');
    }

    public function show(Chat $chat)
    {
        abort_if(Gate::denies('chat_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $chat->load('booking', 'from');

        return view('admin.chats.show', compact('chat'));
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
}
