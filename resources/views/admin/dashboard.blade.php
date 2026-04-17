@extends('components.admin-layout')

@section('header', 'Workspace Summary')

@section('content')
<div class="p-6 space-y-8 animate-fade-in">
    <div class="border-b border-slate-200 pb-4">
        <h1 class="text-xl font-black text-slate-900 tracking-tighter uppercase">Workspace Dashboard</h1>
        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Operational Summary</p>
    </div>

    <!-- Main Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between group hover:border-blue-500 transition-all cursor-default overflow-hidden relative">
            <div class="z-10 text-slate-800">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-[2px] mb-1">Total Environments</div>
                <div class="text-3xl font-black tracking-tighter">{{ $totalScenes }}</div>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="absolute -bottom-2 -right-2 text-slate-50 text-6xl opacity-5 group-hover:text-blue-500 group-hover:opacity-10 transition-all">
                <i class="fas fa-layer-group"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between group hover:border-emerald-500 transition-all cursor-default overflow-hidden relative">
            <div class="z-10 text-slate-800">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-[2px] mb-1">Active Interaction Nodes</div>
                <div class="text-3xl font-black tracking-tighter">{{ $totalHotspots }}</div>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform">
                <i class="fas fa-bullseye"></i>
            </div>
            <div class="absolute -bottom-2 -right-2 text-slate-50 text-6xl opacity-5 group-hover:text-emerald-500 group-hover:opacity-10 transition-all">
                <i class="fas fa-bullseye"></i>
            </div>
        </div>

        <div class="bg-slate-900 p-6 rounded-lg shadow-xl flex items-center justify-between group cursor-default overflow-hidden relative">
            <div class="z-10">
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-[2px] mb-1 text-white/50">Project Health</div>
                <div class="text-3xl font-black tracking-tighter text-white uppercase">Optimized</div>
            </div>
            <div class="w-12 h-12 bg-white/10 text-white rounded-lg flex items-center justify-center text-xl shadow-inner group-hover:scale-110 transition-transform">
                <i class="fas fa-shield-alt"></i>
            </div>
        </div>
    </div>

    <!-- Project Overview Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Newest Assets -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-[10px] font-bold text-slate-800 uppercase tracking-widest">Recent Buffers</h3>
                <a href="{{ route('admin.scenes.index') }}" class="text-[9px] font-bold text-blue-600 hover:underline uppercase">View All</a>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($recentScenes as $scene)
                <div class="px-6 py-4 flex items-center gap-4 hover:bg-slate-50/50 transition-colors">
                    <img src="{{ Storage::url($scene->image_path) }}" class="w-12 h-12 rounded object-cover shadow-sm border border-slate-200" alt="Buffer">
                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] font-bold text-slate-900 truncate tracking-tight">{{ $scene->name }}</p>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">{{ $scene->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center text-slate-400">
                    <p class="text-[10px] uppercase font-bold tracking-widest">No environments detected</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6 space-y-6">
            <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Core Actions</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('admin.scenes.create') }}" class="p-4 bg-blue-600 rounded-lg text-white flex flex-col items-center justify-center gap-3 hover:bg-blue-700 transition-all group shadow-lg shadow-blue-500/10">
                    <i class="fas fa-plus text-lg group-hover:scale-110 transition-transform"></i>
                    <span class="text-[9px] font-bold uppercase tracking-widest">New Scene</span>
                </a>
                <a href="{{ url('/') }}" target="_blank" class="p-4 bg-slate-900 rounded-lg text-white flex flex-col items-center justify-center gap-3 hover:bg-black transition-all group shadow-lg shadow-slate-900/10">
                    <i class="fas fa-play text-lg group-hover:scale-110 transition-transform text-blue-500"></i>
                    <span class="text-[9px] font-bold uppercase tracking-widest">Run Project</span>
                </a>
            </div>
            
            <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-lg">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-widest">Deployment Status</span>
                </div>
                <p class="text-[9px] text-emerald-600 font-medium leading-relaxed">System is operational. All nodes initialized and visually balanced.</p>
            </div>
        </div>
    </div>
</div>
@endsection
