<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('logo.svg') }}">
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="min-h-screen flex">

        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md">
            <div class="flex flex-col justify-between h-full">
                <div>
                    <div class="p-6 font-bold text-xl text-brand">
                        Resume.
                        <div class="text-xs text-gray-500 font-normal">Admin Panel</div>
                    </div>

                    <nav class="px-3 space-y-1 border-0">

                        {{-- Dashboard --}}
                        <a href="{{ route('admin.dashboard') }}"
                            class="group flex items-center gap-3 px-3 py-3 rounded-md transition
       {{ request()->routeIs('admin.dashboard')
           ? 'bg-brand/10 text-brand font-semibold'
           : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <x-lucide-layout-dashboard
                                class="w-5 h-5 transition
            {{ request()->routeIs('admin.dashboard') ? 'text-brand' : 'text-slate-400 group-hover:text-slate-600' }}" />
                            <span>Dashboard</span>
                        </a>


                        {{-- Analytics --}}
                        <a href="{{ route('admin.analytics.index') }}"
                            class="group flex items-center gap-3 px-3 py-3 rounded-md transition
       {{ request()->routeIs('admin.analytics.*')
           ? 'bg-brand/10 text-brand font-semibold'
           : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <x-lucide-bar-chart-2
                                class="w-5 h-5 transition
            {{ request()->routeIs('admin.analytics.*') ? 'text-brand' : 'text-slate-400 group-hover:text-slate-600' }}" />
                            <span>Analytics</span>
                        </a>


                        {{-- Users --}}
                        <a href="{{ route('admin.users.index') }}"
                            class="group flex items-center gap-3 px-3 py-3 rounded-md transition
       {{ request()->routeIs('admin.users.*')
           ? 'bg-brand/10 text-brand font-semibold'
           : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <x-lucide-users
                                class="w-5 h-5 transition
            {{ request()->routeIs('admin.users.*') ? 'text-brand' : 'text-slate-400 group-hover:text-slate-600' }}" />
                            <span>Users</span>
                        </a>

                        {{-- Resumes --}}
                        <a href="{{ route('admin.resumes.index') }}"
                            class="group flex items-center gap-3 px-3 py-3 rounded-md transition
       {{ request()->routeIs('admin.resumes.*')
           ? 'bg-brand/10 text-brand font-semibold'
           : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <x-lucide-file-text
                                class="w-5 h-5 transition
            {{ request()->routeIs('admin.resumes.*') ? 'text-brand' : 'text-slate-400 group-hover:text-slate-600' }}" />
                            <span>Resumes</span>
                        </a>

                        {{-- Templates --}}
                        <a href="{{ route('admin.templates.index') }}"
                            class="group flex items-center gap-3 px-3 py-3 rounded-md transition
       {{ request()->routeIs('admin.templates.*')
           ? 'bg-brand/10 text-brand font-semibold'
           : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <x-lucide-layout-template
                                class="w-5 h-5 transition
            {{ request()->routeIs('admin.templates.*') ? 'text-brand' : 'text-slate-400 group-hover:text-slate-600' }}" />
                            <span>Templates</span>
                        </a>


                        {{-- Audit Logs --}}
                        <a href="{{ route('admin.audit-logs.index') }}"
                            class="group flex items-center gap-3 px-3 py-3 rounded-md transition
       {{ request()->routeIs('admin.audit-logs.*')
           ? 'bg-brand/10 text-brand font-semibold'
           : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                            <x-lucide-clipboard-list
                                class="w-5 h-5 transition
            {{ request()->routeIs('admin.audit-logs.*') ? 'text-brand' : 'text-slate-400 group-hover:text-slate-600' }}" />
                            <span>Audit Logs</span>
                        </a>

                        {{-- Settings --}}
                        {{-- <a href="{{ route('admin.resumes.index') }}"
                            class="group flex items-center gap-3 px-3 py-3 rounded-md transition text-slate-700 hover:bg-slate-100 hover:text-slate-900">
                            <x-lucide-settings class="w-5 h-5 text-slate-400 group-hover:text-slate-600 transition" />
                            <span>Settings</span>
                        </a> --}}

                    </nav>


                </div>

                <div class="px-6">
                    <div class="border-t border-brand/10 py-6 flex flex-col gap-4">
                        <!-- Profile -->
                        <div class="flex items-center gap-3">
                            <!-- Avatar -->
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-brand text-white md:h-11 md:w-11">
                                <span class="text-lg font-bold">
                                    {{ strtoupper(substr(auth()->user()->email, 0, 1)) }}
                                </span>
                            </div>

                            <!-- Name + Role -->
                            <div class="leading-tight">
                                <div class="text-sm font-semibold text-gray-800">
                                    {{ auth()->user()->fullName ?? 'Admin' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Admin
                                </div>
                            </div>
                        </div>

                        <!-- Logout -->
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button
                                class="px-4 py-2.5 bg-brand/10 hover:bg-brand/15 text-brand text-sm rounded-lg flex flex-row gap-2 items-center">
                                <x-lucide-log-out class="w-4 h-4" />
                                <span>Logout</span>
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </aside>

        <!-- Main -->
        <main class="flex-1">
            <!-- Topbar -->
            <header class="bg-white shadow-sm px-6 py-4 flex gap-3 justify-between items-center">
                <div class="font-semibold text-brand">@yield('pageTitle', 'Dashboard')</div>

                <div class="flex gap-4 items-center">
                    {{-- <div class="text-sm text-gray-600">
                        {{ auth()->user()->email }}
                    </div> --}}

                    <div class="flex gap-4 items-center">
                        <form action="" method="POST">
                            @csrf
                            <a href="{{ route('admin.reports.export') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-brand text-white text-sm font-semibold hover:bg-brand/90 transition">
                                <x-lucide-download class="w-4 h-4" />
                                Export Report
                            </a>

                        </form>

                    </div>

                    {{-- <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                            Logout
                        </button>
                    </form> --}}
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
