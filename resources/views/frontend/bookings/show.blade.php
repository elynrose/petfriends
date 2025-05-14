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
                    <div class="chat-container" style="height: 600px; overflow-y: auto;" data-booking-id="{{ $booking->id }}">
                        <div class="chat-messages p-3" style="height: 450px; overflow-y: auto;">
                            <!-- Messages will be loaded here -->
                        </div>
                        <div class="chat-form p-3 border-top">
                            <form id="chat-form" class="d-flex">
                                <input type="text" class="form-control me-2" id="message" placeholder="Type your message..." required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
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
        display: flex;
        flex-direction: column;
        background-color: #f0f2f5;
        height: 600px;
        position: relative;
    }
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        padding-bottom: 5rem;
    }
    .message {
        margin-bottom: 1.5rem;
        max-width: 70%;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        position: relative;
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
        overflow: hidden;
        flex-shrink: 0;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .message-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .message-avatar svg {
        width: 100%;
        height: 100%;
        fill: #6c757d;
        background-color: #e9ecef;
    }
    .message-content-wrapper {
        display: flex;
        flex-direction: column;
        max-width: calc(100% - 60px);
    }
    .message-sender {
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
        padding: 0 0.5rem;
    }
    .message.sent .message-sender {
        text-align: right;
        color: #1a73e8;
    }
    .message.received .message-sender {
        color: #34a853;
    }
    .message-content {
        padding: 0.75rem 1rem;
        border-radius: 1.25rem;
        display: inline-block;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        position: relative;
        word-wrap: break-word;
    }
    .message.sent .message-content {
        background-color: #1a73e8;
        color: white;
        border-top-right-radius: 0.25rem;
        position: relative;
    }
    .message.sent .message-content::after {
        content: '';
        position: absolute;
        right: -8px;
        top: 0;
        width: 0;
        height: 0;
        border: 8px solid transparent;
        border-left-color: #1a73e8;
        border-right: 0;
        border-top: 0;
        margin-top: 0;
    }
    .message.received .message-content {
        background-color: #34a853;
        color: white;
        border-top-left-radius: 0.25rem;
        position: relative;
    }
    .message.received .message-content::after {
        content: '';
        position: absolute;
        left: -8px;
        top: 0;
        width: 0;
        height: 0;
        border: 8px solid transparent;
        border-right-color: #34a853;
        border-left: 0;
        border-top: 0;
        margin-top: 0;
    }
    .message-time {
        font-size: 0.7rem;
        color: #6c757d;
        margin-top: 0.25rem;
        padding: 0 0.5rem;
    }
    .message.sent .message-time {
        text-align: right;
    }
    .chat-form {
        background-color: #fff;
        border-top: 1px solid #dee2e6;
        padding: 1rem;
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 10;
    }
    .chat-form .form-control {
        border-radius: 1.5rem;
        padding: 0.75rem 1.25rem;
        border: 1px solid #dee2e6;
    }
    .chat-form .form-control:focus {
        box-shadow: none;
        border-color: #1a73e8;
    }
    .chat-form .btn {
        border-radius: 50%;
        width: 40px;
        height: 40px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #1a73e8;
        border: none;
    }
    .chat-form .btn:hover {
        background-color: #1557b0;
    }
    .chat-form .btn i {
        font-size: 1rem;
    }
</style>
@endpush

@push('scripts')
<script>
console.log('Chat script loaded');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    const chatContainer = document.querySelector('.chat-container');
    const chatMessages = chatContainer.querySelector('.chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message');
    const bookingId = chatContainer.dataset.bookingId;

    console.log('Chat elements found:', {
        chatContainer: !!chatContainer,
        chatMessages: !!chatMessages,
        chatForm: !!chatForm,
        messageInput: !!messageInput,
        bookingId: bookingId
    });

    // Load messages
    function loadMessages() {
        console.log('Loading messages for booking:', bookingId);
        fetch(`/frontend/bookings/${bookingId}/messages`)
            .then(response => {
                console.log('Load messages response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Loaded messages:', data);
                chatMessages.innerHTML = '';
                if (data.length === 0) {
                    chatMessages.innerHTML = '<div class="text-center text-muted py-4">No messages yet. Start the conversation!</div>';
                } else {
                    data.forEach(message => {
                        appendMessage(message);
                    });
                }
                scrollToBottom();
            })
            .catch(error => {
                console.error('Error loading messages:', error);
                chatMessages.innerHTML = '<div class="text-center text-danger py-4">Error loading messages. Please try again.</div>';
            });
    }

    // Append message to chat
    function appendMessage(message) {
        console.log('Appending message:', message);
        const isSent = message.from_id === {{ auth()->id() }};
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
        
        // Format the date properly
        let messageTime;
        try {
            const date = new Date(message.created_at);
            messageTime = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        } catch (e) {
            console.error('Error formatting date:', e);
            messageTime = 'Just now';
        }

        // Default avatar SVG with a different color for each user
        const defaultAvatar = `<svg width="50" height="50" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
        </svg>`;

        // Handle both real-time messages and loaded messages
        const fromName = message.from_name || (isSent ? '{{ auth()->user()->name }}' : 'Other User');
        const fromPhoto = message.from_photo || null;

        let avatarHtml;
        if (fromPhoto) {
            avatarHtml = `<div class="message-avatar">
                <img src="${fromPhoto}" alt="${fromName}" onerror="this.parentElement.innerHTML = document.getElementById('default-avatar').innerHTML;">
            </div>`;
        } else {
            avatarHtml = `<div class="message-avatar" id="default-avatar">
                ${defaultAvatar}
            </div>`;
        }

        messageDiv.innerHTML = `
            ${avatarHtml}
            <div class="message-content-wrapper">
                <div class="message-sender">${fromName}</div>
                <div class="message-content">${message.message}</div>
                <div class="message-time">${messageTime}</div>
            </div>
        `;
        chatMessages.appendChild(messageDiv);
    }

    // Scroll to bottom of chat
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Send message
    if (chatForm) {
        console.log('Adding submit event listener to chat form');
        chatForm.addEventListener('submit', function(e) {
            console.log('Form submitted');
            e.preventDefault();
            const message = messageInput.value.trim();
            if (!message) {
                console.log('Empty message, not sending');
                return;
            }

            console.log('Sending message:', message);
            console.log('CSRF Token:', '{{ csrf_token() }}');
            console.log('Booking ID:', bookingId);

            fetch(`/frontend/bookings/${bookingId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message })
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(text);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Message sent successfully:', data);
                messageInput.value = '';
                scrollToBottom();
            })
            .catch(error => {
                console.error('Error sending message:', error);
                alert('Failed to send message. Please try again.');
            });
        });
    } else {
        console.error('Chat form not found!');
    }

    // Listen for new messages
    Echo.private(`booking.${bookingId}`)
        .listen('NewChatMessage', (e) => {
            console.log('New message received:', e);
            // The event data is now directly the message object
            appendMessage(e);
            scrollToBottom();
        });

    // Mark messages as read
    function markMessagesAsRead() {
        fetch(`/frontend/bookings/${bookingId}/messages/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    }

    // Load initial messages
    loadMessages();
    markMessagesAsRead();

    // Mark messages as read when window is focused
    window.addEventListener('focus', markMessagesAsRead);
});
</script>
@endpush
@endsection