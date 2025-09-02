<!DOCTYPE html>
<html>

<head>
    <title>User QR Code</title>
</head>

<body>
    <h2>User: {{ $user->name }}</h2>
    <p>Email: {{ $user->email }}</p>
    <p>Phone: {{ $user->phone }}</p>

    <div>
        <h3>QR Code:</h3>
        <img src="{{ $qrCode }}" width="300" alt="QR Code">
    </div>

    <p><strong>Scanned:</strong> {{ $user->scan_count }} times</p>

    <form method="POST" action="/api/reset">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">
        <button type="submit">Reset Scan Count</button>
    </form>
</body>

</html>
