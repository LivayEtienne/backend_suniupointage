<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Photo</title>
</head>
<body>
    <h1>Upload User Photo</h1>

    <!-- Messages -->
    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <ul style="color: red;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ url('/users/1/photo') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label for="photo">Choose a photo:</label>
    <input type="file" id="photo" name="photo" required>
    <button type="submit">Upload Photo</button>
</form>

</body>
</html>
