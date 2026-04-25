@extends('components.admin-layout')

@section('header', 'Edit Node')

@section('content')
<div class="p-6 flex items-center justify-center min-h-full">
    <div class="max-w-2xl w-full animate-fade-in">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200">
            <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-slate-800 text-white rounded flex items-center justify-center text-lg">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-widest leading-none">Calibrate Workspace</h3>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1.5">Adjusting established environment</p>
                    </div>
                </div>
                <div class="bg-white px-3 py-1 border border-slate-200 rounded text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                    ID • #{{ $scene->id }}
                </div>
            </div>
            
            <div class="p-8">
                <form action="{{ route('admin.scenes.update', $scene) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-[3px] ml-1">Workspace Identity</label>
                        <input type="text" name="name" value="{{ old('name', $scene->name) }}" required class="modern-input h-14" placeholder="Environment name...">
                        @error('name') <p class="text-rose-500 text-[8px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[9px] font-bold text-slate-500 uppercase tracking-widest ml-1">Deskripsi (Indonesian)</label>
                            <textarea name="description_id" rows="3" class="modern-input text-xs font-normal" placeholder="Ceritakan tentang ruangan ini...">{{ old('description_id', $scene->description_id) }}</textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-bold text-slate-500 uppercase tracking-widest ml-1">Description (English)</label>
                            <textarea name="description_en" rows="3" class="modern-input text-xs font-normal" placeholder="Tell the story of this room...">{{ old('description_en', $scene->description_en) }}</textarea>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-[9px] font-bold text-slate-500 uppercase tracking-widest ml-0.5">Current Visual Feed</label>
                        <div class="relative group aspect-video rounded-lg overflow-hidden border border-slate-200 bg-slate-100">
                            <img src="{{ Storage::url($scene->high_res_path) }}" class="w-full h-full object-cover group-hover:opacity-40 transition-opacity" alt="Current">
                            <div class="absolute inset-0 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-slate-900/40">
                                <i class="fas fa-sync text-white text-xl mb-2"></i>
                                <span class="text-white text-[9px] font-bold uppercase tracking-widest">Replace Visual Buffer</span>
                            </div>
                            <input type="file" name="image" accept="image/jpeg,image/png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="this.closest('.group').querySelector('.text-white').innerText = 'NEW BUFFER READY'">
                        </div>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest text-center">{{ basename($scene->high_res_path) }}</p>
                        @error('image') <p class="text-rose-500 text-[8px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                    </div>


                    
                    <hr class="border-slate-100">

                    <div class="flex flex-col sm:flex-row items-center gap-4 pt-2">
                        <button type="submit" class="w-full sm:w-auto px-10 py-3 bg-slate-900 text-white font-bold rounded text-[9px] uppercase tracking-widest shadow-sm hover:bg-blue-600 transition-all">
                            Synchronize Changes
                        </button>
                        <a href="{{ route('admin.scenes.index') }}" class="text-[9px] font-bold text-slate-400 hover:text-slate-900 uppercase tracking-widest transition-colors">
                            Discard Edits
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="upload-overlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[9999] flex flex-col items-center justify-center text-white hidden">
    <div class="relative w-24 h-24 mb-6">
        <div class="absolute inset-0 border-4 border-blue-500/20 rounded-full"></div>
        <div class="absolute inset-0 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
    </div>
    <h3 class="text-xs font-black uppercase tracking-[5px] text-white mb-2 ml-1">Calibrating Node</h3>
    <p class="text-[10px] text-blue-200/80 font-bold uppercase tracking-[2px] animate-pulse">Re-Generating Multi-Resolution Variants...</p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('form');
        const overlay = document.getElementById('upload-overlay');
        const fileInput = document.querySelector('input[type="file"]');
        
        if (form && overlay) {
            form.addEventListener('submit', () => {
                // Only show overlay if a new file is actually being uploaded
                if (fileInput && fileInput.files.length > 0) {
                    overlay.classList.remove('hidden');
                }
            });
        }
    });
</script>

<style type="text/tailwindcss">
    @layer utilities {
        .modern-input {
            @apply w-full bg-white border border-slate-200 rounded px-3 py-2 text-xs font-semibold text-slate-800 transition-all outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100;
        }
    }
</style>
@endsection
