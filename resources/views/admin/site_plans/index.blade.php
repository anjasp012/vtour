@extends('components.admin-layout')

@section('header', 'Site Plans')

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    <!-- Collection Management Header -->
    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tighter uppercase">Site Plans & Maps</h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">2D Navigational Layouts for Tour: {{ $tour->name }}</p>
        </div>
        <a href="{{ route('admin.tours.site-plans.create', $tour) }}" class="px-5 py-2.5 bg-blue-600 text-white text-[9px] font-bold rounded shadow-lg shadow-blue-500/10 hover:bg-blue-700 transition-all flex items-center gap-2 uppercase tracking-widest">
            <i class="fas fa-plus"></i> New Site Plan
        </a>
    </div>

    <!-- Site Plan Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 gap-4">
        @forelse($sitePlans as $plan)
            <div class="bg-white border border-slate-200 rounded overflow-hidden flex flex-col shadow-sm hover:shadow transition-all group">
                <!-- Compact Thumbnail -->
                <div class="relative aspect-[4/3] overflow-hidden border-b border-slate-100 bg-slate-50">
                    <img src="{{ Storage::url($plan->low_res_path ?? $plan->high_res_path) }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500" alt="{{ $plan->name }}">
                    
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 p-2 pt-4">
                        <span class="text-white font-bold text-[11px] tracking-tight truncate block">{{ $plan->name }}</span>
                    </div>
                </div>

                <!-- Functional Actions -->
                <div class="p-2 space-y-2">
                    <a href="{{ route('admin.site-plans.show', $plan) }}" class="w-full border border-slate-200 bg-white text-slate-600 text-center py-1.5 rounded text-[9px] font-bold tracking-widest uppercase hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all block">
                        Hotspot Editor
                    </a>
                    
                    <div class="flex items-center justify-between text-[8px] font-bold uppercase tracking-widest px-1">
                        <a href="{{ route('admin.site-plans.edit', $plan) }}" class="text-slate-400 hover:text-blue-600 transition-colors">
                            Settings
                        </a>
                        
                        <form action="{{ route('admin.site-plans.destroy', $plan) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-rose-500 transition-colors">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 bg-slate-50 border border-dashed border-slate-300 rounded-lg flex flex-col items-center justify-center text-slate-400 space-y-4">
                <i class="fas fa-map text-3xl opacity-20"></i>
                <p class="text-xs font-bold uppercase tracking-widest">No site plans uploaded yet.</p>
                <a href="{{ route('admin.tours.site-plans.create', $tour) }}" class="text-blue-600 font-bold hover:underline text-xs">Upload your first 2D map &rarr;</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
