@extends('layouts.frontend')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Notification Preferences</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('frontend.notifications.preferences.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <h5 class="mb-3">Pet Notifications</h5>
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="pet_available" name="pet_available" value="1"
                                    {{ $preferences->pet_available ? 'checked' : '' }}>
                                <label class="form-check-label" for="pet_available">
                                    Notify me when a pet I've cared for becomes available
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="mb-3">Booking Notifications</h5>
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="booking_requested" name="booking_requested" value="1"
                                    {{ $preferences->booking_requested ? 'checked' : '' }}>
                                <label class="form-check-label" for="booking_requested">
                                    Notify me when someone requests to book my pet
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="booking_accepted" name="booking_accepted" value="1"
                                    {{ $preferences->booking_accepted ? 'checked' : '' }}>
                                <label class="form-check-label" for="booking_accepted">
                                    Notify me when my booking request is accepted
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="booking_rejected" name="booking_rejected" value="1"
                                    {{ $preferences->booking_rejected ? 'checked' : '' }}>
                                <label class="form-check-label" for="booking_rejected">
                                    Notify me when my booking request is rejected
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="booking_completed" name="booking_completed" value="1"
                                    {{ $preferences->booking_completed ? 'checked' : '' }}>
                                <label class="form-check-label" for="booking_completed">
                                    Notify me when a booking is completed
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="mb-3">Message Notifications</h5>
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="new_message" name="new_message" value="1"
                                    {{ $preferences->new_message ? 'checked' : '' }}>
                                <label class="form-check-label" for="new_message">
                                    Notify me when I receive a new message
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="mb-3">Email Notifications</h5>
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="email_notifications" name="email_notifications" value="1"
                                    {{ $preferences->email_notifications ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_notifications">
                                    Send me email notifications for the above events
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                Save Preferences
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 