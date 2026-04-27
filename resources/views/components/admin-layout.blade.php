<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>360 Studio Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS v4 -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    <style type="text/tailwindcss">
        @theme {
            --font-outfit: 'Outfit', sans-serif;
            --color-admin-bg: #f8fafc;
            --color-navbar-bg: #1a1a1a;
            --color-accent: #3b82f6;
        }

        body {
            font-family: var(--font-outfit);
        }

        .nav-link {
            @apply flex items-center px-4 h-full text-slate-500 font-bold text-[9px] uppercase tracking-widest transition-all hover:text-white hover:bg-white/5;
        }

        .nav-link.active {
            @apply text-white bg-white/10;
        }

        ::-webkit-scrollbar {
            width: 4px;
        }
        ::-webkit-scrollbar-track {
            @apply bg-slate-100;
        }
        ::-webkit-scrollbar-thumb {
            @apply bg-slate-300 rounded-full hover:bg-slate-400 transition-colors;
        }
    </style>
</head>
<body class="bg-admin-bg flex flex-col h-screen overflow-hidden text-slate-800 antialiased font-medium text-[12px]">

    <!-- Ultra-Slim Navbar -->
    <nav class="bg-navbar-bg h-9 flex items-center justify-between px-4 shrink-0 z-[100] shadow-md border-b border-white/5">
        <div class="flex items-center h-full">
            <!-- Brand -->
            <div class="flex items-center mr-6 pr-6 border-r border-white/10 h-4">
                <i class="fas fa-cube text-blue-500 text-[10px] mr-2"></i>
                <span class="text-white font-black tracking-tighter text-xs uppercase leading-none">360<span class="text-slate-500">STUDIO</span></span>
            </div>

            <!-- Horizontal Menu -->
            <div class="flex items-center h-full">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.site-plans.index') }}" class="nav-link {{ request()->routeIs('admin.site-plans.*') ? 'active' : '' }}">
                    Site Plans
                </a>
                <a href="{{ route('admin.scenes.index') }}" class="nav-link {{ request()->routeIs('admin.scenes.*') && !request()->routeIs('admin.scenes.show') ? 'active' : '' }}">
                    Scene Collections
                </a>
                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    Settings
                </a>
                <a href="{{ route('admin.preview') }}" target="_blank" class="nav-link">
                    Preview
                </a>
            </div>
        </div>

        <div class="flex items-center gap-4 h-full">
            <div class="flex items-center gap-2 pr-4 border-r border-white/10 h-4">
                <div class="w-1 h-1 bg-emerald-500 rounded-full"></div>
                <span class="text-[8px] font-bold text-slate-500 uppercase tracking-widest">Live</span>
            </div>
            
            <div class="flex items-center gap-4 h-full">
                <div class="flex items-center gap-2 cursor-pointer hover:opacity-80 transition-opacity">
                    <div class="w-5 h-5 rounded bg-white/5 flex items-center justify-center text-slate-500 border border-white/10">
                        <i class="fas fa-user text-[8px]"></i>
                    </div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest hidden sm:block">{{ Auth::user()->name }}</span>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="h-full">
                    @csrf
                    <button type="submit" class="nav-link text-rose-500 hover:bg-rose-500/10 hover:text-rose-500 border-l border-white/10">
                        <i class="fas fa-sign-out-alt mr-2"></i> Exit
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Content Shell (No padding, direct flow) -->
    <div class="flex-1 overflow-hidden relative flex flex-col">
        @if(session('success'))
            <div class="m-4 bg-emerald-50 border border-emerald-100 px-4 py-2 rounded flex items-center justify-between animate-fade-in shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-emerald-500 text-[10px]"></i>
                    <p class="text-[9px] font-bold text-emerald-800 uppercase tracking-widest">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-300 hover:text-emerald-500">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
            </div>
        @endif

        <div class="flex-1 overflow-y-auto">
            @yield('content')
        </div>
    </div>

    <style>
        .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</body>
</html>
