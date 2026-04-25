@extends('components.admin-layout')

@section('header', 'Workspaces')

@php
    $totalScenes = $tour->scenes->count();
    $totalHotspots = $tour->scenes->sum(fn($s) => $s->infospots->count());
@endphp

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    <!-- Collection Management Header -->
    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tighter uppercase">Scene Collections</h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total Detected Environments: {{ $tour->scenes->count() }}</p>
        </div>
        <a href="{{ route('admin.scenes.create') }}" class="px-5 py-2.5 bg-blue-600 text-white text-[9px] font-bold rounded shadow-lg shadow-blue-500/10 hover:bg-blue-700 transition-all flex items-center gap-2 uppercase tracking-widest">
            <i class="fas fa-plus"></i> New Visual Buffer
        </a>
    </div>

    <!-- Ultra-Dense Scene Grid -->
    <div id="scenes-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 gap-4">
        @forelse($tour->scenes as $scene)
            <div dir="ltr" data-id="{{ $scene->id }}" class="bg-white border border-slate-200 rounded overflow-hidden flex flex-col shadow-sm hover:shadow transition-all group relative">
                <!-- Drag Handle -->
                <div class="drag-handle absolute top-1.5 right-1.5 z-20 w-6 h-6 bg-black/40 backdrop-blur-sm rounded flex items-center justify-center text-white/60 cursor-move hover:text-white hover:bg-black/60 transition-all opacity-0 group-hover:opacity-100">
                    <i class="fas fa-grip-vertical text-[10px]"></i>
                </div>
                <!-- Compact Thumbnail -->
                <div class="relative aspect-video overflow-hidden border-b border-slate-100">
                    <img src="{{ Storage::url($scene->low_res_path ?? $scene->high_res_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $scene->name }}">
                    
                    @if($scene->is_start_scene)
                        <div class="absolute top-1.5 left-1.5 bg-blue-600 text-white text-[7px] font-bold px-1.5 py-0.5 rounded-sm shadow uppercase tracking-widest">
                            INIT
                        </div>
                    @endif

                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 p-2 pt-4">
                        <span class="text-white font-bold text-[11px] tracking-tight truncate block">{{ $scene->name }}</span>
                    </div>
                </div>

                <!-- Functional Actions -->
                <div class="p-2 space-y-2">
                    <a href="{{ route('admin.scenes.show', $scene) }}" class="w-full border border-slate-200 bg-white text-slate-600 text-center py-1.5 rounded text-[9px] font-bold tracking-widest uppercase hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all block">
                        Editor
                    </a>
                    
                    <div class="flex items-center justify-between text-[8px] font-bold uppercase tracking-widest px-1">
                        <a href="{{ route('admin.scenes.edit', $scene) }}" class="text-slate-400 hover:text-blue-600 transition-colors">
                            Config
                        </a>
                        
                        <form action="{{ route('admin.scenes.destroy', $scene) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus?');">
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
                <i class="fas fa-folder-open text-3xl opacity-20"></i>
                <p class="text-xs font-bold uppercase tracking-widest">No scenes initialized yet.</p>
                <a href="{{ route('admin.scenes.create') }}" class="text-blue-600 font-bold hover:underline text-xs">Init project workspace &rarr;</a>
            </div>
        @endforelse
    </div>
</div>
</div>

<!-- Sortable.js -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const el = document.getElementById('scenes-grid');
    if (!el) return;

    Sortable.create(el, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'opacity-50',
        onEnd: function() {
            const order = [];
            el.querySelectorAll('[data-id]').forEach(item => {
                order.push(item.dataset.id);
            });

            // Send to server
            fetch('{{ route('admin.scenes.reorder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Optional: show a small toast or toast-like indicator
                    console.log('Order updated');
                }
            })
            .catch(err => {
                console.error('Failed to update order', err);
                alert('Gagal mengupdate urutan scene.');
            });
        }
    });
});
</script>
@endsection
