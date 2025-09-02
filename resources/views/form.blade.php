<form method="POST" action="{{route('generate')}}">
    @csrf
    <input type="text" name="name" placeholder="Name" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="text" name="phone" placeholder="Phone" required><br>
    <button type="submit">Generate QR</button>
</form>
