<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen p-4">

<div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">

    <div class="flex items-center justify-between mb-8">
        <a href="/dashboard" class="text-gray-400 hover:text-blue-600 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h2 class="text-2xl font-black text-gray-800">My Profile</h2>
        <div class="w-6"></div>
    </div>

    <form method="POST" action="/profile" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="flex flex-col items-center">
            <div class="relative group">
                <img id="avatarPreview" 
                     src="{{ auth()->user()->avatar_url }}" 
                     class="w-32 h-32 rounded-full object-cover border-4 border-blue-500 shadow-lg transition duration-300 group-hover:brightness-90">
                
                <label for="avatarInput" class="absolute bottom-0 right-0 bg-blue-600 p-2.5 rounded-full cursor-pointer hover:bg-blue-700 transition shadow-xl border-2 border-white">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </label>
            </div>
            <input type="file" name="avatar" id="avatarInput" class="hidden" accept="image/*">
            @error('avatar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Full Name</label>
                <input type="text" name="name" value="{{ auth()->user()->name }}"
                    class="w-full border border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-gray-50" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Email Address</label>
                <input type="email" name="email" value="{{ auth()->user()->email }}"
                    class="w-full border border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-gray-50" required>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-2xl space-y-4 border border-gray-100">
            <h3 class="font-bold text-gray-700 text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Security Update
            </h3>
            
            <input type="password" name="old_password"
                class="w-full border border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition bg-white text-sm" placeholder="Current Password">

            <input type="password" name="new_password"
                class="w-full border border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition bg-white text-sm" placeholder="New Password">
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl shadow-blue-200 shadow-lg transform transition active:scale-95">
            Save Changes
        </button>
    </form>
</div>

<script>
    document.getElementById('avatarInput').onchange = evt => {
        const [file] = document.getElementById('avatarInput').files;
        if (file) {
            document.getElementById('avatarPreview').src = URL.createObjectURL(file);
        }
    }

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Updated!',
            text: "{{ session('success') }}",
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    @endif

    @if(session('error') || $errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: "{{ session('error') ?? $errors->first() }}",
            confirmButtonColor: '#3b82f6'
        });
    @endif
</script>

</body>
</html>