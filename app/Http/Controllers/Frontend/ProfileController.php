<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('frontend.profile.index', compact('user'));
    }

    public function edit()
    {
        return view('frontend.profile.edit');
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $data = $request->all();

        if ($request->has('photo')) {
            if ($user->getFirstMedia('photo')) {
                $user->getFirstMedia('photo')->delete();
            }
            $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::find($request->input('photo'));
            if ($media) {
                $media->model_id = $user->id;
                $media->model_type = get_class($user);
                $media->collection_name = 'photo';
                $media->save();
            }
            unset($data['photo']);
        }

        $user->update($data);

        return redirect()->route('frontend.profile.index')
            ->with('message', 'Profile updated successfully.');
    }

    public function destroy()
    {
        $user = auth()->user();
        $user->delete();

        return redirect()->route('frontend.profile.index')
            ->with('message', 'Profile deleted successfully.');
    }

    public function password(UpdatePasswordRequest $request)
    {
        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);

        return redirect()->route('frontend.profile.index')
            ->with('message', 'Password updated successfully.');
    }

    public function storeMedia(Request $request)
    {
        $model = new User();
        $model->id = 0;
        $model->exists = true;
        $media = $model->addMediaFromRequest('photo')->toMediaCollection('photo');

        return response()->json($media, Response::HTTP_CREATED);
    }

    public function toggleTwoFactor()
    {
        $user = auth()->user();
        $user->two_factor = !$user->two_factor;
        $user->save();

        return redirect()->route('frontend.profile.index')
            ->with('message', 'Two factor authentication ' . ($user->two_factor ? 'enabled' : 'disabled') . ' successfully.');
    }
}
