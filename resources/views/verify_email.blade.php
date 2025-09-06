<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
        }
        .message {
            margin-top: 1rem;
            font-size: 1.2rem;
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
    </style>
</head>
<body>
<div class="card">
    <h2>Email Verification</h2>
    <div class="message {{ $status ? 'success' : 'error' }}">
        {{ $message }}
        <br>
            <p>return to the application</p>
    </div>
</div>
</body>
</html>
