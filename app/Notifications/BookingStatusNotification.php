<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\TwilioChannel;

class BookingStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    protected $status;

    public function __construct(Booking $booking, $status)
    {
        $this->booking = $booking;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        $channels = ['mail'];
        
        if ($notifiable->phone_number && $notifiable->sms_notifications) {
            $channels[] = TwilioChannel::class;
        }
        
        return $channels;
    }

    public function toMail($notifiable)
    {
        $ownerName = $this->booking->pet && $this->booking->pet->user ? $this->booking->pet->user->name : 'Pet Owner';
        $requesterName = $this->booking->user ? $this->booking->user->name : 'Requester';
        $petName = $this->booking->pet ? $this->booking->pet->name : 'Pet';
        $fromDate = $this->booking->from . ' ' . $this->booking->from_time;
        $toDate = $this->booking->to . ' ' . $this->booking->to_time;

        $message = (new MailMessage)
            ->subject('Booking Status Update')
            ->greeting('Hello ' . $notifiable->name . '!');

        switch ($this->status) {
            case 'new':
                $message->line('A new booking request has been received for ' . $petName . '.')
                    ->line('Requester: ' . $requesterName)
                    ->line('📅 Booking Details:')
                    ->line('• Start: ' . $fromDate)
                    ->line('• End: ' . $toDate)
                    ->action('View Booking', route('frontend.bookings.index'));
                break;

            case 'accepted':
                $message->line('Your booking request for ' . $petName . ' has been approved.')
                    ->line('📅 Booking Details:')
                    ->line('• Start: ' . $fromDate)
                    ->line('• End: ' . $toDate)
                    ->line('• Pet Owner: ' . $ownerName)
                    ->line('Please remember to:')
                    ->line('• Arrive on time for pickup')
                    ->line('• Follow all care instructions')
                    ->line('• Contact the owner if you have any questions')
                    ->action('View Booking', route('frontend.bookings.index'));
                break;

            case 'rejected':
                $message->line('Your booking request for ' . $petName . ' has been rejected.')
                    ->line('📅 Requested Booking Details:')
                    ->line('• Start: ' . $fromDate)
                    ->line('• End: ' . $toDate)
                    ->line('Don\'t worry! You can:')
                    ->line('• Try booking another pet')
                    ->line('• Request a different time slot')
                    ->line('• Browse other available pets')
                    ->action('Find Another Pet', route('frontend.home'));
                break;

            case 'completed':
                $message->line('Your booking for ' . $petName . ' has been marked as completed.')
                    ->line('📅 Booking Details:')
                    ->line('• Start: ' . $fromDate)
                    ->line('• End: ' . $toDate)
                    ->line('• Pet Owner: ' . $ownerName)
                    ->line('We hope you had a great experience!')
                    ->line('Please consider:')
                    ->line('• Leaving a review for the pet owner')
                    ->line('• Booking again in the future')
                    ->action('View Booking History', route('frontend.bookings.index'));
                break;

            default:
                $message->line('The status of your booking for ' . $petName . ' has been updated to: ' . $this->status)
                    ->action('View Booking', route('frontend.bookings.index'));
        }

        return $message;
    }

    public function toTwilio($notifiable)
    {
        $petName = $this->booking->pet ? $this->booking->pet->name : 'Pet';
        $ownerName = $this->booking->pet ? $this->booking->pet->user->name : 'Pet Owner';
        $requesterName = $this->booking->user ? $this->booking->user->name : 'Requester';
        $fromDate = $this->booking->from . ' ' . $this->booking->from_time;
        $toDate = $this->booking->to . ' ' . $this->booking->to_time;

        switch ($this->status) {
            case 'new':
                return "PetFriends: New booking request for {$petName} from {$requesterName}. Dates: {$fromDate} to {$toDate}. View at: " . route('frontend.requests.index');

            case 'accepted':
                return "PetFriends: Your booking for {$petName} has been accepted! Dates: {$fromDate} to {$toDate}. Owner: {$ownerName}. View at: " . route('frontend.bookings.index');

            case 'rejected':
                return "PetFriends: Your booking request for {$petName} has been declined. Dates: {$fromDate} to {$toDate}. Find another pet at: " . route('frontend.home');

            case 'completed':
                return "PetFriends: Your booking with {$petName} has been completed! Dates: {$fromDate} to {$toDate}. Thank you for using PetFriends!";
        }
    }
} 