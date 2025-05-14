<div class="chat-container" data-booking-id="{{ $booking->id }}">
    <div class="chat-messages" id="chat-messages">
        <!-- Messages will be loaded here -->
    </div>
    
    <form id="chat-form" class="chat-form">
        <div class="input-group">
            <input type="text" id="message-input" class="form-control" placeholder="Type your message..." required>
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary">Send</button>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
.chat-container {
    height: 400px;
    display: flex;
    flex-direction: column;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
}

.message {
    margin-bottom: 1rem;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    max-width: 80%;
}

.message.sent {
    background-color: #007bff;
    color: white;
    margin-left: auto;
}

.message.received {
    background-color: #f8f9fa;
    margin-right: auto;
}

.chat-form {
    padding: 1rem;
    border-top: 1px solid #ddd;
    background-color: white;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatContainer = document.querySelector('.chat-container');
    const messagesContainer = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const bookingId = chatContainer.dataset.bookingId;

    // Load messages
    function loadMessages() {
        fetch(`/frontend/bookings/${bookingId}/messages`)
            .then(response => response.json())
            .then(messages => {
                messagesContainer.innerHTML = '';
                if (messages.length === 0) {
                    messagesContainer.innerHTML = '<div class="text-center text-muted py-4">No messages yet. Start the conversation!</div>';
                } else {
                    messages.forEach(message => {
                        appendMessage(message);
                    });
                }
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            })
            .catch(error => {
                console.error('Error loading messages:', error);
                messagesContainer.innerHTML = '<div class="text-center text-danger py-4">Error loading messages. Please try again.</div>';
            });
    }

    // Append a message to the chat
    function appendMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${message.from_id === {{ auth()->id() }} ? 'sent' : 'received'}`;
        messageDiv.innerHTML = `
            <div class="message-content">${message.message}</div>
            <small class="message-time">${new Date(message.created_at).toLocaleTimeString()}</small>
        `;
        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Send message
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = messageInput.value.trim();
        if (!message) return;

        fetch(`/frontend/bookings/${bookingId}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            messageInput.value = '';
            appendMessage(data);
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Failed to send message. Please try again.');
        });
    });

    // Listen for new messages
    Echo.private(`booking.${bookingId}`)
        .listen('NewChatMessage', (e) => {
            appendMessage(e);
            markMessagesAsRead();
        });

    // Mark messages as read
    function markMessagesAsRead() {
        fetch(`/frontend/bookings/${bookingId}/messages/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
    }

    // Initial load
    loadMessages();
    markMessagesAsRead();
});
</script>
@endpush 