<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<!-- Navbar -->
<div class="bg-white shadow p-4 flex justify-between">
    <h2 class="font-bold text-lg">Dashboard</h2>

    <div class="flex gap-4 items-center">
        <a href="/profile" class="text-blue-500">Profile</a>
        <a href="/users" class="text-green-500">Users</a>

        <form method="POST" action="/logout">
            @csrf
            <button class="bg-red-500 text-white px-3 py-1 rounded">Logout</button>
        </form>
    </div>
</div>

<div class="max-w-5xl mx-auto mt-10">

    <!-- Cards -->
    <div class="grid md:grid-cols-2 gap-6 mb-6">

        <div class="bg-white p-6 rounded shadow text-center">
            <h3 class="text-gray-500">Welcome</h3>
            <p class="text-xl font-bold mt-2">{{ auth()->user()->name }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow text-center">
            <h3 class="text-gray-500">Total Users</h3>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalUsers }}</p>
        </div>

    </div>

    <!-- Latest Users -->
    <div class="bg-white p-6 rounded shadow">
        <h3 class="font-bold mb-4">Latest Users</h3>

        <table class="w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Name</th>
                    <th class="p-2 border">Email</th>
                </tr>
            </thead>

            <tbody>
                @foreach($latestUsers as $user)
                <tr class="text-center">
                    <td class="p-2 border">{{ $user->name }}</td>
                    <td class="p-2 border">{{ $user->email }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

</body>
</html>