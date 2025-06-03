<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProfileController extends Controller
{
    use MediaUploadingTrait;

    /**
     * Display the user's profile page.
     */
    public function index()
    {
        $user = Auth::user();
        return view('frontend.profile', compact('user'));
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('frontend.profile', compact('user'));
    }

     /**
     * Handle media updates for the pet
     *
     * @param Pet $pet
     * @param Request $request
     * @return void
     */
    private function handleMediaUpdates(User $user, Request $request): void
    {
        $currentPhotos = $user->photo->pluck('file_name')->toArray();
        $newPhotos = $request->input('photo', []);
        
        // Delete removed photo
        $user->photo->each(function ($media) use ($newPhotos) {
            if (!in_array($media->file_name, $newPhotos)) {
                $media->delete();
            }
        });

        // Add new photos
        foreach ($newPhotos as $file) {
            if (!in_array($file, $currentPhotos)) {
                $user->addMedia(storage_path('tmp/uploads/' . basename($file)))
                    ->toMediaCollection('photo');
            }
        }
    }

    /**
     * Update the user's profile information.
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            $user->clearMediaCollection('photo');
            // Add new photo
            $user->addMedia($request->file('photo'))->toMediaCollection('photo');
        } elseif ($request->input('photo')) {
            $path = storage_path('tmp/uploads');
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $fileName = basename($request->input('photo'));
            $filePath = $path . '/' . $fileName;

            if (file_exists($filePath)) {
                // Delete old photo if exists
                $user->clearMediaCollection('photo');
                // Add new photo from Dropzone
                $user->addMedia($filePath)->toMediaCollection('photo');
            }
        }

        $user->update($data);

        return view('frontend.profile', compact('user'))
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function password(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('frontend.profile.index')
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy()
    {
        $user = auth()->user();
        $user->delete();

        return redirect()->route('home')
            ->with('success', 'Your account has been deleted successfully.');
    }

    /**
     * Handle photo upload for the user's profile.
     */
    public function storeMedia(Request $request)
    {
        $path = storage_path('tmp/uploads');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');
        $name = uniqid() . '_' . trim($file->getClientOriginalName());
        $file->move($path, $name);

        return response()->json([
            'name' => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    /**
     * Toggle two-factor authentication for the user.
     */
    public function toggleTwoFactor()
    {
        $user = auth()->user();
        $user->two_factor = !$user->two_factor;
        $user->save();

        return redirect()->route('frontend.profile.index')
            ->with('success', 'Two-factor authentication ' . ($user->two_factor ? 'enabled' : 'disabled') . ' successfully.');
    }
}
