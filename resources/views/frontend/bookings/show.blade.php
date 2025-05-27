@extends('layouts.frontend')
@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Booking Details</h5>
                    <a href="{{ route('frontend.bookings.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Bookings
                    </a>
                </div>
                <div class="card-body">
                    @if(auth()->user()->canUseChat())
                        <!-- Debug info -->
                       
                        <div class="chat-container" data-booking-id="{{ $booking->id }}">
                            <div class="chat-messages" id="chat-messages" style="overflow-y:scroll; max-height: 400px;">
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                            <div class="chat-form">
                                <form id="chat-form">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="message-input" placeholder="Type your message..." required>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <h5 class="alert-heading"><i class="fas fa-crown"></i> Premium Feature</h5>
                            <p>Chat is a premium feature. Upgrade to Premium to start chatting with other pet owners!</p>
                            <hr>
                            <p class="mb-0">
                                <a href="{{ route('frontend.subscription.index') }}" class="btn btn-primary">
                                    <i class="fas fa-crown"></i> Upgrade to Premium
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->canUseChat())
    @push('styles')
    <style>
        .chat-container {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .chat-messages {
            padding: 1.5rem;
            background-color: #f8f9fa;
            min-height: 400px;
        }
        .chat-form {
            padding: 1rem;
            background-color: #fff;
            border-top: 1px solid #dee2e6;
        }
        .message {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            max-width: 85%;
        }
        .message.sent {
            margin-left: auto;
            flex-direction: row-reverse;
        }
        .message.received {
            margin-right: auto;
        }
        .message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin: 0 10px;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .message-content {
            background-color: #fff;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            position: relative;
        }
        .message.sent .message-content {
            background-color: #007bff;
            color: #fff;
        }
        .message.received .message-content {
            background-color: #e9ecef;
        }
        .message-time {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.25rem;
            display: block;
        }
        .message.sent .message-time {
            color: rgba(255,255,255,0.8);
            text-align: right;
            font-size: 10px;
            font-weight: bold;
            margin-left: 10px;
            margin-top: 5px;
            margin-bottom: 5px;
            margin-right: 10px;
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #ffffff;
            background-color: #007bff;
            padding: 2px 5px;
            border-radius: 5px;
        }
        .chat-form .input-group {
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-radius: 2rem;
            overflow: hidden;
        }
        .chat-form .form-control {
            border: none;
            padding: 0.75rem 1.25rem;
        }
        .chat-form .btn {
            border-radius: 0 2rem 2rem 0;
            padding: 0.75rem 1.5rem;
        }
        .chat-form .btn i {
            font-size: 1.1rem;
        }
    </style>
    @endpush
    

    @yield('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatContainer = document.querySelector('.chat-container');
        const chatMessages = document.getElementById('chat-messages');
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const bookingId = chatContainer.dataset.bookingId;
        let isLoading = false;

        // Load messages
        async function loadMessages() {
            if (isLoading) return;
            isLoading = true;
            
            try {
                const response = await fetch(`/frontend/bookings/${bookingId}/messages`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
                }
                
                const messages = await response.json();
                
                // Clear loading spinner
                chatMessages.innerHTML = '';
                
                if (!Array.isArray(messages)) {
                    throw new Error('Invalid response format: expected array of messages');
                }
                
                if (messages.length === 0) {
                    chatMessages.innerHTML = '<div class="text-center text-muted py-4">No messages yet. Start the conversation!</div>';
                } else {
                    messages.forEach(appendMessage);
                }
                scrollToBottom();
            } catch (error) {
                chatMessages.innerHTML = `
                    <div class="alert alert-danger m-3">
                        <i class="fas fa-exclamation-circle"></i> Error loading messages: ${error.message}<br>
                        Please try refreshing the page.
                    </div>
                `;
            } finally {
                isLoading = false;
            }
        }

        // Append message to chat
        function appendMessage(message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${message.from_id === {{ auth()->id() }} ? 'sent' : 'received'}`;
            messageDiv.innerHTML = `
                <img src="${message.from_photo || '/images/default-avatar.png'}" alt="Avatar" class="message-avatar">
                <div class="message-content">
                    <div class="message-text">${message.message}</div>
                    <small class="message-time">${new Date(message.created_at).toLocaleTimeString()}</small>
                </div>
            `;
            chatMessages.appendChild(messageDiv);
        }

        // Scroll to bottom of chat
        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Send message
        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            if (!message) return;
            
            try {
                const response = await fetch(`/frontend/bookings/${bookingId}/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message })
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                if (data.success) {
                    messageInput.value = '';
                    appendMessage(data.message);
                    scrollToBottom();
                }
            } catch (error) {
                alert('Error sending message: ' + error.message);
            }
        });

        // Load initial messages
        loadMessages();

        // Listen for new messages
        Echo.private(`booking.${bookingId}`)
            .listen('NewChatMessage', (e) => {
                appendMessage(e.message);
                scrollToBottom();
            });

        // Mark messages as read when window is focused
        window.addEventListener('focus', async function() {
            try {
                await fetch(`/frontend/bookings/${bookingId}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
            } catch (error) {
                // Silently fail - marking as read is not critical
            }
        });
    });
    </script>
   
@endif
@endsection