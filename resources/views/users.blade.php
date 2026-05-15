<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-white shadow-sm p-4 sticky top-0 z-50">
    <div class="max-w-6xl mx-auto flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="/dashboard" class="text-gray-500 hover:text-blue-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-black text-xl text-gray-800">User Directory</h2>
        </div>
        <div class="flex items-center gap-3">
            <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover border border-blue-100">
            <span class="text-sm font-bold text-gray-600">{{ auth()->user()->name }}</span>
        </div>
    </div>
</nav>

<div class="max-w-6xl mx-auto mt-10 px-4 pb-10">

    <div class="relative mb-8">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <input type="text" id="search"
            value="{{ request('search') }}"
            class="block w-full pl-10 pr-3 py-4 border-none rounded-2xl bg-white shadow-sm focus:ring-2 focus:ring-blue-500 outline-none text-gray-700 transition"
            placeholder="Search by name or email address...">
        
        <div id="loader" class="hidden absolute inset-y-0 right-0 pr-4 flex items-center">
            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"></div>
        </div>
    </div>

    <div id="userData" class="space-y-4">
        <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-widest">
                    <tr>
                        <th class="p-4 text-left font-bold">User Information</th>
                        <th class="p-4 text-left font-bold">Email Address</th>
                        <th class="p-4 text-center font-bold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @if($users->count() > 0)
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="p-4">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $user->avatar_url }}" class="w-12 h-12 rounded-full object-cover shadow-sm group-hover:scale-105 transition">
                                    <span class="font-bold text-gray-700 block text-lg">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="p-4 text-gray-600 font-medium">{{ $user->email }}</td>
                            <td class="p-4 text-center">
                                <span class="bg-green-100 text-green-700 text-xs font-black px-3 py-1 rounded-full uppercase">Active</span>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="p-20 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    <p class="text-gray-400 font-bold">No users matching your search</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-8 flex justify-center custom-pagination">
            {{ $users->appends(['search' => request('search')])->links() }}
        </div>
    </div>

</div>

<script>
let timer;

document.getElementById('search').addEventListener('keyup', function() {
    clearTimeout(timer);
    let query = this.value;
    let loader = document.getElementById('loader');
    loader.classList.remove('hidden');

    timer = setTimeout(() => {
        fetch(`/users?search=${query}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(data => {
            document.getElementById('userData').innerHTML = data;
            loader.classList.add('hidden');
        });
    }, 400);
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.pagination a')) {
        e.preventDefault();
        let url = e.target.closest('a').getAttribute('href');
        let loader = document.getElementById('loader');
        loader.classList.remove('hidden');

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(data => {
            document.getElementById('userData').innerHTML = data;
            loader.classList.add('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});
</script>

<style>
    .pagination { display: flex; gap: 0.5rem; }
    .pagination span, .pagination a { 
        padding: 0.75rem 1.25rem; 
        border-radius: 1rem; 
        background: white; 
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        font-weight: bold;
        color: #4b5563;
        transition: all 0.2s;
    }
    .pagination .active span { background: #2563eb; color: white; }
    .pagination a:hover { background: #f3f4f6; color: #2563eb; }
</style>

</body>
</html>