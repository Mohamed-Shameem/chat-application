<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 20px 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
            font-size: 24px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            box-sizing: border-box;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            margin-bottom: 20px;
            font-size: 12px;
            display: none;
        }

        .error {
            margin-top: -10px;
            color: red;
        }
    </style>
</head>

<div class="container">
    <h2>Register</h2>
    <form id="registerForm" method="POST">
        <div>
            <label for="name">Name</label>
            <input id="name" type="text" name="name">
        </div>

        <div>
            <label for="email">Email</label>
            <input id="email" type="email" name="email">
        </div>

        <div>
            <label for="password">Password</label>
            <input id="password" type="password" name="password">
        </div>

        <div>
            <label for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation">
        </div>

        <div>
            <button type="submit">Register</button>
        </div>
        <div id="error-message" class="error-message"></div>
    </form>
</div>
<script>
    $(document).ready(function() {
        var loginForm = $("#registerForm").submit(function(e) {
            e.preventDefault();
        }).validate({
            rules: {
                name: {
                    required: true,
                    normalizer: function(value) {
                        return $.trim(value);
                    }
                },
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true,
                    minlength: 8
                },
                password_confirmation: {
                    required: true,
                    equalTo: "#password"
                }
            },
            messages: {
                name: {
                    required: "Please enter your name"
                },
                email: {
                    required: "Please enter your email",
                    email: "Please enter a valid email address"
                },
                password: {
                    required: "Please enter your password",
                    minlength: "Your password must be at least 8 characters long"
                },
                password_confirmation: {
                    required: "Please confirm your password",
                    equalTo: "Passwords do not match"
                }
            },
            submitHandler: function(form) {
                var data = $(form).serialize();
                $.ajax({
                    url: "{{ route('register') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Redirect to the home page
                            window.location.href = response.redirect_url;
                        } else {
                            // Display general error message
                            $('#error-message').text(response.message).show();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#error-message').append('<p>' + value[0] +
                                    '</p>').show();
                            });
                        } else {
                            // Display other error messages
                            $('#error-message').text(
                                'An error occurred. Please try again.').show();
                        }
                    }
                });
            }
        });
    });
</script>
</body>

</html>
