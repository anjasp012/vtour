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
                        <label class="text-[9px] font-bold text-slate-500 uppercase tracking-widest ml-0.5">Equirectangular Buffer Collection (JPG/PNG)</label>
                        <div class="relative py-8 border border-dashed border-slate-300 rounded bg-slate-50/30 hover:bg-white hover:border-blue-400 transition-all flex flex-col items-center justify-center text-center group cursor-pointer">
                            <i class="fas fa-images text-slate-300 group-hover:text-blue-500 transition-colors mb-2"></i>
                            <p class="text-[10px] font-bold text-slate-400">Click to select one or multiple source files</p>
                            <input type="file" name="images[]" accept="image/jpeg,image/png" multiple required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="this.parentElement.querySelector('p').innerText = this.files.length + ' files selected'">
                        </div>
                        @error('images') <p class="text-rose-500 text-[8px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div class="p-4 bg-slate-50 rounded border border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded flex items-center justify-center text-xs">
                                <i class="fas fa-play"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-700 uppercase tracking-widest block leading-none mb-1">Initialization Node</span>
                                <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest">Entry waypoint for tour</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_start_scene" value="1" class="sr-only peer">
                            <div class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-blue-600 shadow-inner"></div>
                        </label>
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

<style type="text/tailwindcss">
    @layer utilities {
        .modern-input {
            @apply w-full bg-white border border-slate-200 rounded px-3 py-2 text-xs font-semibold text-slate-800 transition-all outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100;
        }
    }
</style>
@endsection
