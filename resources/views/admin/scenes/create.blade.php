@extends('components.admin-layout')

@section('header', 'New Scene')

@section('content')
<div class="p-6 flex items-center justify-center min-h-full">
    <div class="max-w-xl w-full animate-fade-in">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200">
            <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-blue-600 text-white rounded flex items-center justify-center text-lg">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-widest leading-none">Initialize Workspace</h3>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1.5">New panorama configuration</p>
                    </div>
                </div>
            </div>
            
            <div class="p-8">
                <form action="{{ route('admin.scenes.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-900 uppercase tracking-[3px] ml-1">Workspace Identity</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="modern-input h-14" placeholder="e.g. Presidential Suite">
                        @error('name') <p class="text-rose-500 text-[8px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[9px] font-bold text-slate-500 uppercase tracking-widest ml-1">Deskripsi (Indonesian)</label>
                            <textarea name="description_id" rows="3" class="modern-input text-xs font-normal" placeholder="Ceritakan tentang ruangan ini...">{{ old('description_id') }}</textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-bold text-slate-500 uppercase tracking-widest ml-1">Description (English)</label>
                            <textarea name="description_en" rows="3" class="modern-input text-xs font-normal" placeholder="Tell the story of this room...">{{ old('description_en') }}</textarea>
                        </div>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-bold text-slate-500 uppercase tracking-widest ml-0.5">Equirectangular Buffer (JPG/PNG)</label>
                        <div class="relative py-8 border border-dashed border-slate-300 rounded bg-slate-50/30 hover:bg-white hover:border-blue-400 transition-all flex flex-col items-center justify-center text-center group cursor-pointer">
                            <i class="fas fa-upload text-slate-300 group-hover:text-blue-500 transition-colors mb-2"></i>
                            <p class="text-[10px] font-bold text-slate-400">Click to select source file</p>
                            <input type="file" name="image" accept="image/jpeg,image/png" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="this.parentElement.querySelector('p').innerText = this.files[0].name">
                        </div>
                        @error('image') <p class="text-rose-500 text-[8px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                    </div>


                    
                    <hr class="border-slate-100">

                    <div class="flex flex-col sm:flex-row items-center gap-4 pt-2">
                        <button type="submit" class="w-full sm:w-auto px-10 py-3 bg-blue-600 text-white font-bold rounded text-[9px] uppercase tracking-widest shadow-sm hover:bg-blue-700 transition-all">
                            Initialize Node
                        </button>
                        <a href="{{ route('admin.scenes.index') }}" class="text-[9px] font-bold text-slate-400 hover:text-slate-900 uppercase tracking-widest transition-colors">
                            Discard
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
    <h3 class="text-xs font-black uppercase tracking-[5px] text-white mb-2 ml-1">Processing Node</h3>
    <p class="text-[10px] text-blue-200/80 font-bold uppercase tracking-[2px] animate-pulse">Generating Multi-Resolution Variants...</p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('form');
        const overlay = document.getElementById('upload-overlay');
        if (form && overlay) {
            form.addEventListener('submit', () => {
                overlay.classList.remove('hidden');
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
