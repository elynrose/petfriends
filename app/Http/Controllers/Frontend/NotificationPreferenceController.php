<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    public function edit()
    {
        $preferences = auth()->user()->getNotificationPreferences();
        return view('frontend.notifications.preferences', compact('preferences'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'pet_available' => 'boolean',
            'booking_requested' => 'boolean',
            'booking_accepted' => 'boolean',
            'booking_rejected' => 'boolean',
            'booking_completed' => 'boolean',
            'new_message' => 'boolean',
            'email_notifications' => 'boolean',
        ]);

        $preferences = auth()->user()->getNotificationPreferences();
        $preferences->update($validated);

        return redirect()
            ->route('frontend.notifications.preferences.edit')
            ->with('success', 'Notification preferences updated successfully');
    }
} 