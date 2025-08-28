<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email</title>
</head>
<body>
<h2>Welcome, {{ $user->username }}</h2>
<p>keep this code to verify your payments in the futures:</p>
<p style="background-color: #3490dc; color: cornsilk">
    {{$code}}</p>
</body>
</html>
