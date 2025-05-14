@extends('layouts.frontend')
@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Chat</h4>
                    <a href="{{ route('frontend.bookings.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Bookings
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="chat-container" data-booking-id="{{ $booking->id }}">
                        <div class="chat-messages" id="chat-messages">
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
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .chat-container {
        max-width: 800px;
        margin: 0 auto;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .chat-messages {
        height: 500px;
        overflow-y: auto;
        padding: 20px;
        background: #f8f9fa;
    }

    .message {
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
    }

    .message.sent {
        align-items: flex-end;
    }

    .message.received {
        align-items: flex-start;
    }

    .message-content {
        max-width: 70%;
        background: #fff;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .message.sent .message-content {
        background: #007bff;
        color: #fff;
    }

    .message-header {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }

    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 10px;
        object-fit: cover;
    }

    .user-name {
        font-weight: bold;
        margin-right: 10px;
    }

    .message-time {
        font-size: 0.8em;
        color: #666;
    }

    .message.sent .message-time {
        color: #fff;
    }

    .message-text {
        word-wrap: break-word;
    }

    .chat-input {
        padding: 20px;
        background: #fff;
        border-top: 1px solid #eee;
    }

    .chat-form {
        display: flex;
        gap: 10px;
    }

    .message-input {
        flex: 1;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        resize: none;
    }

    .send-button {
        padding: 10px 20px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .send-button:hover {
        background: #0056b3;
    }

    .send-button:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    .loading-spinner {
        text-align: center;
        padding: 20px;
        color: #666;
    }

    .error-message {
        text-align: center;
        padding: 20px;
        color: #dc3545;
        background: #f8d7da;
        border-radius: 5px;
        margin: 10px 0;
    }

    .no-messages {
        text-align: center;
        padding: 20px;
        color: #666;
        font-style: italic;
    }
</style>
@endpush

@push('scripts')
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
            console.log('Fetching messages for booking:', bookingId);
            const response = await fetch(`/frontend/bookings/${bookingId}/messages`);
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const messages = await response.json();
            console.log('Received messages:', messages);
            
            // Clear loading spinner
            chatMessages.innerHTML = '';
            
            if (!Array.isArray(messages)) {
                throw new Error('Invalid response format: expected array of messages');
            }
            
            if (messages.length === 0) {
                chatMessages.innerHTML = '<div class="no-messages">No messages yet. Start the conversation!</div>';
            } else {
                messages.forEach(appendMessage);
            }
            scrollToBottom();
        } catch (error) {
            console.error('Error loading messages:', error);
            chatMessages.innerHTML = `
                <div class="error-message">
                    Error loading messages: ${error.message}<br>
                    Please try refreshing the page.
                </div>
            `;
        } finally {
            isLoading = false;
        }
    }

    // Append message to chat
    function appendMessage(message) {
        const isCurrentUser = message.from_id === {{ Auth::id() }};
        const userPhoto = message.from_photo ? message.from_photo : '{{ asset('images/user-placeholder.svg') }}';
        const messageHtml = `
            <div class="message ${isCurrentUser ? 'sent' : 'received'}" data-message-id="${message.id}">
                <div class="message-content">
                    <div class="message-header">
                        <img src="${userPhoto}" alt="${message.from_name}" class="user-avatar" width="50" height="50" onerror="this.onerror=null; this.src='{{ asset('images/user-placeholder.svg') }}';">
                        <span class="user-name">${message.from_name}</span>
                        <span class="message-time">${new Date(message.created_at).toLocaleTimeString()}</span>
                    </div>
                    <div class="message-text">${message.message}</div>
                </div>
            </div>
        `;
        chatMessages.insertAdjacentHTML('beforeend', messageHtml);
    }

    // Scroll to bottom of chat
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Send message
    if (chatForm) {
        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const message = messageInput.value.trim();
            if (!message) return;

            const submitButton = chatForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            try {
                const response = await fetch(`/frontend/bookings/${bookingId}/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message })
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.error || 'Failed to send message');
                }

                if (data.success) {
                    messageInput.value = '';
                    scrollToBottom();
                } else {
                    throw new Error(data.error || 'Failed to send message');
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Failed to send message: ' + error.message);
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
            }
        });
    }

    // Listen for new messages
    Echo.private(`booking.${bookingId}`)
        .listen('NewChatMessage', (e) => {
            console.log('Received new message event:', e);
            const existingMessage = document.querySelector(`[data-message-id="${e.id}"]`);
            if (!existingMessage) {
                appendMessage(e);
                scrollToBottom();
                markMessagesAsRead();
            }
        });

    // Mark messages as read
    async function markMessagesAsRead() {
        try {
            const response = await fetch(`/frontend/bookings/${bookingId}/messages/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Failed to mark messages as read');
            }
        } catch (error) {
            console.error('Error marking messages as read:', error);
        }
    }

    // Initial load
    loadMessages();
    markMessagesAsRead();

    // Mark messages as read when window is focused
    window.addEventListener('focus', markMessagesAsRead);
});
</script>
@endpush
@endsection