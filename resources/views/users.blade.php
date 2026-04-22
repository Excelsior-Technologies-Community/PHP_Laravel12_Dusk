<!DOCTYPE html>
<html>
<head>
    <title>Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-5xl mx-auto mt-10">

    <h2 class="text-xl font-bold mb-4">Users</h2>

    <!-- 🔍 Live Search -->
    <input type="text" id="search"
        value="{{ request('search') }}"
        class="border p-2 w-full rounded mb-4"
        placeholder="Search users...">

    <!-- Loader -->
    <div id="loader" class="hidden text-center text-gray-500 mb-2">
        Searching...
    </div>

    <!-- 🔁 Dynamic Content -->
    <div id="userData">

        <div class="bg-white shadow rounded">
            <table class="w-full">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-2 text-left">Name</th>
                        <th class="p-2 text-left">Email</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($users as $user)
                    <tr class="border-t">
                        <td class="p-2">{{ $user->name }}</td>
                        <td class="p-2">{{ $user->email }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $users->appends(['search' => request('search')])->links() }}
        </div>

    </div>

</div>

<!-- ⚡ LIVE SEARCH + PAGINATION -->
<script>

let timer;

// 🔍 Live Search
document.getElementById('search').addEventListener('keyup', function() {

    clearTimeout(timer);

    let query = this.value;
    let loader = document.getElementById('loader');

    loader.classList.remove('hidden');

    timer = setTimeout(() => {
        fetch(`/users?search=${query}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.text())
        .then(data => {
            document.getElementById('userData').innerHTML = data;
            loader.classList.add('hidden');
        });
    }, 400);

});


// 📄 Pagination click (AJAX)
document.addEventListener('click', function(e) {

    if (e.target.closest('.pagination a')) {
        e.preventDefault();

        let url = e.target.closest('a').getAttribute('href');
        let loader = document.getElementById('loader');

        loader.classList.remove('hidden');

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.text())
        .then(data => {
            document.getElementById('userData').innerHTML = data;
            loader.classList.add('hidden');
        });
    }

});

</script>

</body>
</html>