<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - 360 Studio Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS v4 -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    <style type="text/tailwindcss">
        @theme {
            --font-outfit: 'Outfit', sans-serif;
            --color-admin-bg: #0f172a;
            --color-accent: #3b82f6;
        }

        body {
            font-family: var(--font-outfit);
        }

        .glass-card {
            @apply bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl;
        }

        .input-field {
            @apply w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all;
        }

        .btn-primary {
            @apply w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-500/20 transition-all transform hover:scale-[1.02] active:scale-[0.98];
        }
    </style>
</head>
<body class="bg-admin-bg min-h-screen flex items-center justify-center p-4 antialiased">

    <!-- Background Decoration -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-blue-600/20 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-indigo-600/20 rounded-full blur-[120px]"></div>
    </div>

    <div class="w-full max-w-md relative z-10 animate-fade-in">
        <!-- Logo -->
        <div class="flex flex-col items-center mb-8">
            <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center shadow-xl shadow-blue-500/40 mb-4 transform -rotate-6">
                <i class="fas fa-cube text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-black text-white tracking-tighter uppercase">360<span class="text-slate-500">STUDIO</span></h1>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mt-2">Administrative Gateway</p>
        </div>

        <div class="glass-card p-8">
            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-2 ml-1">Email Address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required class="input-field pl-11" placeholder="admin@360studio.com">
                    </div>
                    @error('email')
                        <p class="text-rose-500 text-[10px] font-bold mt-2 ml-1 uppercase tracking-wider">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-2 ml-1">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                        <input type="password" name="password" required class="input-field pl-11" placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="remember" class="hidden peer">
                        <div class="w-4 h-4 rounded border border-white/10 flex items-center justify-center peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all">
                            <i class="fas fa-check text-white text-[8px] opacity-0 peer-checked:opacity-100"></i>
                        </div>
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-widest group-hover:text-slate-300 transition-colors">Remember Me</span>
                    </label>
                </div>

                <button type="submit" class="btn-primary uppercase tracking-widest text-[11px]">
                    Authenticate
                </button>
            </form>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ url('/') }}" class="text-slate-500 hover:text-slate-300 text-[10px] font-bold uppercase tracking-widest transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left"></i> Back to Public Tour
            </a>
        </div>
    </div>

    <style>
        .animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</body>
</html>
