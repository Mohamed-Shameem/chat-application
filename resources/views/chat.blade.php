<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Application</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/7.0.3/pusher.min.js"></script>
    <style>
        .message {
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 10px;
            max-width: 60%;
        }

        .message-left {
            background-color: #f1f0f0;
            margin-right: auto;
        }

        .message-right {
            background-color: #007bff;
            color: white;
            margin-left: auto;
        }

        .message-container {
            display: flex;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span>Chat Application</span>
                        <button id="logout-button" class="btn btn-light">Logout</button>
                    </div>
                    <div class="card-body" id="chat-window" style="height: 400px; overflow-y: scroll;">
                        <div id="messages"></div>
                    </div>
                    <div class="card-footer">
                        <div class="input-group">
                            <input type="text" id="message-input" class="form-control"
                                placeholder="Type your message here..." autofocus>
                            <div class="input-group-append">
                                <button id="send-button" class="btn btn-primary">Send</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ mix('js/app.js') }}"></script>
    <script>
        const userName = '{{ Auth::user()->name }}'; // Pass authenticated user's name to JavaScript
        const csrfToken = '{{ csrf_token() }}'; // Pass CSRF token to JavaScript

        $(document).ready(function() {
            const $messageInput = $('#message-input');
            const $sendButton = $('#send-button');
            const $messagesDiv = $('#messages');
            const $logoutButton = $('#logout-button');

            // Fetch existing messages
            $.get('/messages', function(response) {
                response.forEach(function(message) {
                    const messageElement = $('<div class="message-container"></div>');
                    const messageContent = $('<div class="message"></div>').text(
                        `${message.user}: ${message.message}`);
                    if (message.user === userName) {
                        messageContent.addClass('message-right');
                    } else {
                        messageContent.addClass('message-left');
                    }
                    messageElement.append(messageContent);
                    $messagesDiv.append(messageElement);
                });
                $messagesDiv.scrollTop($messagesDiv[0].scrollHeight);
            });

            // Set up Pusher
            Pusher.logToConsole = true;

            const pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
                cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
                forceTLS: true
            });

            const channel = pusher.subscribe('chat-application');
            channel.bind('App\\Events\\MessageSent', function(data) {
                const messageElement = $('<div class="message-container"></div>');
                const messageContent = $('<div class="message"></div>').text(
                    `${data.user}: ${data.message}`);
                if (data.user === userName) {
                    messageContent.addClass('message-right');
                } else {
                    messageContent.addClass('message-left');
                }
                messageElement.append(messageContent);
                $messagesDiv.append(messageElement);
                $messagesDiv.scrollTop($messagesDiv[0].scrollHeight); // Auto-scroll to the latest message
            });

            // Send a message
            $sendButton.on('click', function() {
                const message = $messageInput.val();
                $.post('/messages', {
                    user: userName, // Use the actual user's name
                    message: message,
                    _token: csrfToken // Include CSRF token
                }).done(function() {
                    $messageInput.val('');
                });
            });

            // Handle logout
            $logoutButton.on('click', function() {
                $.ajax({
                    url: "{{ route('logout') }}",
                    type: 'POST',
                    data: {
                        _token: csrfToken // Include CSRF token
                    },
                    success: function() {
                        window.location.href = '/login';
                    },
                    error: function(xhr, status, error) {
                        console.error('Logout failed:', error);
                    }
                });
            });
        });
    </script>
</body>

</html>
