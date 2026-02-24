<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'Admin Control Center') - GuidedTours</title>
    
    <!-- Scripts & Styles -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <!-- AlpineJS for interactions -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#1a73e8", // Enterprise Blue
                        "primary-dark": "#1557b0",
                        "background-light": "#f3f4f6", // Cool gray background for light mode
                        "background-dark": "#0f172a", // Slate 900 for dark mode
                        "surface-light": "#ffffff",
                        "surface-dark": "#1e293b", // Slate 800 for cards
                        "border-light": "#e5e7eb",
                        "border-dark": "#334155",
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                        body: ["Inter", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                    },
                },
            },
        };
    </script>
    @stack('styles')
</head>
<body class="bg-background-light dark:bg-background-dark text-gray-800 dark:text-gray-100 font-body antialiased min-h-screen flex flex-col" x-data="{ sidebarOpen: false, darkMode: localStorage.getItem('theme') === 'dark' }" :class="{ 'dark': darkMode }">

    <!-- Header -->
    <header class="bg-surface-light dark:bg-surface-dark border-b border-border-light dark:border-border-dark sticky top-0 z-30 h-16 flex items-center justify-between px-6 shadow-sm">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 dark:text-gray-400 hover:text-primary transition-colors">
                <span class="material-icons">menu</span>
            </button>
            <a href="{{ route('home') }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                <span class="material-icons text-primary text-3xl">public</span>
                <span class="font-bold text-xl tracking-tight text-gray-900 dark:text-white">Guided Tours <span class="text-xs font-normal bg-primary/10 text-primary px-2 py-0.5 rounded-full ml-1 border border-primary/20">Admin</span></span>
            </a>
            
            <!-- Dark Mode Toggle -->
            <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" class="p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-full transition-colors" title="Toggle dark mode">
                <span class="material-icons text-xl" x-text="darkMode ? 'light_mode' : 'dark_mode'"></span>
            </button>
        </div>
        <div class="flex items-center gap-6">
            <!-- Search (Hidden on mobile) -->
            <div class="hidden md:flex relative group">
                <span class="absolute left-3 top-2.5 text-gray-400 dark:text-gray-500 material-icons text-lg">search</span>
                <input class="pl-10 pr-4 py-2 w-64 bg-gray-50 dark:bg-slate-900/50 border border-gray-200 dark:border-border-dark rounded-full text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder-gray-400 dark:placeholder-gray-500 text-gray-700 dark:text-gray-200" placeholder="Search system..." type="text"/>
            </div>

            <div class="flex items-center gap-4">
                <button class="relative p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-full transition-colors">
                    <span class="material-icons">notifications</span>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border-2 border-surface-light dark:border-surface-dark"></span>
                </button>

                <div class="h-8 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>
                
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-3 hover:bg-gray-50 dark:hover:bg-slate-800 py-1 px-2 rounded-lg transition-colors">
                        <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-semibold text-sm">
                            {{ substr(Auth::user()->first_name ?? 'A', 0, 1) }}
                        </div>
                        <div class="hidden md:flex flex-col items-start">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200 leading-tight">{{ Auth::user()->first_name ?? 'Admin' }} {{ Auth::user()->last_name ?? '' }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 leading-tight">Super Admin</span>
                        </div>
                        <span class="material-icons text-gray-400 text-lg hidden md:block">expand_more</span>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="open" x-transition.origin.top.right class="absolute right-0 mt-2 w-48 bg-white dark:bg-surface-dark rounded-md shadow-lg py-1 border border-border-light dark:border-border-dark z-50" style="display: none;">
                        <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-slate-700">Sign Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" class="fixed lg:static inset-y-0 left-0 z-20 w-64 bg-surface-light dark:bg-surface-dark border-r border-border-light dark:border-border-dark flex flex-col transition-transform duration-300 ease-in-out h-[calc(100vh-64px)] lg:h-auto overflow-y-auto">
            <nav class="flex-1 px-3 py-6 space-y-1">
                <div class="pb-4">
                    <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Overview</h3>
                    <a class="bg-primary/10 text-primary group flex items-center px-3 py-2 text-sm font-medium rounded-md border-r-4 border-primary dark:border-primary" href="{{ route('admin.configurator') }}">
                        <span class="material-icons mr-3 text-xl">dashboard</span>
                        Dashboard
                    </a>
                </div>
                <div class="pb-4 border-t border-gray-100 dark:border-gray-800 pt-4">
                    <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Management</h3>
                    <a class="text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700/50 group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors" href="#">
                        <span class="material-icons mr-3 text-xl text-gray-400 group-hover:text-gray-500">place</span>
                        Places
                    </a>
                    <a class="text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700/50 group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors" href="#">
                        <span class="material-icons mr-3 text-xl text-gray-400 group-hover:text-gray-500">category</span>
                        Visit Types
                    </a>
                    <a class="text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700/50 group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors" href="#">
                        <span class="material-icons mr-3 text-xl text-gray-400 group-hover:text-gray-500">people</span>
                        Users & Roles
                    </a>
                    <a class="text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700/50 group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors" href="#">
                        <span class="material-icons mr-3 text-xl text-gray-400 group-hover:text-gray-500">event</span>
                        Planning
                    </a>
                </div>
            </nav>
            <div class="p-4 border-t border-border-light dark:border-border-dark">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <span class="material-icons">logout</span>
                        Sign Out
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-slate-900/20 p-6 lg:p-8">
            <div class="max-w-7xl mx-auto space-y-8">
                @yield('content')
            </div>
            
            <footer class="mt-12 border-t border-gray-200 dark:border-gray-800 pt-6 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500 dark:text-gray-400">
                <div class="mb-2 md:mb-0">
                    © {{ date('Y') }} Guided Tours Org. All rights reserved.
                </div>
                <div class="flex gap-6">
                    <a class="hover:text-primary transition-colors" href="#">Privacy Policy</a>
                    <a class="hover:text-primary transition-colors" href="#">Terms of Service</a>
                </div>
            </footer>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
