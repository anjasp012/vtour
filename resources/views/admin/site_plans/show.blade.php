@extends('components.admin-layout')

@section('header', 'Map Editor')

@section('content')
<div class="h-full flex flex-col lg:flex-row overflow-hidden bg-slate-50">
    <!-- Main Editing Canvas -->
    <div class="flex-1 relative overflow-auto p-8 flex items-center justify-center min-h-0">
        <div class="relative inline-block shadow-2xl border-4 border-white rounded-lg bg-white group" id="map-container">
            <img src="{{ Storage::url($sitePlan->image_path) }}" class="max-w-full max-h-[75vh] w-auto h-auto block select-none pointer-events-none rounded-sm" id="site-plan-image">
            
            <!-- Hotspots Layer -->
            <div id="hotspots-layer" class="absolute inset-0 cursor-crosshair"></div>

            <!-- Canvas Info Badge -->
            <div class="absolute -top-12 left-0 flex items-center gap-3">
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-slate-200">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block leading-none mb-1">Canvas Resolution</span>
                    <span class="text-xs font-black text-slate-900" id="res-display">Loading...</span>
                </div>
            </div>
        </div>

        <!-- Float Help -->
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-6 py-2.5 rounded-full shadow-2xl flex items-center gap-4 border border-white/10">
            <div class="flex items-center gap-2">
                <kbd class="px-1.5 py-0.5 bg-white/10 rounded text-[9px] font-bold">CLICK</kbd>
                <span class="text-[9px] font-bold uppercase tracking-widest text-white/60">To Place Node</span>
            </div>
            <div class="w-px h-3 bg-white/10"></div>
            <div class="flex items-center gap-2">
                <kbd class="px-1.5 py-0.5 bg-white/10 rounded text-[9px] font-bold">DRAG</kbd>
                <span class="text-[9px] font-bold uppercase tracking-widest text-white/60">To Move Node</span>
            </div>
        </div>
    </div>

    <!-- Inspector Sidebar -->
    <aside class="w-full lg:w-[380px] bg-white border-l border-slate-200 flex flex-col shadow-2xl shrink-0 z-10">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-tighter">Plan Inspector</h3>
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">{{ $sitePlan->name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Live Editor</span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-8 scrollbar-thin">
            <!-- Node List -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Active Hotspots</span>
                    <span class="bg-blue-600 text-white text-[9px] font-bold px-2.5 py-0.5 rounded-full" id="hotspot-count">0</span>
                </div>

                <div id="hotspot-list" class="space-y-2">
                    <!-- Dynamic Items -->
                </div>

                <div id="empty-state" class="py-12 text-center bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl hidden">
                    <i class="fas fa-bullseye text-slate-200 text-3xl mb-3"></i>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest px-8">No hotspots detected. Click on the map to initialize a new routing node.</p>
                </div>
            </div>
        </div>

        <!-- Action Footer -->
        <div class="p-6 bg-slate-50 border-t border-slate-100">
            <button id="save-btn" class="w-full bg-blue-600 text-white font-black py-4 rounded-xl text-xs uppercase tracking-[2px] shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center justify-center gap-3 group">
                <i class="fas fa-save group-hover:scale-110 transition-transform"></i>
                Synchronize Changes
            </button>
            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest text-center mt-4">Unsaved changes will be lost on refresh.</p>
        </div>
    </aside>
</div>

<!-- Hotspot Prototype (Hidden) -->
<template id="hotspot-template">
    <div class="absolute group hotspot-marker -translate-x-1/2 -translate-y-1/2" style="left: 0%; top: 0%;">
        <!-- Marker Visual -->
        <div class="relative">
            <div class="w-8 h-8 bg-blue-600 rounded-full border-4 border-white shadow-xl flex items-center justify-center text-white text-[10px] cursor-grab active:cursor-grabbing hover:scale-110 transition-transform">
                <i class="fas fa-location-arrow"></i>
            </div>
            <!-- Tooltip -->
            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 bg-slate-900 text-white px-3 py-1.5 rounded-lg text-[9px] font-bold uppercase tracking-widest whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none shadow-2xl">
                <span class="target-name">Select Scene</span>
                <div class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-slate-900"></div>
            </div>
        </div>
    </div>
</template>

