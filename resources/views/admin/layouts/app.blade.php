<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="min-h-screen flex">

        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r">
            <div class="p-4 font-bold text-xl">
                Resume Builder
                <div class="text-xs text-gray-500 font-normal">Admin Panel</div>
            </div>

            <nav class="px-3 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-gray-100">
                    Dashboard
                </a>

                <a href="{{ route('admin.users') }}" class="block px-3 py-2 rounded hover:bg-gray-100">
                    Users
                </a>

                <a href="{{ route('admin.resumes') }}" class="block px-3 py-2 rounded hover:bg-gray-100">
                    Resumes
                </a>
            </nav>
        </aside>

        <!-- Main -->
        <main class="flex-1">
            <!-- Topbar -->
            <header class="bg-white border-b px-6 py-4 flex justify-between items-center">
                <div class="font-semibold">@yield('pageTitle', 'Dashboard')</div>

                <div class="flex gap-4 items-center">
                    <div class="text-sm text-gray-600">
                        {{ auth()->user()->email }}
                    </div>

                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <section class="p-6">
                @yield('content')
            </section>
        </main>

    </div>

    @livewireScripts
</body>

</html>
