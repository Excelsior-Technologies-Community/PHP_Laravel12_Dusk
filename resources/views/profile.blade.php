<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">

<div class="bg-white p-8 rounded shadow w-full max-w-md">

    <h2 class="text-xl font-bold mb-4 text-center">Profile</h2>

    @if(session('success'))
        <p class="bg-green-100 text-green-700 p-2 mb-3">{{ session('success') }}</p>
    @endif

    @if(session('error'))
        <p class="bg-red-100 text-red-700 p-2 mb-3">{{ session('error') }}</p>
    @endif

    <form method="POST" action="/profile" class="space-y-4">
        @csrf

        <input type="text" name="name" value="{{ auth()->user()->name }}"
            class="w-full border p-2 rounded" placeholder="Name">

        <input type="email" name="email" value="{{ auth()->user()->email }}"
            class="w-full border p-2 rounded" placeholder="Email">

        <h3 class="font-semibold">Change Password</h3>

        <input type="password" name="old_password"
            class="w-full border p-2 rounded" placeholder="Old Password">

        <input type="password" name="new_password"
            class="w-full border p-2 rounded" placeholder="New Password">

        <button class="w-full bg-blue-500 text-white py-2 rounded">
            Update Profile
        </button>
    </form>

</div>

</body>
</html>