@extends('components.admin-layout')

@section('header', 'Visual Editor')

@section('content')
<div class="h-full flex overflow-hidden">
    <!-- 3D Area (Pro Dark) -->
    <div class="flex-1 bg-[#0a0a0a] relative overflow-hidden group">
        <div id="panolens-container" class="w-full h-full cursor-default"></div>

        <!-- Toolbar Info Overlay -->
        <div class="absolute top-4 left-4 z-10 flex items-center gap-3">
            <div class="bg-[#1a1a1a]/80 backdrop-blur border border-white/5 px-4 py-2 rounded-lg shadow-xl">
                <div class="flex items-center gap-4">
                    <div class="flex flex-col">
                        <span class="text-[8px] font-bold text-slate-500 uppercase tracking-widest leading-none">Workspace</span>
                        <span class="text-white font-bold text-xs mt-1">{{ $scene->name }}</span>
                    </div>
                    <div class="w-px h-6 bg-white/10"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                        <span class="text-[9px] font-bold text-white/50 uppercase tracking-widest">Active</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top-right overlay: coord display + lock button -->
        <div class="absolute top-4 right-4 z-10 flex items-center gap-2">
            <!-- Small Coordinates Display (shown during node placement) -->
            <div id="coord-display" class="hidden bg-[#1a1a1a]/80 backdrop-blur border border-white/5 px-4 py-2 rounded-lg text-white font-mono text-[9px] tracking-widest shadow-xl">
                POS: <span class="text-blue-400">X: 0 | Y: 0 | Z: 0</span>
            </div>

            <!-- Lock Initial View Button -->
            <button id="btn-lock-view"
                onclick="lockInitialView()"
                title="Lock initial camera direction for this scene"
                class="flex items-center gap-2 px-3 py-2 rounded-lg text-[9px] font-bold uppercase tracking-widest shadow-xl transition-all duration-200 select-none
                       bg-[#1a1a1a]/80 backdrop-blur border border-white/10 text-white/60 hover:border-indigo-500/50 hover:text-white">
                <i class="fas fa-lock-open text-[10px]" id="lock-icon"></i>
                <span id="lock-label">Lock View</span>
            </button>
        </div>

        <!-- Instructional Toast -->
        <div id="instruction-toast" class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 px-6 py-3 bg-white border border-slate-200 rounded-lg shadow-2xl transition-all duration-500 opacity-0 translate-y-4 pointer-events-none">
            <p class="text-slate-900 text-[9px] font-bold uppercase tracking-widest flex items-center gap-3">
                <i class="fas fa-info-circle text-blue-600"></i>
                <span id="toast-text">Use right-click to add points</span>
            </p>
        </div>
    </div>

    <!-- Professional Inspector (Right Sidebar) -->
    <aside id="sidebar-panel" class="w-[340px] bg-white border-l border-slate-200 flex flex-col shadow-2xl z-20 overflow-hidden">
        <!-- Sidebar Header -->
        <div class="p-5 border-b border-slate-100 bg-slate-50/50 shrink-0">
            <h3 id="sidebar-title" class="text-sm font-bold text-slate-900 uppercase tracking-widest">Inspector</h3>
            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1">Configure node properties</p>
        </div>

        <!-- Sidebar Content -->
        <div class="flex-1 overflow-y-auto p-5 scrollbar-thin">
            <!-- List Mode -->
            <div id="state-list" class="space-y-6">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Scene Nodes</span>
                    <span class="bg-blue-600 text-white text-[9px] font-bold px-2 py-0.5 rounded" id="point-count-badge">{{ count($scene->infospots) }}</span>
                </div>
                
                <div class="space-y-1.5" id="infospots-list-container">
                    @forelse($scene->infospots as $spot)
                        <button onclick="editInfospot({{ $spot->id }}, {{ json_encode($spot) }})" class="w-full flex items-center justify-between p-3 bg-white border border-slate-100 rounded-lg hover:border-blue-500 hover:bg-blue-50/10 transition-all group text-left shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 {{ $spot->type == 'info' ? 'bg-blue-50 text-blue-600' : 'bg-slate-100 text-slate-600' }} rounded flex items-center justify-center text-[10px] shadow-inner">
                                    <i class="fas {{ $spot->type == 'info' ? 'fa-info' : 'fa-location-arrow' }}"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-800 truncate max-w-[150px]">{{ $spot->title ?? ($spot->type == 'info' ? 'Info Node' : 'Nav Node') }}</span>
                                    <span class="text-[8px] text-slate-400 font-bold uppercase tracking-widest">X: {{ round($spot->position_x) }} Y: {{ round($spot->position_y) }}</span>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-[8px] text-slate-300 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    @empty
                        <div class="py-12 text-center bg-slate-50 rounded-lg border border-dashed border-slate-200">
                            <i class="fas fa-mouse-pointer text-slate-200 text-xl mb-3"></i>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest px-6">Right-click on the 3D viewport to place nodes.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Form Mode -->
            <div id="state-form" class="hidden animate-fade-in space-y-6">
                <form id="infospot-form" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div id="method-put"></div>

                    <!-- Behavior Section -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <label class="text-[9px] font-bold text-slate-400 uppercase tracking-[2px]">Properties</label>
                            <input type="hidden" name="type" id="input-type">
                        </div>
                        
                        <div class="space-y-3">
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded flex items-center justify-between shadow-sm">
                                <div>
                                    <label class="block text-slate-800 font-bold text-[9px] uppercase tracking-widest">3D Perspective</label>
                                    <p class="text-[8px] text-slate-400 font-bold uppercase tracking-wider">Surface alignment</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_perspective" id="input-perspective" value="1" class="sr-only peer">
                                    <div class="w-8 h-4 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:rounded-full after:h-2 after:w-2 after:transition-all peer-checked:bg-blue-600 shadow-inner"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Transformation Section -->
                    <div id="transformation-controls" class="hidden space-y-3">
                        <label class="text-[9px] font-bold text-blue-600 uppercase tracking-[2px] block border-b border-blue-50 pb-2">3D Calibration</label>
                        
                        <div class="space-y-4 p-3 bg-slate-50 border border-slate-200 rounded shadow-inner">
                            <div class="space-y-3">
                                <div class="space-y-1.5">
                                    <div class="flex justify-between text-[8px] font-bold text-slate-500 uppercase"><span>Rotate X</span><span id="label-rx" class="text-blue-600 font-mono">0°</span></div>
                                    <input type="range" name="rotation_x" id="input-rx" min="-3.14" max="3.14" step="0.01" value="0" class="inspector-slider">
                                </div>
                                <div class="space-y-1.5">
                                    <div class="flex justify-between text-[8px] font-bold text-slate-500 uppercase"><span>Rotate Y</span><span id="label-ry" class="text-blue-600 font-mono">0°</span></div>
                                    <input type="range" name="rotation_y" id="input-ry" min="-3.14" max="3.14" step="0.01" value="0" class="inspector-slider">
                                </div>
                                <div class="space-y-1.5">
                                    <div class="flex justify-between text-[8px] font-bold text-slate-500 uppercase"><span>Rotate Z</span><span id="label-rz" class="text-blue-600 font-mono">0°</span></div>
                                    <input type="range" name="rotation_z" id="input-rz" min="-3.14" max="3.14" step="0.01" value="0" class="inspector-slider">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3 border-t border-slate-200 pt-3 mt-1">
                                <div class="space-y-1.5">
                                    <div class="flex justify-between text-[8px] font-bold text-slate-500 uppercase"><span>Scale X</span><span id="label-sx" class="text-emerald-600 font-mono">1.0</span></div>
                                    <input type="range" name="scale_x" id="input-sx" min="0.1" max="5" step="0.1" value="1" class="inspector-slider">
                                </div>
                                <div class="space-y-1.5">
                                    <div class="flex justify-between text-[8px] font-bold text-slate-500 uppercase"><span>Scale Y</span><span id="label-sy" class="text-emerald-600 font-mono">1.0</span></div>
                                    <input type="range" name="scale_y" id="input-sy" min="0.1" max="5" step="0.1" value="1" class="inspector-slider">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metadata Section (Bilingual) -->
                    <div id="fields-info" class="space-y-6">
                        <label class="text-[9px] font-bold text-slate-400 uppercase tracking-[2px] block border-b border-slate-100 pb-2">Content</label>
                        
                        <div class="space-y-4">
                            <!-- Media Assets Panel -->
                            <div class="space-y-2 p-3 bg-slate-900 rounded border border-slate-800 shadow-inner">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-widest">Media Assets</label>
                                    <button type="button" id="btn-add-asset-row"
                                        class="text-[7px] font-bold bg-slate-700 hover:bg-slate-600 text-slate-300 px-2 py-1 rounded tracking-widest uppercase transition-colors flex items-center gap-1">
                                        <i class="fas fa-plus"></i> Add File
                                    </button>
                                </div>

                                <!-- Existing assets list (shown in edit mode) -->
                                <div id="existing-assets-list" class="space-y-1.5 hidden"></div>

                                <!-- New file rows -->
                                <div id="new-asset-rows" class="space-y-2"></div>
                                <p id="no-asset-hint" class="text-[8px] text-slate-600 italic text-center py-2">Klik &quot;+ Add File&quot; untuk upload.</p>

                                <!-- Upload action (only visible when there are new rows) -->
                                <div id="asset-upload-wrap" class="hidden pt-2 border-t border-slate-800">
                                    <button type="button" id="btn-upload-assets"
                                        class="w-full py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[8px] font-bold uppercase tracking-widest rounded transition-colors flex items-center justify-center gap-1.5">
                                        <i class="fas fa-cloud-upload-alt"></i> Upload Files
                                    </button>
                                    <div id="asset-upload-loading" class="hidden items-center justify-center gap-2 py-1">
                                        <div class="w-3 h-3 border-2 border-slate-600 border-t-indigo-500 rounded-full animate-spin"></div>
                                        <span class="text-[8px] text-slate-400 uppercase tracking-widest">Uploading...</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[9px] font-bold text-slate-500 uppercase tracking-widest ml-0.5">Node Title</label>
                                <input type="text" name="title" id="input-title" class="modern-input" placeholder="Enter node title...">
                            </div>

                            <div class="space-y-3">
                                <!-- Indonesian -->
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
                                        <span class="w-1 h-1 bg-slate-400 rounded-full"></span> Indonesian
                                    </label>
                                    <textarea name="content_id" id="input-desc-id" class="hidden"></textarea>
                                    <button type="button" class="narasi-btn" id="btn-open-editor-id"
                                        onclick="openQuillEditor('id')">
                                        <span style="font-size:10px">🇮🇩</span>
                                        <span class="preview-text" id="preview-id">Klik untuk menulis narasi...</span>
                                        <i class="fas fa-pen-to-square text-[10px] opacity-40 ml-2"></i>
                                    </button>
                                </div>

                                <!-- English -->
                                <div class="space-y-1">
                                    <label class="text-[8px] font-bold text-blue-400 uppercase tracking-widest flex items-center gap-1.5">
                                        <span class="w-1 h-1 bg-blue-400 rounded-full"></span> English
                                    </label>
                                    <textarea name="content_en" id="input-desc-en" class="hidden"></textarea>
                                    <button type="button" class="narasi-btn" id="btn-open-editor-en"
                                        onclick="openQuillEditor('en')">
                                        <span style="font-size:10px">🇬🇧</span>
                                        <span class="preview-text" id="preview-en">Click to write narration...</span>
                                        <i class="fas fa-pen-to-square text-[10px] opacity-40 ml-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Section -->
                    <div id="fields-nav" class="hidden space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <label class="text-[9px] font-bold text-slate-400 uppercase tracking-[2px]">Routing</label>
                            <button type="button" onclick="toggleQuickCreate()" class="text-[8px] font-bold text-blue-600 hover:text-blue-700 uppercase tracking-widest flex items-center gap-1.5 transition-colors">
                                <i class="fas fa-plus-circle"></i> Quick Create
                            </button>
                        </div>

                        <!-- Quick Create Inline Form (Hidden by default) -->
                        <div id="quick-create-panel" class="hidden bg-slate-900 p-4 rounded-lg border border-slate-800 space-y-4 animate-fade-in shadow-xl">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-[8px] font-bold text-white/50 uppercase tracking-[2px]">New Buffer Entry</span>
                                <button type="button" onclick="toggleQuickCreate()" class="text-white/30 hover:text-white"><i class="fas fa-times text-[10px]"></i></button>
                            </div>
                            
                            <div class="space-y-3">
                                <div class="space-y-1.5">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase">Buffer Name</label>
                                    <input type="text" id="qc-name" class="modern-input bg-slate-800 border-slate-700 text-white placeholder-slate-600" placeholder="e.g. Garden View">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[8px] font-bold text-slate-500 uppercase tracking-widest leading-none">Visual Data (360° Image)</label>
                                    <input type="file" id="qc-image" accept="image/*" class="w-full text-[9px] text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-[9px] file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition-all cursor-pointer">
                                </div>
                                <button type="button" id="btn-do-quick-create" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[9px] font-bold uppercase tracking-widest rounded shadow-lg shadow-emerald-500/10 transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-cloud-upload-alt"></i> Initialize Scene
                                </button>
                            </div>
                            <div id="qc-loading" class="hidden flex items-center justify-center gap-3">
                                <div class="w-3 h-3 border-2 border-slate-600 border-t-blue-500 rounded-full animate-spin"></div>
                                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Processing Buffer...</span>
                            </div>
                        </div>

                        <div class="space-y-1.5 group-choices">
                            <label class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Target Scene</label>
                            <select name="target_scene_id" id="input-target" class="modern-input">
                                <option value="">Select destination...</option>
                                @foreach($hasTargetScenes as $ts)
                                    <option value="{{ $ts->id }}">{{ $ts->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Coordinate Display -->
                    <div class="p-4 bg-slate-900 border border-slate-800 rounded-lg flex flex-col gap-4">
                        <span class="text-[8px] font-bold text-slate-500 uppercase tracking-[2px] border-b border-slate-800 pb-2">Coordinates</span>
                        <div class="grid grid-cols-3 gap-4 font-mono text-[9px] font-bold">
                            <div class="flex flex-col gap-1.5">
                                <div class="flex justify-between items-center text-white/40"><span class="text-blue-500">X</span><span id="val_x">0</span></div>
                                <div class="h-0.5 bg-slate-800 rounded-full overflow-hidden"><div id="bar_x" class="h-full bg-blue-600 transition-all duration-300" style="width: 0%"></div></div>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <div class="flex justify-between items-center text-white/40"><span class="text-emerald-500">Y</span><span id="val_y">0</span></div>
                                <div class="h-0.5 bg-slate-800 rounded-full overflow-hidden"><div id="bar_y" class="h-full bg-emerald-600 transition-all duration-300" style="width: 0%"></div></div>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <div class="flex justify-between items-center text-white/40"><span class="text-indigo-500">Z</span><span id="val_z">0</span></div>
                                <div class="h-0.5 bg-slate-800 rounded-full overflow-hidden"><div id="bar_z" class="h-full bg-indigo-600 transition-all duration-300" style="width: 0%"></div></div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="position_x" id="pos_x">
                    <input type="hidden" name="position_y" id="pos_y">
                    <input type="hidden" name="position_z" id="pos_z">

                    <!-- Actions Bar (Sticky bottom) -->
                    <div class="pt-6 flex flex-col gap-2">
                        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded text-[9px] uppercase tracking-widest shadow-lg hover:bg-blue-700 transition-all">Update Database</button>
                        <button type="button" onclick="cancelForm()" class="w-full bg-white text-slate-400 font-bold py-3 rounded text-[9px] uppercase tracking-widest border border-slate-200 hover:text-slate-950 transition-all">Cancel Edit</button>
                    </div>
                </form>

                <!-- Delete Action -->
                <form id="form-delete" method="POST" class="mt-8 pt-6 border-t border-slate-100 hidden">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Purge node?');" class="w-full flex items-center justify-center gap-2 text-rose-500 font-bold text-[9px] uppercase tracking-widest py-3 rounded border border-rose-50 hover:bg-rose-50 transition-all">
                        <i class="fas fa-trash-alt"></i> Purge From Memory
                    </button>
                </form>
            </div>
        </div>
    </aside>
</div>

<!-- Pro Context Menu (Dark) -->
<div id="context-menu" class="hidden fixed z-[999] bg-[#1a1a1a] border border-white/5 rounded shadow-2xl py-2 w-48 text-white font-bold text-[9px] uppercase tracking-widest">
    <div id="menu-add-info">
        <button onclick="handleMenuAction('add_info')" class="w-full text-left px-5 py-3 hover:bg-blue-600 flex items-center justify-between group transition-colors">
            <span>Add Info Node</span> <i class="fas fa-plus opacity-30 group-hover:opacity-100"></i>
        </button>
    </div>
    <div id="menu-add-nav">
        <button onclick="handleMenuAction('add_nav')" class="w-full text-left px-5 py-3 hover:bg-blue-600 flex items-center justify-between group border-t border-white/5 transition-colors">
            <span>Add Nav Link</span> <i class="fas fa-link opacity-30 group-hover:opacity-100"></i>
        </button>
    </div>
    <div id="menu-divider" class="h-px bg-white/5 my-1 mx-2"></div>
    <div id="menu-edit">
        <button onclick="handleMenuAction('edit')" class="w-full text-left px-5 py-3 hover:bg-slate-700 flex items-center justify-between group transition-colors">
            <span>Edit Properties</span> <i class="fas fa-cog opacity-30 group-hover:opacity-100"></i>
        </button>
    </div>
    <div id="menu-delete">
        <button onclick="handleMenuAction('delete')" class="w-full text-left px-5 py-3 hover:bg-rose-600 text-rose-400 hover:text-white flex items-center justify-between group border-t border-white/5 transition-colors">
            <span>Delete Point</span> <i class="fas fa-trash opacity-30 group-hover:opacity-100"></i>
        </button>
    </div>
</div>

<!-- Tailwind CSS v4 -->
<script src="https://unpkg.com/@tailwindcss/browser@4"></script>

<!-- Choices.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<!-- Quill Rich Text Editor -->
<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<style type="text/tailwindcss">
    @layer utilities {
        .modern-input {
            @apply w-full bg-white border border-slate-200 rounded px-3 py-2 text-xs font-semibold text-slate-800 transition-all outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100;
        }
        .inspector-slider {
            @apply w-full h-1 bg-slate-200 rounded appearance-none cursor-pointer accent-blue-600;
        }
        .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* Choices.js Custom Overrides for Professional UI */
        .choices { margin-bottom: 0; }
        .choices__inner { 
            @apply min-h-[38px] bg-white border border-slate-200 rounded !px-3 !py-1 flex items-center text-xs font-semibold text-slate-800 transition-all; 
        }
        .choices__input { @apply bg-transparent text-xs font-semibold text-slate-800 p-0; }
        .choices__list--dropdown { @apply rounded-lg shadow-2xl border border-slate-200 z-[9999]; }
        .choices__list--dropdown .choices__item--selectable.is-highlighted { @apply bg-blue-600 text-white; }
        .choices[data-type*="select-one"]::after { @apply border-t-slate-400; }
    }
</style>

<style>
    /* ---- Quill Editor Popup ---- */
    #quill-popup-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(6px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
    }
    #quill-popup-overlay.open { display: flex; }
    #quill-popup-box {
        background: #0f172a;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 24px;
        width: min(820px, 96vw);
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 40px 80px rgba(0,0,0,0.7);
    }
    #quill-popup-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 24px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        flex-shrink: 0;
    }
    #quill-popup-title {
        font-size: 13px;
        font-weight: 700;
        color: #e2e8f0;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    #quill-popup-close {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: none;
        background: rgba(255,255,255,0.1);
        color: rgba(255,255,255,0.5);
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s, color 0.2s;
    }
    #quill-popup-close:hover { background: #ef4444; color: #fff; }
    #quill-popup-editor {
        flex: 1;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    /* Override Quill toolbar for dark bg */
    #quill-popup-editor .ql-toolbar {
        background: #1e293b;
        border: none;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        padding: 10px 16px;
        flex-shrink: 0;
    }
    #quill-popup-editor .ql-toolbar button,
    #quill-popup-editor .ql-toolbar .ql-picker-label {
        color: #94a3b8;
    }
    #quill-popup-editor .ql-toolbar button:hover,
    #quill-popup-editor .ql-toolbar button.ql-active,
    #quill-popup-editor .ql-toolbar .ql-picker-label:hover {
        color: #6366f1;
    }
    #quill-popup-editor .ql-toolbar .ql-stroke { stroke: #94a3b8; }
    #quill-popup-editor .ql-toolbar button:hover .ql-stroke,
    #quill-popup-editor .ql-toolbar button.ql-active .ql-stroke { stroke: #6366f1; }
    #quill-popup-editor .ql-toolbar .ql-fill { fill: #94a3b8; }
    #quill-popup-editor .ql-toolbar button:hover .ql-fill,
    #quill-popup-editor .ql-toolbar button.ql-active .ql-fill { fill: #6366f1; }
    #quill-popup-editor .ql-container {
        border: none;
        background: #0f172a;
        flex: 1;
        overflow-y: auto;
        min-height: 320px;
    }
    #quill-popup-editor .ql-editor {
        font-size: 14px;
        line-height: 1.75;
        color: #cbd5e1;
        padding: 20px 24px;
        min-height: 320px;
    }
    #quill-popup-editor .ql-editor.ql-blank::before { color: rgba(148,163,184,0.4); }
    #quill-popup-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        padding: 14px 24px;
        border-top: 1px solid rgba(255,255,255,0.08);
        flex-shrink: 0;
    }
    .ql-popup-btn {
        padding: 8px 20px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }
    .ql-popup-btn-cancel {
        background: rgba(255,255,255,0.06);
        color: #94a3b8;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .ql-popup-btn-cancel:hover { background: rgba(255,255,255,0.12); color: #e2e8f0; }
    .ql-popup-btn-apply {
        background: #6366f1;
        color: #fff;
    }
    .ql-popup-btn-apply:hover { background: #4f46e5; }
    /* Narasi trigger buttons in sidebar */
    .narasi-btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border-radius: 8px;
        border: 1.5px dashed rgba(100,116,139,0.35);
        background: rgba(248,250,252,0.5);
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        text-align: left;
    }
    .narasi-btn:hover {
        border-color: #6366f1;
        background: rgba(99,102,241,0.05);
        color: #6366f1;
    }
    .narasi-btn .preview-text {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 160px;
        font-weight: 400;
        color: #94a3b8;
        font-size: 10px;
    }
    .narasi-btn.has-content {
        border-style: solid;
        border-color: rgba(99,102,241,0.4);
        background: rgba(99,102,241,0.04);
    }
    .narasi-btn.has-content .preview-text { color: #64748b; }

    /* ---- Drag & Drop sort indicators ---- */
    [draggable="true"] { transition: opacity 0.15s; }
    .drag-over-top {
        border-top: 2px solid #6366f1 !important;
        border-radius: 6px 6px 4px 4px;
    }
    .drag-over-bottom {
        border-bottom: 2px solid #6366f1 !important;
        border-radius: 4px 4px 6px 6px;
    }
    .drag-handle { touch-action: none; }
</style>

<script src="https://pchen66.github.io/js/three/three.min.js"></script>
<script src="https://pchen66.github.io/js/panolens/panolens.min.js"></script>
<script>
    const container = document.getElementById('panolens-container');
    const viewer = new PANOLENS.Viewer({ 
        container: container, 
        autoHideNav: false, 
        controlBar: false, 
        autoRotate: false,
        cameraFov: 90
    });
    
    // Invert scroll zoom direction
    const controls = viewer.getControl();
    controls.dollyIn = controls.dollyOut;

    const panorama = new PANOLENS.ImagePanorama('{{ Storage::url($scene->image_path) }}');
    viewer.add(panorama);

    // Initial state vars
    const existingSpots = @json($scene->infospots);
    const renderedSpots = {};
    let isAdding = false;
    let editingId = null; 

    // Helper: Create Styled Icon
    function createStyledIcon(iconContent, color = '#2563eb', rotation = 0) {
        return new Promise((resolve) => {
            const canvas = document.createElement('canvas');
            canvas.width = 150; canvas.height = 150;
            const ctx = canvas.getContext('2d');
            if(rotation) {
                ctx.translate(75, 75);
                ctx.rotate(rotation * Math.PI / 180);
                ctx.translate(-75, -75);
            }
            ctx.beginPath();
            ctx.arc(75, 75, 60, 0, 2 * Math.PI);
            ctx.fillStyle = color;
            ctx.fill();
            ctx.fillStyle = "white";
            ctx.font = 'bold 80px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(iconContent, 75, 80);
            resolve(canvas.toDataURL());
        });
    }

    function addBounce(infospot) {
        // Hentikan tween lama jika ada
        if (infospot.bounceTween) {
            infospot.bounceTween.stop();
        }

        const startY = infospot.position.y;
        infospot.bounceTween = new TWEEN.Tween(infospot.position)
            .to({ y: startY + 200 }, 1000)
            .easing(TWEEN.Easing.Quadratic.InOut)
            .repeat(Infinity)
            .yoyo(true)
            .start();
    }

    let ghostMarker = null;
    let iconTextures = {};

    // Render existing spots
    Promise.all([
        createStyledIcon('i', '#2563eb'), // info
        createStyledIcon('⮝', '#4f46e5') // nav (Updated to vibrant indigo)
    ]).then(([infoUrl, navUrl]) => {
        iconTextures.info = infoUrl;
        iconTextures.nav = navUrl;
        
        existingSpots.forEach(spot => {
            renderMarker(spot);
        });
        
        ghostMarker = new PANOLENS.Infospot(600, infoUrl);
        ghostMarker.material.opacity = 0.5;
    });

    function renderMarker(spotData) {
        if (renderedSpots[spotData.id]) {
            const oldMarker = renderedSpots[spotData.id];
            if (oldMarker.bounceTween) oldMarker.bounceTween.stop();
            panorama.remove(oldMarker);
            delete renderedSpots[spotData.id];
        }

        const iconUrl = spotData.type === 'info' ? iconTextures.info : iconTextures.nav;
        let marker;

        if (spotData.is_perspective) {
            const geometry = new THREE.PlaneGeometry(600, 600);
            const loader = new THREE.TextureLoader();
            const texture = loader.load(iconUrl);
            const material = new THREE.MeshBasicMaterial({ 
                map: texture, 
                transparent: true, 
                side: THREE.DoubleSide,
                alphaTest: 0.1,
                depthTest: false,
                depthWrite: false
            });
            marker = new THREE.Mesh(geometry, material);
            marker.renderOrder = 999;
            marker.rotation.order = 'YXZ';
            marker.rotation.set(spotData.rotation_x || 0, spotData.rotation_y || 0, spotData.rotation_z || 0);
            marker.scale.set(spotData.scale_x || 1, spotData.scale_y || 1, 1);
            marker.isPerspectiveMesh = true;
            marker.spotData = spotData;
        } else {
            marker = new PANOLENS.Infospot(600, iconUrl);
        }

        marker.position.set(spotData.position_x, spotData.position_y, spotData.position_z);
        
        marker.addEventListener('click', () => {
            if(isAdding) return;
            if(window.wasDragging) {
                window.wasDragging = false; 
                return;
            }
            editInfospot(spotData.id, spotData);
        });

        // Hover Effect
        marker.addEventListener('hoverenter', () => {
            if (marker.isPerspectiveMesh) {
                new TWEEN.Tween(marker.scale).to({ x: (spotData.scale_x || 1) * 1.2, y: (spotData.scale_y || 1) * 1.2, z: 1.2 }, 300).easing(TWEEN.Easing.Back.Out).start();
            } else {
                marker.scale.set(1.3, 1.3, 1.3);
            }
        });
        marker.addEventListener('hoverleave', () => {
            if (marker.isPerspectiveMesh) {
                new TWEEN.Tween(marker.scale).to({ x: spotData.scale_x || 1, y: spotData.scale_y || 1, z: 1 }, 300).easing(TWEEN.Easing.Back.Out).start();
            } else {
                marker.scale.set(1, 1, 1);
            }
        });

        // Bounce Animation
        addBounce(marker);

        panorama.add(marker);
        renderedSpots[spotData.id] = marker;
    }

    const createForm = document.getElementById('state-form');
    const listState = document.getElementById('state-list');
    const coordDisplay = document.getElementById('coord-display');
    const titleHeader = document.getElementById('sidebar-title');
    const inputType = document.getElementById('input-type');
    const fieldsInfo = document.getElementById('fields-info');
    const fieldsNav = document.getElementById('fields-nav');
    const formEl = document.getElementById('infospot-form');
    const methodPut = document.getElementById('method-put');
    const formDelete = document.getElementById('form-delete');
    const btnReposition = document.getElementById('btn-reposition');
    const toast = document.getElementById('instruction-toast');
    const toastText = document.getElementById('toast-text');

    const inputPerspective = document.getElementById('input-perspective');
    const transformControls = document.getElementById('transformation-controls');
    const inputRx = document.getElementById('input-rx'), inputRy = document.getElementById('input-ry'), inputRz = document.getElementById('input-rz');
    const inputSx = document.getElementById('input-sx'), inputSy = document.getElementById('input-sy');
    const labelRx = document.getElementById('label-rx'), labelRy = document.getElementById('label-ry'), labelRz = document.getElementById('label-rz');
    const labelSx = document.getElementById('label-sx'), labelSy = document.getElementById('label-sy');

    const valX = document.getElementById('val_x'), valY = document.getElementById('val_y'), valZ = document.getElementById('val_z');
    const barX = document.getElementById('bar_x'), barY = document.getElementById('bar_y'), barZ = document.getElementById('bar_z');
    const pos_x = document.getElementById('pos_x'), pos_y = document.getElementById('pos_y'), pos_z = document.getElementById('pos_z');

    inputPerspective.addEventListener('change', (e) => {
        if (e.target.checked) transformControls.classList.remove('hidden');
        else transformControls.classList.add('hidden');
        updateRealtimePreview();
    });

    [inputRx, inputRy, inputRz, inputSx, inputSy].forEach(input => {
        input.addEventListener('input', () => {
            updateLabels();
            updateRealtimePreview();
        });
    });

    function updateLabels() {
        labelRx.innerText = `${Math.round(inputRx.value * 180 / Math.PI)}°`;
        labelRy.innerText = `${Math.round(inputRy.value * 180 / Math.PI)}°`;
        labelRz.innerText = `${Math.round(inputRz.value * 180 / Math.PI)}°`;
        labelSx.innerText = inputSx.value;
        labelSy.innerText = inputSy.value;
    }

    function updateRealtimePreview() {
        if (!editingId || !renderedSpots[editingId]) return;
        const currentData = existingSpots.find(s => s.id == editingId);
        if (currentData) {
            const needsRebuild = (!!currentData.is_perspective !== inputPerspective.checked);
            currentData.is_perspective = inputPerspective.checked;
            currentData.rotation_x = parseFloat(inputRx.value);
            currentData.rotation_y = parseFloat(inputRy.value);
            currentData.rotation_z = parseFloat(inputRz.value);
            currentData.scale_x = parseFloat(inputSx.value);
            currentData.scale_y = parseFloat(inputSy.value);

            if (needsRebuild) renderMarker(currentData);
            else {
                const marker = renderedSpots[editingId];
                if (currentData.is_perspective) {
                    marker.rotation.set(currentData.rotation_x, currentData.rotation_y, currentData.rotation_z);
                    marker.scale.set(currentData.scale_x, currentData.scale_y, 1);
                }
            }
        }
    }

    inputType.addEventListener('change', (e) => {
        if(e.target.value === 'nav') {
            fieldsInfo.classList.add('hidden');
            fieldsNav.classList.remove('hidden');
        } else {
            fieldsInfo.classList.remove('hidden');
            fieldsNav.classList.add('hidden');
        }
    });

    const ctxMenu = document.getElementById('context-menu');
    let lastRightClickCoords = null;
    let lastRightClickSpot = null;

    container.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        const rect = container.getBoundingClientRect();
        const mouse = new THREE.Vector2(((e.clientX - rect.left) / rect.width) * 2 - 1, -((e.clientY - rect.top) / rect.height) * 2 + 1);
        const raycaster = new THREE.Raycaster();
        raycaster.setFromCamera(mouse, viewer.getCamera());
        
        const markers = Object.values(renderedSpots);
        const markerIntersects = raycaster.intersectObjects(markers, true);

        if (markerIntersects.length > 0) {
            let hit = markerIntersects[0].object;
            let foundId = null;
            let curr = hit;
            while (curr && !foundId) {
                for (const id in renderedSpots) { if (renderedSpots[id] === curr) { foundId = id; break; } }
                curr = curr.parent;
            }
            if (foundId) {
                lastRightClickSpot = { id: foundId, data: existingSpots.find(s => s.id == foundId) };
                showContextMenu(e.clientX, e.clientY, 'spot');
                return;
            }
        }

        const panoramaIntersects = raycaster.intersectObject(panorama, false);
        if (panoramaIntersects.length > 0) {
            const p = panoramaIntersects[0].point;
            lastRightClickCoords = { x: Math.round(p.x), y: Math.round(p.y), z: Math.round(p.z) };
            showContextMenu(e.clientX, e.clientY, 'empty');
        }
    }, true);

    function showContextMenu(x, y, mode) {
        ctxMenu.classList.remove('hidden');
        ctxMenu.style.top = `${y}px`; ctxMenu.style.left = `${x}px`;
        if (mode === 'spot') {
            document.getElementById('menu-add-info').classList.add('hidden');
            document.getElementById('menu-add-nav').classList.add('hidden');
            document.getElementById('menu-edit').classList.remove('hidden');
            document.getElementById('menu-delete').classList.remove('hidden');
            document.getElementById('menu-divider').classList.remove('hidden');
        } else {
            document.getElementById('menu-add-info').classList.remove('hidden');
            document.getElementById('menu-add-nav').classList.remove('hidden');
            document.getElementById('menu-edit').classList.add('hidden');
            document.getElementById('menu-delete').classList.add('hidden');
            document.getElementById('menu-divider').classList.add('hidden');
        }
    }

    function hideContextMenu() { ctxMenu.classList.add('hidden'); }
    window.addEventListener('click', (e) => { if (!ctxMenu.contains(e.target)) hideContextMenu(); });

    window.handleMenuAction = function(action) {
        hideContextMenu();
        if (action === 'add_info' || action === 'add_nav') {
            const type = (action === 'add_info') ? 'info' : 'nav';
            if (lastRightClickCoords) {
                openForm('create');
                inputType.value = type;
                inputType.dispatchEvent(new Event('change'));
                pos_x.value = lastRightClickCoords.x; pos_y.value = lastRightClickCoords.y; pos_z.value = lastRightClickCoords.z;
                updatePosDisplay(lastRightClickCoords.x, lastRightClickCoords.y, lastRightClickCoords.z);
                
                // Update ghost marker texture to match type
                if (ghostMarker) {
                    const textureUrl = (type === 'info') ? iconTextures.info : iconTextures.nav;
                    const loader = new THREE.TextureLoader();
                    ghostMarker.material.map = loader.load(textureUrl);
                    ghostMarker.material.needsUpdate = true;
                    
                    if (!ghostMarker.parent) panorama.add(ghostMarker);
                    ghostMarker.position.set(lastRightClickCoords.x, lastRightClickCoords.y, lastRightClickCoords.z);
                }
            }
        } else if (action === 'edit') {
            if (lastRightClickSpot) editInfospot(lastRightClickSpot.id, lastRightClickSpot.data);
        } else if (action === 'delete') {
            if (lastRightClickSpot && confirm('Hapus point ini?')) {
                const f = document.getElementById('form-delete');
                f.action = `{{ url('admin/infospots') }}/${lastRightClickSpot.id}`;
                f.submit();
            }
        }
    };

    function updatePosDisplay(x, y, z) {
        valX.innerText = x; valY.innerText = y; valZ.innerText = z;
        const max = 5000;
        barX.style.width = Math.min(Math.abs(x)/max * 100, 100) + '%';
        barY.style.width = Math.min(Math.abs(y)/max * 100, 100) + '%';
        barZ.style.width = Math.min(Math.abs(z)/max * 100, 100) + '%';
        
        coordDisplay.classList.remove('hidden');
        coordDisplay.querySelector('.text-blue-400').innerText = `X: ${x} | Y: ${y} | Z: ${z}`;
    }

    // Track infospot id for asset uploads
    let currentUploadInfospotId = null;
    let assetRowIndex = 0;

    // ---- Asset row management ----
    document.getElementById('btn-add-asset-row').addEventListener('click', () => {
        addNewAssetRow();
    });

    function addNewAssetRow() {
        const idx = assetRowIndex++;
        const row = document.createElement('div');
        row.className = 'flex flex-col gap-1.5 p-2 bg-slate-800 rounded border border-slate-700';
        row.dataset.index = idx;
        row.innerHTML = `
            <div class="flex items-center gap-1.5">
                <select class="flex-1 bg-slate-700 border border-slate-600 text-slate-300 text-[8px] font-bold uppercase tracking-widest rounded px-2 py-1 focus:outline-none asset-type-select">
                    <option value="2d">🖼 2D Image</option>
                    <option value="3d">🧊 3D GLB</option>
                </select>
                <button type="button" class="remove-asset-row text-slate-500 hover:text-rose-400 transition-colors">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
            </div>
            <input type="text" placeholder="Label (opsional)" class="asset-label bg-slate-700 border border-slate-600 text-slate-300 text-[8px] rounded px-2 py-1 placeholder-slate-600 focus:outline-none focus:border-slate-500">
            <input type="file" accept="image/*" class="asset-file w-full text-[8px] text-slate-400 file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-[8px] file:font-bold file:bg-slate-700 file:text-slate-300 hover:file:bg-slate-600 cursor-pointer">
        `;

        const typeSelect = row.querySelector('.asset-type-select');
        const fileInput  = row.querySelector('.asset-file');
        typeSelect.addEventListener('change', () => {
            fileInput.accept = typeSelect.value === '3d' ? '.glb' : 'image/*';
            fileInput.value = '';
        });
        row.querySelector('.remove-asset-row').addEventListener('click', () => {
            row.remove();
            updateAssetUploadVisibility();
        });

        document.getElementById('new-asset-rows').appendChild(row);
        updateAssetUploadVisibility();
    }

    function updateAssetUploadVisibility() {
        const hasRows = document.getElementById('new-asset-rows').children.length > 0;
        document.getElementById('asset-upload-wrap').classList.toggle('hidden', !hasRows);
        document.getElementById('no-asset-hint').classList.toggle('hidden', hasRows);
    }

    // ---- Load existing assets via AJAX ----
    async function loadExistingAssets(infospotId) {
        const container = document.getElementById('existing-assets-list');
        container.innerHTML = '<p class="text-[8px] text-slate-500 italic text-center py-2">Loading...</p>';
        container.classList.remove('hidden');

        try {
            const res = await fetch(`{{ url('admin/infospots') }}/${infospotId}/assets`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            if (!data.assets || data.assets.length === 0) {
                container.innerHTML = '<p class="text-[8px] text-slate-600 italic text-center py-1">Belum ada asset.</p>';
                return;
            }

            container.innerHTML = data.assets.map(a => `
                <div class="flex items-center gap-1.5 p-1.5 bg-slate-800 rounded border border-slate-700 cursor-default select-none"
                     id="asset-row-${a.id}" data-asset-id="${a.id}" draggable="true">
                    <!-- Drag Handle -->
                    <span class="drag-handle shrink-0 text-slate-600 hover:text-slate-300 cursor-grab active:cursor-grabbing px-0.5"
                          title="Drag to reorder">
                        <i class="fas fa-grip-vertical text-[9px]"></i>
                    </span>
                    <span class="shrink-0 text-[7px] font-bold px-1.5 py-0.5 rounded ${
                        a.file_type === '3d'
                        ? 'bg-purple-900 text-purple-300'
                        : 'bg-blue-900 text-blue-300'
                    } uppercase tracking-widest">${a.file_type === '3d' ? '3D' : '2D'}</span>
                    <span class="text-[8px] text-slate-300 truncate flex-1 min-w-0">${a.label || a.filename}</span>
                    <button type="button" onclick="deleteAsset(${a.id})" title="Hapus"
                        class="shrink-0 text-slate-500 hover:text-rose-400 transition-colors">
                        <i class="fas fa-trash-alt text-[9px]"></i>
                    </button>
                </div>
            `).join('');

            // init drag-and-drop
            initDragSort(container);

        } catch(e) {
            container.innerHTML = '<p class="text-[8px] text-rose-400 italic text-center py-1">Gagal memuat asset.</p>';
        }
    }

    // ---- Vanilla drag-and-drop sort ----
    function initDragSort(container) {
        let draggingEl = null;

        container.querySelectorAll('[draggable="true"]').forEach(row => {
            row.addEventListener('dragstart', (e) => {
                draggingEl = row;
                row.style.opacity = '0.4';
                e.dataTransfer.effectAllowed = 'move';
            });

            row.addEventListener('dragend', () => {
                row.style.opacity = '';
                draggingEl = null;
                container.querySelectorAll('[draggable="true"]').forEach(r => r.classList.remove('drag-over'));
                // Save new order to server
                saveSortOrder(container);
            });

            row.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                if (row === draggingEl) return;

                // Determine insert position
                const rect   = row.getBoundingClientRect();
                const midY   = rect.top + rect.height / 2;
                const isAfter = e.clientY > midY;

                container.querySelectorAll('[draggable="true"]').forEach(r => r.classList.remove('drag-over-top', 'drag-over-bottom'));
                row.classList.add(isAfter ? 'drag-over-bottom' : 'drag-over-top');
            });

            row.addEventListener('drop', (e) => {
                e.preventDefault();
                if (!draggingEl || draggingEl === row) return;

                const rect   = row.getBoundingClientRect();
                const isAfter = e.clientY > rect.top + rect.height / 2;

                if (isAfter) {
                    row.after(draggingEl);
                } else {
                    row.before(draggingEl);
                }
                container.querySelectorAll('[draggable="true"]').forEach(r => r.classList.remove('drag-over-top', 'drag-over-bottom'));
            });
        });
    }

    // ---- Save new sort order to server ----
    async function saveSortOrder(container) {
        const ids = [...container.querySelectorAll('[data-asset-id]')]
            .map(el => ({ id: parseInt(el.dataset.assetId) }));

        if (ids.length === 0) return;

        try {
            await fetch('{{ url('admin/infospot-assets/reorder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ order: ids })
            });
        } catch(e) {
            console.warn('Failed to save sort order:', e);
        }
    }

    // ---- Delete asset ----
    window.deleteAsset = async function(assetId) {
        if (!confirm('Hapus asset ini?')) return;
        try {
            const res = await fetch(`{{ url('admin/infospot-assets') }}/${assetId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById(`asset-row-${assetId}`)?.remove();
                const list = document.getElementById('existing-assets-list');
                if (!list.querySelector('[id^="asset-row-"]')) {
                    list.innerHTML = '<p class="text-[8px] text-slate-600 italic text-center py-1">Belum ada asset.</p>';
                }
            }
        } catch(e) { alert('Gagal menghapus asset.'); }
    };

    // ---- Upload new assets ----
    document.getElementById('btn-upload-assets').addEventListener('click', async () => {
        if (!currentUploadInfospotId) { alert('Simpan node dahulu sebelum upload asset.'); return; }

        const rows = document.getElementById('new-asset-rows').querySelectorAll('[data-index]');
        if (rows.length === 0) return;

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');

        let hasFile = false;
        rows.forEach((row, i) => {
            const fileInput = row.querySelector('.asset-file');
            const typeSelect = row.querySelector('.asset-type-select');
            const label = row.querySelector('.asset-label');
            if (fileInput.files.length > 0) {
                formData.append(`assets[${i}][file]`, fileInput.files[0]);
                formData.append(`assets[${i}][file_type]`, typeSelect.value);
                formData.append(`assets[${i}][label]`, label.value);
                hasFile = true;
            }
        });

        if (!hasFile) { alert('Pilih minimal satu file.'); return; }

        const btn = document.getElementById('btn-upload-assets');
        const loading = document.getElementById('asset-upload-loading');
        btn.classList.add('hidden');
        loading.classList.remove('hidden');
        loading.style.display = 'flex';

        try {
            const res = await fetch(`{{ url('admin/infospots') }}/${currentUploadInfospotId}/assets`, {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('new-asset-rows').innerHTML = '';
                updateAssetUploadVisibility();
                loadExistingAssets(currentUploadInfospotId);
                showInstruction('ASSETS UPLOADED.');
            } else {
                alert('Upload gagal: ' + (data.message || 'Unknown error'));
            }
        } catch(e) { alert('Upload error: ' + e.message); }
        finally {
            btn.classList.remove('hidden');
            loading.classList.add('hidden');
            loading.style.display = '';
        }
    });

    function openForm(mode, spot = null) {
        listState.classList.add('hidden');
        createForm.classList.remove('hidden');
        if (mode === 'create') {
            titleHeader.innerText = "New Node";
            formEl.reset();
            document.getElementById('input-desc-id').value = '';
            document.getElementById('input-desc-en').value = '';
            // Reset asset panel
            document.getElementById('existing-assets-list').innerHTML = '';
            document.getElementById('existing-assets-list').classList.add('hidden');
            document.getElementById('new-asset-rows').innerHTML = '';
            document.getElementById('asset-upload-wrap').classList.add('hidden');
            document.getElementById('no-asset-hint').classList.remove('hidden');
            currentUploadInfospotId = null;
            methodPut.innerHTML = '';
            formEl.action = "{{ route('admin.scenes.infospots.store', $scene) }}";
            formDelete.classList.add('hidden');
            inputPerspective.checked = false;
            inputPerspective.dispatchEvent(new Event('change'));
        } else {
            titleHeader.innerText = "Inspector";
            inputType.value = spot.type;
            inputType.dispatchEvent(new Event('change'));
            pos_x.value = spot.position_x; pos_y.value = spot.position_y; pos_z.value = spot.position_z;
            updatePosDisplay(spot.position_x, spot.position_y, spot.position_z);
            inputPerspective.checked = !!spot.is_perspective;
            inputPerspective.dispatchEvent(new Event('change'));
            inputRx.value = spot.rotation_x || 0; inputRy.value = spot.rotation_y || 0; inputRz.value = spot.rotation_z || 0;
            inputSx.value = spot.scale_x || 1; inputSy.value = spot.scale_y || 1;
            updateLabels();
            
            document.getElementById('input-title').value = spot.title || '';
            document.getElementById('input-target').value = spot.target_scene_id || '';
            document.getElementById('input-desc-id').value = spot.content_id || '';
            document.getElementById('input-desc-en').value = spot.content_en || '';
            // Update narasi preview buttons
            _updateNarasiPreview('id', spot.content_id || '');
            _updateNarasiPreview('en', spot.content_en || '');

            // Load existing assets
            loadExistingAssets(spot.id);

            // Reset new-file rows
            document.getElementById('new-asset-rows').innerHTML = '';
            document.getElementById('asset-upload-wrap').classList.add('hidden');
            document.getElementById('no-asset-hint').classList.remove('hidden');
            currentUploadInfospotId = spot.id;

            methodPut.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            formEl.action = `{{ url('admin/infospots') }}/${spot.id}`; 
            formDelete.action = `{{ url('admin/infospots') }}/${spot.id}`;
            formDelete.classList.remove('hidden');
        }
    }

    window.editInfospot = function(id, spotData) {
        editingId = id;
        viewer.tweenControlCenter(new THREE.Vector3(spotData.position_x, spotData.position_y, spotData.position_z), 500);
        openForm('edit', spotData);
    };

    window.cancelForm = function() {
        createForm.classList.add('hidden');
        listState.classList.remove('hidden');
        titleHeader.innerText = "Inspector";
        editingId = null;
        if(ghostMarker && ghostMarker.parent) panorama.remove(ghostMarker);
        coordDisplay.classList.add('hidden');
    };

    function showInstruction(msg) {
        toastText.innerText = msg;
        toast.classList.remove('opacity-0');
        toast.classList.add('opacity-100');
        setTimeout(() => toast.classList.replace('opacity-100', 'opacity-0'), 4000);
    }

    let isDragging = false, dragMarker = null, windowWasDragging = false;
    
    container.addEventListener('pointermove', (e) => {
        const rect = container.getBoundingClientRect();
        const mouse = new THREE.Vector2(((e.clientX - rect.left) / rect.width) * 2 - 1, -((e.clientY - rect.top) / rect.height) * 2 + 1);
        const raycaster = new THREE.Raycaster();
        raycaster.setFromCamera(mouse, viewer.getCamera());
        
        if (!isDragging) {
            const markers = Object.values(renderedSpots);
            const intersects = raycaster.intersectObjects(markers, true);
            container.style.cursor = (intersects.length > 0) ? 'grab' : 'default';
        } else {
            windowWasDragging = true;
            container.style.cursor = 'grabbing';
            if (viewer.getControl()) viewer.getControl().enabled = false;
            const panoramaIntersects = raycaster.intersectObject(panorama, false);
            if (panoramaIntersects.length > 0) {
                const p = panoramaIntersects[0].point;
                const x = Math.round(p.x), y = Math.round(p.y), z = Math.round(p.z);
                dragMarker.position.set(x, y, z);
                updatePosDisplay(x, y, z);
                pos_x.value = x; pos_y.value = y; pos_z.value = z;
            }
        }
    });

    container.addEventListener('pointerdown', (e) => {
        if (e.button !== 0) return;
        const rect = container.getBoundingClientRect();
        const mouse = new THREE.Vector2(((e.clientX - rect.left) / rect.width) * 2 - 1, -((e.clientY - rect.top) / rect.height) * 2 + 1);
        const raycaster = new THREE.Raycaster();
        raycaster.setFromCamera(mouse, viewer.getCamera());
        const intersects = raycaster.intersectObjects(Object.values(renderedSpots), true);
        if (intersects.length > 0) {
            let hit = intersects[0].object;
            let found = null;
            let curr = hit;
            while(curr && !found) {
                for(const id in renderedSpots) { if(renderedSpots[id] === curr) { found = renderedSpots[id]; break; } }
                curr = curr.parent;
            }
            if (found) {
                isDragging = true; windowWasDragging = false; dragMarker = found;
                if (viewer.getControl()) viewer.getControl().enabled = false;
                
                // Hentikan bounce saat sedang di-drag agar tidak "melawan"
                if (dragMarker.bounceTween) {
                    dragMarker.bounceTween.stop();
                }
            }
        }
    });

    window.addEventListener('pointerup', () => {
        if (isDragging) {
            // Jalankan kembali bounce setelah drag selesai dengan titik Y yang baru
            if (dragMarker) {
                addBounce(dragMarker);
            }

            isDragging = false; dragMarker = null;
            if (viewer.getControl()) viewer.getControl().enabled = true;
            showInstruction("POSITION FIXED.");
        }
    });

    // Initialize Choices.js
    const targetSelect = document.getElementById('input-target');
    const choices = new Choices(targetSelect, {
        searchEnabled: true,
        itemSelectText: '',
        shouldSort: false,
        placeholder: true,
        placeholderValue: 'Select destination...'
    });

    // Quick Create Toggle
    window.toggleQuickCreate = function() {
        const panel = document.getElementById('quick-create-panel');
        panel.classList.toggle('hidden');
    };

    // Quick Create Execution
    document.getElementById('btn-do-quick-create').addEventListener('click', async () => {
        const name = document.getElementById('qc-name').value;
        const image = document.getElementById('qc-image').files[0];
        const loading = document.getElementById('qc-loading');
        const btn = document.getElementById('btn-do-quick-create');

        if (!name || !image) {
            alert('Please provide a name and a 360° image.');
            return;
        }

        const formData = new FormData();
        formData.append('name', name);
        formData.append('image', image);
        formData.append('_token', '{{ csrf_token() }}');

        loading.classList.remove('hidden');
        btn.classList.add('hidden');

        try {
            const response = await fetch("{{ route('admin.scenes.store') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            if (data.success) {
                // Add to Choices list
                choices.setChoices([{
                    value: data.scene.id,
                    label: data.scene.name,
                    selected: true
                }], 'value', 'label', false);

                showInstruction("SCENE INITIALIZED & LINKED.");
                toggleQuickCreate();
                
                // Clear form
                document.getElementById('qc-name').value = '';
                document.getElementById('qc-image').value = '';
            } else {
                alert('Upload failed: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error(error);
            alert('An error occurred during scene initialization.');
        } finally {
            loading.classList.add('hidden');
            btn.classList.remove('hidden');
        }
    });

    setTimeout(() => showInstruction("RIGHT-CLICK FOR CONTEXT OVERLAY."), 1000);

    /* ---- Lock Initial View ---- */
    // Show locked state if scene already has a saved view
    (function initLockBtn() {
        const savedLon = {{ $scene->initial_lon ?? 0 }};
        const savedLat = {{ $scene->initial_lat ?? 0 }};
        if (savedLon !== 0 || savedLat !== 0) {
            _setLockState(true, savedLon, savedLat);
        }
    })();

    function _setLockState(locked, lon, lat) {
        const btn   = document.getElementById('btn-lock-view');
        const icon  = document.getElementById('lock-icon');
        const label = document.getElementById('lock-label');
        if (locked) {
            btn.classList.add('border-indigo-500/60', 'text-indigo-300');
            btn.classList.remove('border-white/10', 'text-white/60');
            icon.className  = 'fas fa-lock text-[10px] text-indigo-400';
            label.innerText = `Locked (${lon?.toFixed(1)}° / ${lat?.toFixed(1)}°)`;
        } else {
            btn.classList.remove('border-indigo-500/60', 'text-indigo-300');
            btn.classList.add('border-white/10', 'text-white/60');
            icon.className  = 'fas fa-lock-open text-[10px]';
            label.innerText = 'Lock View';
        }
    }

    window.lockInitialView = async function() {
        // Read the actual camera direction from Three.js (more reliable than controls.lon/lat)
        const camera = viewer.getCamera();
        const dir    = new THREE.Vector3();
        camera.getWorldDirection(dir);

        // Panolens OrbitControls convention:
        //   target.x = sin(phi)*cos(theta)  →  dir.x = cos(lat)*cos(lon)
        //   target.y = cos(phi)              →  dir.y = sin(lat)
        //   target.z = sin(phi)*sin(theta)  →  dir.z = cos(lat)*sin(lon)
        // Therefore: lon = atan2(dir.z, dir.x), lat = asin(dir.y)
        const lon = Math.atan2(dir.z, dir.x) * (180 / Math.PI);
        const lat = Math.asin(Math.max(-1, Math.min(1, dir.y))) * (180 / Math.PI);
        console.log('[LockView] lon=', lon.toFixed(2), 'lat=', lat.toFixed(2));

        const btn = document.getElementById('btn-lock-view');
        btn.disabled = true;
        const origLabel = document.getElementById('lock-label').innerText;
        document.getElementById('lock-label').innerText = 'Saving...';

        try {
            const res = await fetch('{{ route('admin.scenes.lockView', $scene) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ lon, lat })
            });
            const data = await res.json();
            if (data.success) {
                _setLockState(true, data.lon, data.lat);
                showInstruction(`VIEW LOCKED — LON: ${lon.toFixed(1)}° LAT: ${lat.toFixed(1)}°`);
            }
        } catch(e) {
            document.getElementById('lock-label').innerText = origLabel;
            alert('Failed to save view.');
        } finally {
            btn.disabled = false;
        }
    };
</script>

<!-- ===== Quill Rich Text Editor Popup ===== -->
<div id="quill-popup-overlay">
    <div id="quill-popup-box">
        <div id="quill-popup-header">
            <div id="quill-popup-title">
                <i class="fas fa-pen-to-square" style="color:#6366f1"></i>
                <span id="quill-popup-title-text">Edit Narration</span>
            </div>
            <button id="quill-popup-close" onclick="closeQuillEditor()">&#10005;</button>
        </div>
        <div id="quill-popup-editor">
            <div id="quill-container"></div>
        </div>
        <div id="quill-popup-footer">
            <button class="ql-popup-btn ql-popup-btn-cancel" onclick="closeQuillEditor()">Cancel</button>
            <button class="ql-popup-btn ql-popup-btn-apply" onclick="applyQuillContent()"><i class="fas fa-check mr-1"></i> Apply</button>
        </div>
    </div>
</div>

<script>
    /* ---- Quill Editor Popup Logic ---- */
    let _quill = null;
    let _quillLang = null; // 'id' or 'en'

    // Init Quill once DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        _quill = new Quill('#quill-container', {
            theme: 'snow',
            placeholder: 'Write your narration here...',
            modules: {
                toolbar: [
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link'],
                    ['clean']
                ]
            }
        });
    });

    window.openQuillEditor = function(lang) {
        _quillLang = lang;
        const textarea = document.getElementById('input-desc-' + lang);
        const isId = lang === 'id';

        // Set popup title
        document.getElementById('quill-popup-title-text').innerText =
            isId ? '🇮🇩 Narasi Indonesia' : '🇬🇧 English Narration';

        // Load existing content into Quill
        const html = textarea.value || '';
        _quill.root.innerHTML = html;

        // Show popup
        document.getElementById('quill-popup-overlay').classList.add('open');
        setTimeout(() => _quill.focus(), 100);
    };

    window.closeQuillEditor = function() {
        document.getElementById('quill-popup-overlay').classList.remove('open');
    };

    window.applyQuillContent = function() {
        if (!_quillLang) return;
        const html = _quill.root.innerHTML;

        // Write to hidden textarea
        document.getElementById('input-desc-' + _quillLang).value = html;

        // Update preview button text (strip tags for preview)
        const plain = _quill.getText().trim();
        const btnId = 'btn-open-editor-' + _quillLang;
        const prevId = 'preview-' + _quillLang;
        const btn    = document.getElementById(btnId);
        const prev   = document.getElementById(prevId);

        if (plain) {
            prev.innerText = plain.substring(0, 80) + (plain.length > 80 ? '...' : '');
            btn.classList.add('has-content');
        } else {
            prev.innerText = _quillLang === 'id' ? 'Klik untuk menulis narasi...' : 'Click to write narration...';
            btn.classList.remove('has-content');
        }

        closeQuillEditor();
    };

    // Close on overlay click
    document.getElementById('quill-popup-overlay').addEventListener('click', (e) => {
        if (e.target === document.getElementById('quill-popup-overlay')) closeQuillEditor();
    });

    // Helper: update preview button after loading spot
    window._updateNarasiPreview = function(lang, html) {
        const tmp   = document.createElement('div');
        tmp.innerHTML = html || '';
        const plain = (tmp.innerText || '').trim();
        const btn   = document.getElementById('btn-open-editor-' + lang);
        const prev  = document.getElementById('preview-' + lang);
        if (!btn || !prev) return;
        if (plain) {
            prev.innerText = plain.substring(0, 80) + (plain.length > 80 ? '...' : '');
            btn.classList.add('has-content');
        } else {
            prev.innerText = lang === 'id' ? 'Klik untuk menulis narasi...' : 'Click to write narration...';
            btn.classList.remove('has-content');
        }
    }
</script>

@endsection