<!-- Scene Selector Template (Hidden) -->
<template id="hotspot-item-template">
    <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-sm hover:border-blue-500 transition-all group/item">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400 group-hover/item:bg-blue-50 group-hover/item:text-blue-600 transition-colors">
                <i class="fas fa-link"></i>
            </div>
            <div class="flex-1">
                <select class="w-full bg-transparent border-none p-0 text-xs font-bold text-slate-900 focus:ring-0 cursor-pointer scene-select">
                    <option value="">Select Destination...</option>
                    @foreach($scenes as $scene)
                        <option value="{{ $scene->id }}">{{ $scene->name }}</option>
                    @endforeach
                </select>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest pos-display">X: 0% Y: 0%</span>
                </div>
            </div>
            <button class="w-8 h-8 flex items-center justify-center text-slate-300 hover:text-rose-500 transition-colors remove-hotspot">
                <i class="fas fa-trash-alt text-[10px]"></i>
            </button>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mapContainer = document.getElementById('map-container');
    const hotspotsLayer = document.getElementById('hotspots-layer');
    const hotspotList = document.getElementById('hotspot-list');
    const emptyState = document.getElementById('empty-state');
    const hotspotCount = document.getElementById('hotspot-count');
    const resDisplay = document.getElementById('res-display');
    const saveBtn = document.getElementById('save-btn');
    const img = document.getElementById('site-plan-image');

    let hotspots = {!! json_encode($sitePlan->hotspots->map(function($h) {
        return [
            'id' => $h->id,
            'scene_id' => $h->scene_id,
            'x' => (float)$h->x,
            'y' => (float)$h->y
        ];
    })) !!};

    // Initialize resolution display
    img.onload = () => {
        resDisplay.textContent = `${img.naturalWidth} x ${img.naturalHeight} PX`;
    };
    if (img.complete) resDisplay.textContent = `${img.naturalWidth} x ${img.naturalHeight} PX`;

    function updateUI() {
        hotspotCount.textContent = hotspots.length;
        if (hotspots.length === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    }

    function renderHotspots() {
        hotspotsLayer.innerHTML = '';
        hotspotList.innerHTML = '';

        hotspots.forEach((hs, index) => {
            // Render Marker on Map
            const markerTpl = document.getElementById('hotspot-template').content.cloneNode(true);
            const markerDiv = markerTpl.querySelector('.hotspot-marker');
            markerDiv.style.left = hs.x + '%';
            markerDiv.style.top = hs.y + '%';
            markerDiv.dataset.index = index;

            const sceneNameSpan = markerDiv.querySelector('.target-name');
            const selectedScene = {!! json_encode($scenes) !!}.find(s => s.id == hs.scene_id);
            sceneNameSpan.textContent = selectedScene ? selectedScene.name : 'Select Scene';

            // Dragging Logic
            markerDiv.addEventListener('mousedown', startDragging);

            hotspotsLayer.appendChild(markerDiv);

            // Render Item in Sidebar
            const itemTpl = document.getElementById('hotspot-item-template').content.cloneNode(true);
            const itemDiv = itemTpl.querySelector('div');
            const select = itemDiv.querySelector('.scene-select');
            const posDisp = itemDiv.querySelector('.pos-display');
            const removeBtn = itemDiv.querySelector('.remove-hotspot');

            select.value = hs.scene_id || '';
            posDisp.textContent = `X: ${hs.x.toFixed(1)}% Y: ${hs.y.toFixed(1)}%`;

            select.onchange = (e) => {
                hotspots[index].scene_id = e.target.value;
                const newScene = {!! json_encode($scenes) !!}.find(s => s.id == e.target.value);
                sceneNameSpan.textContent = newScene ? newScene.name : 'Select Scene';
            };

            removeBtn.onclick = () => {
                hotspots.splice(index, 1);
                renderHotspots();
                updateUI();
            };

            hotspotList.appendChild(itemDiv);
        });
        updateUI();
    }

    function startDragging(e) {
        e.preventDefault();
        const marker = e.currentTarget;
        const index = marker.dataset.index;
        const rect = mapContainer.getBoundingClientRect();

        function onMouseMove(moveEvent) {
            let x = ((moveEvent.clientX - rect.left) / rect.width) * 100;
            let y = ((moveEvent.clientY - rect.top) / rect.height) * 100;

            // Constrain
            x = Math.max(0, Math.min(100, x));
            y = Math.max(0, Math.min(100, y));

            hotspots[index].x = x;
            hotspots[index].y = y;
            
            marker.style.left = x + '%';
            marker.style.top = y + '%';
            
            // Update sidebar display without full re-render
            const item = hotspotList.children[index];
            if (item) {
                item.querySelector('.pos-display').textContent = `X: ${x.toFixed(1)}% Y: ${y.toFixed(1)}%`;
            }
        }

        function onMouseUp() {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        }

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    }

    hotspotsLayer.onclick = (e) => {
        if (e.target !== hotspotsLayer) return;

        const rect = hotspotsLayer.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width) * 100;
        const y = ((e.clientY - rect.top) / rect.height) * 100;

        hotspots.push({
            scene_id: null,
            x: x,
            y: y
        });

        renderHotspots();
    };

    saveBtn.onclick = async () => {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        try {
            const response = await fetch('{{ route('admin.site-plans.hotspots.save', $sitePlan) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ hotspots: hotspots })
            });

            const result = await response.json();
            if (result.success) {
                alert('Changes synchronized successfully!');
            } else {
                alert('Error: ' + result.message);
            }
        } catch (err) {
            console.error(err);
            alert('A critical error occurred while synchronizing.');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save"></i> Synchronize Changes';
        }
    };

    renderHotspots();
});
</script>

<style>
    .scrollbar-thin::-webkit-scrollbar { width: 4px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
@endsection
