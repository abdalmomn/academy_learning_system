<!DOCTYPE html>
<html>
<head>
    <style>
        body { text-align: center; font-family: DejaVu Sans, sans-serif; }
        .certificate { border: 5px solid #000; padding: 50px; }
        h1 { font-size: 40px; }
        h2 { font-size: 30px; }
        p { font-size: 20px; }
    </style>
</head>
<body>
<div class="certificate">
    <h1>Certificate of Completion</h1>
    <p>This is to certify that</p>
    <h2>{{ $username }}</h2>
    <p>has successfully completed the course</p>
    <h2>{{ $course_name }}</h2>
    <p>on {{ $date }}</p>
</div>
</body>
</html>
