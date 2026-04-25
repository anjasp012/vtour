@extends('components.admin-layout')

@section('header', 'Edit Site Plan')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden animate-fade-in">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h1 class="text-xl font-black text-slate-900 tracking-tighter uppercase">Configure Map Data</h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Updating metadata for: {{ $sitePlan->name }}</p>
        </div>

        <form action="{{ route('admin.site-plans.update', $sitePlan) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-0.5">Map Name</label>
                <input type="text" name="name" value="{{ old('name', $sitePlan->name) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 text-sm font-bold text-slate-900 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all outline-none" placeholder="e.g. Ground Floor, Building A...">
                @error('name') <p class="text-rose-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-0.5">Change Plan Image (Optional)</label>
                <div class="mb-4 aspect-video rounded-lg overflow-hidden border border-slate-100 bg-slate-50 relative group">
                    <img src="{{ Storage::url($sitePlan->high_res_path) }}" class="w-full h-full object-contain" id="current-preview">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <span class="text-white text-[8px] font-bold uppercase tracking-widest bg-black/60 px-3 py-1.5 rounded-full">Current Image</span>
                    </div>
                </div>

                <div class="relative group">
                    <input type="file" name="image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div class="border-2 border-dashed border-slate-200 rounded-xl p-8 text-center group-hover:border-blue-400 group-hover:bg-blue-50/50 transition-all">
                        <i class="fas fa-sync-alt text-3xl text-slate-300 group-hover:text-blue-500 mb-3"></i>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-blue-600">Select New Asset to Replace</p>
                    </div>
                </div>
                @error('image') <p class="text-rose-500 text-[10px] font-bold uppercase mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button type="submit" class="flex-1 bg-indigo-600 text-white font-black py-4 rounded-lg text-xs uppercase tracking-[2px] shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 transition-all">
                    Update Site Plan
                </button>
                <a href="{{ route('admin.tours.site-plans.index', $sitePlan->tour) }}" class="px-8 bg-slate-100 text-slate-400 font-bold py-4 rounded-lg text-xs uppercase tracking-[2px] hover:bg-slate-200 hover:text-slate-600 transition-all text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
<div id="upload-overlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[9999] flex flex-col items-center justify-center text-white hidden">
    <div class="relative w-24 h-24 mb-6">
        <div class="absolute inset-0 border-4 border-blue-500/20 rounded-full"></div>
        <div class="absolute inset-0 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
    </div>
    <h3 class="text-xs font-black uppercase tracking-[5px] text-white mb-2 ml-1">Optimizing Map</h3>
    <p class="text-[10px] text-blue-200/80 font-bold uppercase tracking-[2px] animate-pulse">Generating Multi-Resolution Variants...</p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('form');
        const overlay = document.getElementById('upload-overlay');
        const fileInput = document.querySelector('input[type="file"]');
        
        if (form && overlay) {
            form.addEventListener('submit', () => {
                if (fileInput && fileInput.files.length > 0) {
                    overlay.classList.remove('hidden');
                }
            });
        }
    });
</script>
@endsection
