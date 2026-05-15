<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">

<nav class="bg-white shadow-md p-4 sticky top-0 z-50">
    <div class="max-w-6xl mx-auto flex justify-between items-center">
        <h2 class="font-extrabold text-2xl text-blue-600 tracking-tight">AppDashboard</h2>

        <div class="flex gap-6 items-center">
            <div class="flex items-center gap-2 bg-gray-50 px-3 py-1 rounded-full border">
                <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover border border-blue-200">
                <span class="text-sm font-semibold text-gray-700">{{ auth()->user()->name }}</span>
            </div>

            <div class="flex gap-4">
                <a href="/profile" class="text-gray-600 hover:text-blue-500 font-medium transition">Profile</a>
                <a href="/users" class="text-gray-600 hover:text-green-500 font-medium transition">Users</a>
            </div>

            <form method="POST" action="/logout">
                @csrf
                <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-1.5 rounded-lg text-sm font-bold transition shadow-sm">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<div class="max-w-6xl mx-auto mt-10 px-4">

    <div class="grid md:grid-cols-2 gap-8 mb-10">
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-6">
            <img src="{{ auth()->user()->avatar_url }}" class="w-20 h-20 rounded-full object-cover border-4 border-blue-50 shadow-md">
            <div>
                <h3 class="text-gray-400 text-sm uppercase font-bold tracking-wider">Welcome Back</h3>
                <p class="text-2xl font-black text-gray-800">{{ auth()->user()->name }}</p>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-700 p-8 rounded-2xl shadow-lg text-white flex flex-col justify-center">
            <h3 class="opacity-80 text-sm uppercase font-bold tracking-wider">Total Platform Users</h3>
            <p class="text-5xl font-black mt-1">{{ $totalUsers }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="font-black text-gray-800 text-lg">Latest Registered Users</h3>
            <a href="/users" class="text-blue-500 text-sm font-bold hover:underline">View All</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="p-4 text-left font-bold">User</th>
                        <th class="p-4 text-left font-bold">Email Address</th>
                        <th class="p-4 text-center font-bold">Joined Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($latestUsers as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar_url }}" class="w-10 h-10 rounded-full object-cover shadow-sm">
                                <span class="font-bold text-gray-700">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="p-4 text-gray-600 text-sm">{{ $user->email }}</td>
                        <td class="p-4 text-center text-gray-400 text-xs font-medium">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ session('error') }}",
            confirmButtonColor: '#3b82f6'
        });
    @endif
</script>

</body>
</html>