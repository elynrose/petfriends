<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\PetNotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(PetNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $notifications = $this->notificationService->getUserNotifications(auth()->id());
        
        return view('frontend.notifications.index', compact('notifications'));
    }

    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(auth()->id());
        
        return redirect()->back()->with('success', 'All notifications marked as read');
    }
} 