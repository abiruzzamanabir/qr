<!-- resources/views/qr/show.blade.php -->

<h2>User: {{ $user->name }}</h2>
<p>Email: {{ $user->email }}</p>
<p>Phone: {{ $user->phone }}</p>

<div>
    <h3>QR Code:</h3>
    <img src="{{ $qrCode }}" alt="QR Code" width="300" />
</div>

<p>Scanned: {{ $user->scan_count }} times</p>
