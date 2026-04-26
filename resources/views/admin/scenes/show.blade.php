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
                            <span
                                class="text-[8px] font-bold text-slate-500 uppercase tracking-widest leading-none">Workspace</span>
                            <span class="text-white font-bold text-xs mt-1">{{ $scene->name }}</span>
                        </div>
                        <div class="w-px h-6 bg-white/10"></div>
                        <div class="flex items-center gap-2">
                            <div
                                class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.5)]">
                            </div>
                            <span class="text-[9px] font-bold text-white/50 uppercase tracking-widest">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top-right overlay: coord display + lock button -->
            <div class="absolute top-4 right-4 z-10 flex items-center gap-2">
                <!-- Small Coordinates Display (shown during node placement) -->
                <div id="coord-display"
                    class="hidden bg-[#1a1a1a]/80 backdrop-blur border border-white/5 px-4 py-2 rounded-lg text-white font-mono text-[9px] tracking-widest shadow-xl">
                    POS: <span class="text-blue-400">X: 0 | Y: 0 | Z: 0</span>
                </div>

                <!-- Lock Initial View Button -->
                <button id="btn-lock-view" onclick="lockInitialView()" title="Lock initial camera direction for this scene"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-[9px] font-bold uppercase tracking-widest shadow-xl transition-all duration-200 select-none
                       bg-[#1a1a1a]/80 backdrop-blur border border-white/10 text-white/60 hover:border-indigo-500/50 hover:text-white">
                    <i class="fas fa-lock-open text-[10px]" id="lock-icon"></i>
                    <span id="lock-label">Lock View</span>
                </button>
            </div>

            <!-- Instructional Toast -->
            <div id="instruction-toast"
                class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 px-6 py-3 bg-white border border-slate-200 rounded-lg shadow-2xl transition-all duration-500 opacity-0 translate-y-4 pointer-events-none">
                <p class="text-slate-900 text-[9px] font-bold uppercase tracking-widest flex items-center gap-3">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    <span id="toast-text">Use right-click to add points</span>
                </p>
            </div>
        </div>

        <!-- Professional Inspector (Right Sidebar) -->
        <aside id="sidebar-panel"
            class="w-[340px] bg-white border-l border-slate-200 flex flex-col shadow-2xl z-20 overflow-hidden">
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
                        <span class="bg-blue-600 text-white text-[9px] font-bold px-2 py-0.5 rounded"
                            id="point-count-badge">{{ count($scene->infospots) }}</span>
                    </div>

                    <div class="space-y-1.5" id="infospots-list-container">
                        @forelse($scene->infospots as $spot)
                            <button id="spot-card-{{ $spot->id }}"
                                onclick="editInfospot({{ $spot->id }}, {{ json_encode($spot) }})"
                                class="spot-card-btn w-full flex items-center justify-between p-3 bg-white border border-slate-100 rounded-lg hover:border-blue-500 hover:bg-blue-50/10 transition-all group text-left shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 {{ $spot->type == 'info' ? 'bg-blue-50 text-blue-600' : ($spot->type == '3d' ? 'bg-purple-50 text-purple-600' : 'bg-slate-100 text-slate-600') }} rounded flex items-center justify-center text-[10px] shadow-inner">
                                        <i
                                            class="fas {{ $spot->type == 'info' ? 'fa-info' : ($spot->type == '3d' ? 'fa-cube' : 'fa-location-arrow') }}"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span
                                            class="text-xs font-bold text-slate-800 truncate max-w-[150px]">{{ $spot->title ?? ($spot->type == 'info' ? 'Info Node' : 'Nav Node') }}</span>
                                        <span class="text-[8px] text-slate-400 font-bold uppercase tracking-widest">X:
                                            {{ round($spot->position_x) }} Y: {{ round($spot->position_y) }}</span>
                                    </div>
                                </div>
                                <i
                                    class="fas fa-chevron-right text-[8px] text-slate-300 group-hover:translate-x-1 transition-transform"></i>
                            </button>
                        @empty
                            <div class="py-12 text-center bg-slate-50 rounded-lg border border-dashed border-slate-200">
                                <i class="fas fa-mouse-pointer text-slate-200 text-xl mb-3"></i>
                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest px-6">Right-click on
                                    the 3D viewport to place nodes.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Form Mode -->
                <div id="state-form" style="display: none" class="animate-fade-in space-y-6">
                    <form id="infospot-form" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div id="method-put"></div>

                        <!-- 1. Identity & Content Section -->
                        <div id="fields-info" class="space-y-6">
                            <label
                                class="text-[9px] font-bold text-slate-400 uppercase tracking-[2px] block border-b border-slate-100 pb-2">Content
                                Details</label>

                            <div class="space-y-4">
                                <!-- Node Title -->
                                <div class="space-y-1.5">
                                    <label class="text-[9px] font-bold text-slate-500 uppercase tracking-widest ml-0.5">Node
                                        Title</label>
                                    <input type="text" name="title" id="input-title" class="modern-input"
                                        placeholder="Enter node title...">
                                    <p class="text-[7px] text-slate-500 italic px-1">Note: In Single Product mode, node
                                        title will be used as product name.</p>
                                </div>

                                <!-- Single Product Content Wrapper (Shown if is_multi == false) -->
                                <div id="single-product-wrapper" class="space-y-4 animate-fade-in hidden">
                                    <div class="grid grid-cols-1 gap-2">
                                        <div class="space-y-1.5">
                                            <button type="button" onclick="openTabbedQuill('desc', 'single')"
                                                id="btn-open-desc-single" class="narasi-btn group">
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="w-6 h-6 rounded bg-indigo-50 flex items-center justify-center text-indigo-500 text-[10px]">
                                                        <i class="fas fa-align-left"></i></div>
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="text-[7px] font-bold text-slate-800 uppercase tracking-widest">Description
                                                            (ID/EN)</span>
                                                        <p id="preview-desc-single" class="preview-text">Klik untuk menulis
                                                            narasi...</p>
                                                    </div>
                                                </div>
                                                <i
                                                    class="fas fa-chevron-right text-[8px] text-slate-300 group-hover:text-indigo-400"></i>
                                            </button>
                                            <textarea id="product-desc-id" name="product_desc_id" class="hidden"></textarea>
                                            <textarea id="product-desc-en" name="product_desc_en" class="hidden"></textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <button type="button" onclick="openQuillEditor('researcher', 'single')"
                                                id="btn-open-researcher-single" class="narasi-btn group">
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="w-6 h-6 rounded bg-emerald-50 flex items-center justify-center text-emerald-500 text-[10px]">
                                                        <i class="fas fa-user-tie"></i></div>
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="text-[7px] font-bold text-slate-800 uppercase tracking-widest">Peneliti</span>
                                                        <p id="preview-researcher-single" class="preview-text">Klik...</p>
                                                    </div>
                                                </div>
                                            </button>
                                            <textarea id="product-researcher" name="product_researcher" class="hidden"></textarea>

                                            <button type="button" onclick="openQuillEditor('contact', 'single')"
                                                id="btn-open-contact-single" class="narasi-btn group">
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="w-6 h-6 rounded bg-sky-50 flex items-center justify-center text-sky-500 text-[10px]">
                                                        <i class="fas fa-address-book"></i></div>
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="text-[7px] font-bold text-slate-800 uppercase tracking-widest">Kontak</span>
                                                        <p id="preview-contact-single" class="preview-text">Klik...</p>
                                                    </div>
                                                </div>
                                            </button>
                                            <textarea id="product-contact" name="product_contact" class="hidden"></textarea>
                                        </div>
                                    </div>

                                    <!-- Asset Panel for Single Product -->
                                    <div id="single-product-assets"
                                        class="p-3 bg-slate-900 rounded border border-slate-800 shadow-inner space-y-2">
                                        <div class="flex items-center justify-between mb-2">
                                            <label
                                                class="text-[8px] font-bold text-slate-500 uppercase tracking-widest">Media
                                                Assets</label>
                                            <button type="button" onclick="addNewAssetRow('single')"
                                                class="text-[7px] font-bold bg-slate-700 hover:bg-slate-600 text-slate-300 px-2 py-1 rounded tracking-widest uppercase transition-colors flex items-center gap-1">
                                                <i class="fas fa-plus"></i> Add File
                                            </button>
                                        </div>
                                        <div id="single-existing-assets" class="space-y-1.5 hidden"></div>
                                        <div id="single-new-assets" class="space-y-2"></div>
                                        <p id="single-no-asset-hint"
                                            class="text-[8px] text-slate-600 italic text-center py-2">Klik &quot;+ Add
                                            File&quot; untuk upload.</p>

                                        <div id="single-upload-wrap" class="hidden pt-2 border-t border-slate-800">
                                            <button type="button" id="btn-upload-single-assets"
                                                class="w-full py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[8px] font-bold uppercase tracking-widest rounded transition-colors flex items-center justify-center gap-1.5">
                                                <i class="fas fa-cloud-upload-alt"></i> Upload Files
                                            </button>
                                            <div id="single-upload-loading"
                                                class="hidden items-center justify-center gap-2 py-1">
                                                <div
                                                    class="w-3 h-3 border-2 border-slate-600 border-t-indigo-500 rounded-full animate-spin">
                                                </div>
                                                <span
                                                    class="text-[8px] text-slate-400 uppercase tracking-widest">Uploading...</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!-- Multi Product Groups Panel (Shown if is_multi == true) -->
                                <div id="multi-product-wrapper"
                                    class="space-y-2 p-3 bg-slate-900 rounded border border-slate-800 shadow-inner hidden">
                                    <div class="flex items-center justify-between mb-2">
                                        <label
                                            class="text-[8px] font-bold text-slate-500 uppercase tracking-widest">Product
                                            Groups</label>
                                        <button type="button" id="btn-add-product"
                                            class="text-[7px] font-bold bg-slate-700 hover:bg-slate-600 text-slate-300 px-2 py-1 rounded tracking-widest uppercase transition-colors flex items-center gap-1">
                                            <i class="fas fa-plus"></i> New Product
                                        </button>
                                    </div>
                                    <div id="product-list" class="space-y-1.5">
                                        <p class="text-[7px] text-slate-600 italic text-center py-1">No products yet.</p>
                                    </div>

                                    <!-- Product Form (Hidden) -->
                                    <div id="product-form-wrap"
                                        class="hidden space-y-2 pt-2 border-t border-slate-800 animate-fade-in">
                                        <input type="hidden" id="edit-product-id">
                                        <input type="text" id="product-name" placeholder="Nama Produk"
                                            class="w-full bg-slate-800 border border-slate-700 text-slate-300 text-[8px] rounded px-2 py-1.5 focus:outline-none focus:border-indigo-500">
                                        <div class="space-y-2">
                                            <button type="button" onclick="openTabbedQuill('desc', 'multi')"
                                                id="btn-open-desc-multi"
                                                class="narasi-btn group !bg-slate-800 !border-slate-700 !text-slate-300">
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="w-6 h-6 rounded bg-slate-700 flex items-center justify-center text-indigo-400 text-[10px]">
                                                        <i class="fas fa-align-left"></i></div>
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="text-[7px] font-bold text-slate-400 uppercase tracking-widest">Description
                                                            (ID/EN)</span>
                                                        <p id="preview-desc-multi" class="preview-text !text-slate-500">
                                                            Klik untuk menulis narasi...</p>
                                                    </div>
                                                </div>
                                                <i
                                                    class="fas fa-chevron-right text-[8px] text-slate-600 group-hover:text-indigo-400"></i>
                                            </button>
                                            <textarea id="product-desc-id-multi" class="hidden"></textarea>
                                            <textarea id="product-desc-en-multi" class="hidden"></textarea>

                                            <div class="grid grid-cols-2 gap-2">
                                                <button type="button" onclick="openQuillEditor('researcher', 'multi')"
                                                    id="btn-open-researcher-multi"
                                                    class="narasi-btn group !bg-slate-800 !border-slate-700 !text-slate-300">
                                                    <div class="flex items-center gap-2">
                                                        <div
                                                            class="w-6 h-6 rounded bg-slate-700 flex items-center justify-center text-emerald-400 text-[10px]">
                                                            <i class="fas fa-user-tie"></i></div>
                                                        <div class="flex flex-col">
                                                            <span
                                                                class="text-[7px] font-bold text-slate-400 uppercase tracking-widest">Peneliti</span>
                                                            <p id="preview-researcher-multi"
                                                                class="preview-text !text-slate-500">Klik...</p>
                                                        </div>
                                                    </div>
                                                </button>
                                                <textarea id="product-researcher-multi" class="hidden"></textarea>

                                                <button type="button" onclick="openQuillEditor('contact', 'multi')"
                                                    id="btn-open-contact-multi"
                                                    class="narasi-btn group !bg-slate-800 !border-slate-700 !text-slate-300">
                                                    <div class="flex items-center gap-2">
                                                        <div
                                                            class="w-6 h-6 rounded bg-slate-700 flex items-center justify-center text-sky-400 text-[10px]">
                                                            <i class="fas fa-address-book"></i></div>
                                                        <div class="flex flex-col">
                                                            <span
                                                                class="text-[7px] font-bold text-slate-400 uppercase tracking-widest">Kontak</span>
                                                            <p id="preview-contact-multi"
                                                                class="preview-text !text-slate-500">Klik...</p>
                                                        </div>
                                                    </div>
                                                </button>
                                                <textarea id="product-contact-multi" class="hidden"></textarea>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" id="btn-save-product"
                                                class="flex-1 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-[7px] font-bold uppercase tracking-widest rounded transition-colors">Simpan
                                                Produk</button>
                                            <button type="button" id="btn-cancel-product"
                                                class="px-3 py-1 bg-slate-700 hover:bg-slate-600 text-slate-300 text-[7px] font-bold uppercase tracking-widest rounded transition-colors">Batal</button>
                                        </div>

                                        <!-- Media Assets Panel (Nested within product) -->
                                        <div id="product-assets-section"
                                            class="hidden space-y-2 pt-4 border-t border-slate-800 animate-fade-in">
                                            <div class="flex items-center justify-between mb-2">
                                                <label
                                                    class="text-[8px] font-bold text-slate-500 uppercase tracking-widest">Media
                                                    Assets for this Product</label>
                                                <button type="button" id="btn-add-asset-row"
                                                    class="text-[7px] font-bold bg-slate-700 hover:bg-slate-600 text-slate-300 px-2 py-1 rounded tracking-widest uppercase transition-colors flex items-center gap-1">
                                                    <i class="fas fa-plus"></i> Add File
                                                </button>
                                            </div>
                                            <div id="existing-assets-list" class="space-y-1.5 hidden"></div>
                                            <div id="new-asset-rows" class="space-y-2"></div>
                                            <p id="no-asset-hint"
                                                class="text-[8px] text-slate-600 italic text-center py-2">Klik &quot;+ Add
                                                File&quot; untuk upload.</p>
                                            <div id="asset-upload-wrap" class="hidden pt-2 border-t border-slate-800">
                                                <button type="button" id="btn-upload-assets"
                                                    class="w-full py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[8px] font-bold uppercase tracking-widest rounded transition-colors flex items-center justify-center gap-1.5">
                                                    <i class="fas fa-cloud-upload-alt"></i> Upload Files
                                                </button>
                                                <div id="asset-upload-loading"
                                                    class="hidden items-center justify-center gap-2 py-1">
                                                    <div
                                                        class="w-3 h-3 border-2 border-slate-600 border-t-indigo-500 rounded-full animate-spin">
                                                    </div>
                                                    <span
                                                        class="text-[8px] text-slate-400 uppercase tracking-widest">Uploading...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Marker Visual Style -->
                        <div id="wrapper-marker-visual"
                            class="space-y-4 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                            <label class="text-[9px] font-bold text-slate-400 uppercase tracking-[2px] block">Marker
                                Visual</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" onclick="setMarkerType('info')" id="btn-marker-info"
                                    class="marker-type-btn active flex flex-col items-center gap-1.5 p-2 rounded-lg border-2 border-slate-100 bg-white hover:border-blue-200 transition-all">
                                    <div
                                        class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <span
                                        class="text-[8px] font-black uppercase tracking-widest text-slate-500">Icon</span>
                                </button>
                                <button type="button" onclick="setMarkerType('image')" id="btn-marker-image"
                                    class="marker-type-btn flex flex-col items-center gap-1.5 p-2 rounded-lg border-2 border-slate-100 bg-white hover:border-blue-200 transition-all">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600">
                                        <i class="fas fa-image"></i>
                                    </div>
                                    <span class="text-[8px] font-black uppercase tracking-widest text-slate-500">2D
                                        Image</span>
                                </button>
                                <button type="button" onclick="setMarkerType('3d')" id="btn-marker-3d"
                                    class="marker-type-btn flex flex-col items-center gap-1.5 p-2 rounded-lg border-2 border-slate-100 bg-white hover:border-blue-200 transition-all">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                                        <i class="fas fa-cube"></i>
                                    </div>
                                    <span class="text-[8px] font-black uppercase tracking-widest text-slate-500">3D
                                        Object</span>
                                </button>
                            </div>

                            <!-- 2D Image Upload -->
                            <div id="marker-image-upload" class="hidden space-y-2 mt-4 animate-fade-in">
                                <label
                                    class="text-[8px] font-bold text-orange-500 uppercase tracking-widest block ml-0.5">Marker
                                    Image (2D)</label>
                                <input type="file" name="marker_image" id="input-marker-image" accept="image/*"
                                    class="w-full text-[9px] text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-[9px] file:font-bold file:bg-orange-600 file:text-white hover:file:bg-orange-700 transition-all cursor-pointer">
                                <p class="text-[7px] text-slate-500 italic">Pilih gambar 2D untuk digunakan sebagai penanda
                                    di panorama.</p>
                            </div>

                            <!-- 3D Model Upload -->
                            <div id="fields-3d" class="hidden space-y-2 mt-4 animate-fade-in">
                                <label
                                    class="text-[8px] font-bold text-indigo-400 uppercase tracking-widest block ml-0.5">Primary
                                    3D Model (Floating Object)</label>
                                <div class="space-y-2">
                                    <input type="file" name="model_file" id="input-model-file" accept=".glb"
                                        class="w-full text-[9px] text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-[9px] file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 transition-all cursor-pointer">
                                    <p class="text-[7px] text-slate-500 italic">Upload file .glb untuk merender objek 3D
                                        langsung di panorama.</p>
                                </div>
                            </div>

                            <!-- Active Asset Info (Shared) -->
                            <div id="current-model-info"
                                class="hidden flex items-center gap-2 p-1.5 mt-4 rounded border animate-fade-in bg-slate-900/40 border-slate-700/50">
                                <div id="current-model-icon-box"
                                    class="w-6 h-6 rounded flex items-center justify-center text-[10px]">
                                    <i id="current-model-icon" class="fas fa-cube"></i>
                                </div>
                                <span id="current-model-name" class="text-[8px] text-slate-200 truncate flex-1"></span>
                                <span
                                    class="text-[7px] text-slate-400 font-bold uppercase tracking-widest px-1.5 py-0.5 bg-slate-800 rounded">Active</span>
                            </div>
                        </div>

                        <style>
                            .marker-type-btn.active {
                                border-color: #3b82f6 !important;
                                background-color: #eff6ff !important;
                                ring: 4px;
                                ring-color: #dbeafe;
                                box-shadow: 0 0 0 4px #dbeafe !important;
                            }

                            .marker-type-btn.active span {
                                color: #2563eb !important;
                            }

                            .marker-type-btn.active .w-8 {
                                background-color: #3b82f6 !important;
                                color: #ffffff !important;
                            }

                            .marker-type-btn.active i {
                                color: #ffffff !important;
                            }

                            .spot-card-btn.active {
                                border-color: #2563eb !important;
                                background-color: #eff6ff !important;
                                box-shadow: 0 0 0 4px #dbeafe !important;
                            }
                        </style>

                        <!-- 3. Navigation Section -->
                        <div id="fields-nav" class="hidden space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <label class="text-[9px] font-bold text-slate-400 uppercase tracking-[2px]">Routing</label>
                                <button type="button" onclick="toggleQuickCreate()"
                                    class="text-[8px] font-bold text-blue-600 hover:text-blue-700 uppercase tracking-widest flex items-center gap-1.5 transition-colors">
                                    <i class="fas fa-plus-circle"></i> Quick Create
                                </button>
                            </div>

                            <!-- Quick Create Inline Form (Hidden by default) -->
                            <div id="quick-create-panel"
                                class="hidden bg-slate-900 p-4 rounded-lg border border-slate-800 space-y-4 animate-fade-in shadow-xl">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <span class="text-[8px] font-bold text-white/50 uppercase tracking-[2px]">New Buffer
                                        Entry</span>
                                    <button type="button" onclick="toggleQuickCreate()"
                                        class="text-white/30 hover:text-white"><i
                                            class="fas fa-times text-[10px]"></i></button>
                                </div>

                                <div class="space-y-3">
                                    <div class="space-y-1.5">
                                        <label class="text-[8px] font-bold text-slate-500 uppercase">Buffer Name</label>
                                        <input type="text" id="qc-name"
                                            class="modern-input bg-slate-800 border-slate-700 text-white placeholder-slate-600"
                                            placeholder="e.g. Garden View">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label
                                            class="text-[8px] font-bold text-slate-500 uppercase tracking-widest leading-none">Visual
                                            Data (360° Image)</label>
                                        <input type="file" id="qc-image" accept="image/*"
                                            class="w-full text-[9px] text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-[9px] file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition-all cursor-pointer">
                                    </div>
                                    <button type="button" id="btn-do-quick-create"
                                        class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[9px] font-bold uppercase tracking-widest rounded shadow-lg shadow-emerald-500/10 transition-all flex items-center justify-center gap-2">
                                        <i class="fas fa-cloud-upload-alt"></i> Initialize Scene
                                    </button>
                                </div>
                                <div id="qc-loading" class="hidden flex items-center justify-center gap-3">
                                    <div
                                        class="w-3 h-3 border-2 border-slate-600 border-t-blue-500 rounded-full animate-spin">
                                    </div>
                                    <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Processing
                                        Buffer...</span>
                                </div>
                            </div>

                            <div class="space-y-1.5 group-choices">
                                <label class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Target
                                    Scene</label>
                                <select name="target_scene_id" id="input-target" class="modern-input">
                                    <option value="">Select destination...</option>
                                    @foreach ($hasTargetScenes as $ts)
                                        <option value="{{ $ts->id }}">{{ $ts->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 4. Perspective & Calibration (3D Transform) -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <label class="text-[9px] font-bold text-slate-400 uppercase tracking-[2px]">3D
                                    Properties</label>
                                <input type="hidden" name="type" id="input-type">
                                <input type="hidden" name="is_multi" id="input-is-multi" value="0">
                            </div>

                            <div class="space-y-3">
                                <div
                                    class="p-3 bg-slate-50 border border-slate-200 rounded flex items-center justify-between shadow-sm">
                                    <div>
                                        <label
                                            class="block text-slate-800 font-bold text-[9px] uppercase tracking-widest">3D
                                            Perspective</label>
                                        <p class="text-[8px] text-slate-400 font-bold uppercase tracking-wider">Surface
                                            alignment</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_perspective" id="input-perspective"
                                            value="1" class="sr-only peer">
                                        <div
                                            class="w-8 h-4 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:rounded-full after:h-2 after:w-2 after:transition-all peer-checked:bg-blue-600 shadow-inner">
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Calibration Sliders -->
                            <div id="transformation-controls" class="hidden space-y-3 animate-fade-in">
                                <div class="space-y-4 p-3 bg-slate-50 border border-slate-200 rounded shadow-inner">
                                    <div class="space-y-3">
                                        <div class="flex justify-between text-[8px] font-bold text-slate-500 uppercase">
                                            <span>Rotate X</span><span id="label-rx"
                                                class="text-blue-600 font-mono">0°</span></div>
                                        <input type="range" name="rotation_x" id="input-rx" min="-3.14"
                                            max="3.14" step="0.01" value="0" class="inspector-slider">

                                        <div class="flex justify-between text-[8px] font-bold text-slate-500 uppercase">
                                            <span>Rotate Y</span><span id="label-ry"
                                                class="text-blue-600 font-mono">0°</span></div>
                                        <input type="range" name="rotation_y" id="input-ry" min="-3.14"
                                            max="3.14" step="0.01" value="0" class="inspector-slider">

                                        <div class="flex justify-between text-[8px] font-bold text-slate-500 uppercase">
                                            <span>Rotate Z</span><span id="label-rz"
                                                class="text-blue-600 font-mono">0°</span></div>
                                        <input type="range" name="rotation_z" id="input-rz" min="-3.14"
                                            max="3.14" step="0.01" value="0" class="inspector-slider">
                                    </div>

                                    <div class="grid grid-cols-2 gap-3 border-t border-slate-200 pt-3 mt-1">
                                        <div class="space-y-1.5">
                                            <div
                                                class="flex justify-between text-[8px] font-bold text-slate-500 uppercase">
                                                <span>Scale X</span><span id="label-sx"
                                                    class="text-emerald-600 font-mono">0.1</span></div>
                                            <input type="range" name="scale_x" id="input-sx" min="0.01"
                                                max="20" step="0.01" value="0.1" class="inspector-slider">
                                        </div>
                                        <div class="space-y-1.5">
                                            <div
                                                class="flex justify-between text-[8px] font-bold text-slate-500 uppercase">
                                                <span>Scale Y</span><span id="label-sy"
                                                    class="text-emerald-600 font-mono">0.1</span></div>
                                            <input type="range" name="scale_y" id="input-sy" min="0.01"
                                                max="20" step="0.01" value="0.1" class="inspector-slider">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Coordinate Monitoring Section -->
                        <div class="p-4 bg-slate-900 border border-slate-800 rounded-lg flex flex-col gap-4">
                            <span
                                class="text-[8px] font-bold text-slate-500 uppercase tracking-[2px] border-b border-slate-800 pb-2">Coordinates
                                Monitoring</span>
                            <div class="grid grid-cols-3 gap-4 font-mono text-[9px] font-bold">
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex justify-between items-center text-white/40"><span
                                            class="text-blue-500">X</span><span id="val_x">0</span></div>
                                    <div class="h-0.5 bg-slate-800 rounded-full overflow-hidden">
                                        <div id="bar_x" class="h-full bg-blue-600 transition-all duration-300"
                                            style="width: 0%"></div>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex justify-between items-center text-white/40"><span
                                            class="text-emerald-500">Y</span><span id="val_y">0</span></div>
                                    <div class="h-0.5 bg-slate-800 rounded-full overflow-hidden">
                                        <div id="bar_y" class="h-full bg-emerald-600 transition-all duration-300"
                                            style="width: 0%"></div>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex justify-between items-center text-white/40"><span
                                            class="text-indigo-500">Z</span><span id="val_z">0</span></div>
                                    <div class="h-0.5 bg-slate-800 rounded-full overflow-hidden">
                                        <div id="bar_z" class="h-full bg-indigo-600 transition-all duration-300"
                                            style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between text-[8px] font-bold text-slate-500 uppercase"><span>Fine-tune
                                    Position X</span><span id="label-px" class="text-blue-600 font-mono">0</span></div>
                            <input type="range" id="slider-px" min="-10000" max="10000" step="10"
                                class="inspector-slider">

                            <div class="flex justify-between text-[8px] font-bold text-slate-500 uppercase"><span>Fine-tune
                                    Position Y</span><span id="label-py" class="text-emerald-600 font-mono">0</span>
                            </div>
                            <input type="range" id="slider-py" min="-10000" max="10000" step="10"
                                class="inspector-slider">

                            <div class="flex justify-between text-[8px] font-bold text-slate-500 uppercase"><span>Fine-tune
                                    Position Z</span><span id="label-pz" class="text-indigo-600 font-mono">0</span></div>
                            <input type="range" id="slider-pz" min="-10000" max="10000" step="10"
                                class="inspector-slider">
                        </div>

                        <input type="hidden" name="position_x" id="pos_x">
                        <input type="hidden" name="position_y" id="pos_y">
                        <input type="hidden" name="position_z" id="pos_z">

                        <!-- Actions Bar (Sticky bottom) -->
                        <div class="pt-6 flex flex-col gap-2">
                            <button type="submit" id="btn-submit-infospot"
                                class="w-full bg-blue-600 text-white font-bold py-3 rounded text-[9px] uppercase tracking-widest shadow-lg hover:bg-blue-700 transition-all">Update
                                Database</button>
                            <button type="button" onclick="cancelForm()"
                                class="w-full bg-white text-slate-400 font-bold py-3 rounded text-[9px] uppercase tracking-widest border border-slate-200 hover:text-slate-950 transition-all">Cancel
                                Edit</button>
                        </div>
                    </form>

                    <!-- Delete Action -->
                    <form id="form-delete" method="POST" class="mt-8 pt-6 border-t border-slate-100 hidden">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Purge node?');"
                            class="w-full flex items-center justify-center gap-2 text-rose-500 font-bold text-[9px] uppercase tracking-widest py-3 rounded border border-rose-50 hover:bg-rose-50 transition-all">
                            <i class="fas fa-trash-alt"></i> Purge From Memory
                        </button>
                    </form>
                </div>
            </div>
        </aside>
    </div>

    <!-- Pro Context Menu (Dark) -->
    <div id="context-menu"
        class="hidden fixed z-[999] bg-[#1a1a1a] border border-white/5 rounded shadow-2xl py-2 w-48 text-white font-bold text-[9px] uppercase tracking-widest">
        <div id="menu-add-info">
            <button onclick="handleMenuAction('add_info')"
                class="w-full text-left px-5 py-3 hover:bg-blue-600 flex items-center justify-between group transition-colors">
                <span>Add Single Produk</span> <i class="fas fa-plus opacity-30 group-hover:opacity-100"></i>
            </button>
        </div>
        <div id="menu-add-multi">
            <button onclick="handleMenuAction('add_multi')"
                class="w-full text-left px-5 py-3 hover:bg-emerald-600 flex items-center justify-between group border-t border-white/5 transition-colors">
                <span>Add Multi Produk</span> <i class="fas fa-th-large opacity-30 group-hover:opacity-100"></i>
            </button>
        </div>
        <div id="menu-add-nav">
            <button onclick="handleMenuAction('add_nav')"
                class="w-full text-left px-5 py-3 hover:bg-blue-600 flex items-center justify-between group border-t border-white/5 transition-colors">
                <span>Add Nav Link</span> <i class="fas fa-link opacity-30 group-hover:opacity-100"></i>
            </button>
        </div>
        <div id="menu-divider" class="h-px bg-white/5 my-1 mx-2"></div>
        <div id="menu-edit">
            <button onclick="handleMenuAction('edit')"
                class="w-full text-left px-5 py-3 hover:bg-slate-700 flex items-center justify-between group transition-colors">
                <span>Edit Properties</span> <i class="fas fa-cog opacity-30 group-hover:opacity-100"></i>
            </button>
        </div>
        <div id="menu-delete">
            <button onclick="handleMenuAction('delete')"
                class="w-full text-left px-5 py-3 hover:bg-rose-600 text-rose-400 hover:text-white flex items-center justify-between group border-t border-white/5 transition-colors">
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

            .animate-fade-in {
                animation: fadeIn 0.3s ease-out forwards;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            /* Choices.js Custom Overrides for Professional UI */
            .choices {
                margin-bottom: 0;
            }

            .choices__inner {
                @apply min-h-[38px] bg-white border border-slate-200 rounded !px-3 !py-1 flex items-center text-xs font-semibold text-slate-800 transition-all;
            }

            .choices__input {
                @apply bg-transparent text-xs font-semibold text-slate-800 p-0;
            }

            .choices__list--dropdown {
                @apply rounded-lg shadow-2xl border border-slate-200 z-[9999];
            }

            .choices__list--dropdown .choices__item--selectable.is-highlighted {
                @apply bg-blue-600 text-white;
            }

            .choices[data-type*="select-one"]::after {
                @apply border-t-slate-400;
            }
        }
    </style>

    <style>
        /* ---- Quill Editor Popup ---- */
        #quill-popup-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(6px);
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
        }

        #quill-popup-overlay.open {
            display: flex;
        }

        #quill-popup-box {
            background: #0f172a;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            width: min(820px, 96vw);
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 40px 80px rgba(0, 0, 0, 0.7);
        }

        #quill-popup-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
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
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, color 0.2s;
        }

        #quill-popup-close:hover {
            background: #ef4444;
            color: #fff;
        }

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
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
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

        #quill-popup-editor .ql-toolbar .ql-stroke {
            stroke: #94a3b8;
        }

        #quill-popup-editor .ql-toolbar button:hover .ql-stroke,
        #quill-popup-editor .ql-toolbar button.ql-active .ql-stroke {
            stroke: #6366f1;
        }

        #quill-popup-editor .ql-toolbar .ql-fill {
            fill: #94a3b8;
        }

        #quill-popup-editor .ql-toolbar button:hover .ql-fill,
        #quill-popup-editor .ql-toolbar button.ql-active .ql-fill {
            fill: #6366f1;
        }

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
            color: #ffffff;
            padding: 20px 24px;
            min-height: 320px;
        }

        #quill-popup-editor .ql-editor.ql-blank::before {
            color: rgba(148, 163, 184, 0.4);
        }

        #quill-popup-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding: 14px 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
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
            background: rgba(255, 255, 255, 0.06);
            color: #94a3b8;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .ql-popup-btn-cancel:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #e2e8f0;
        }

        .ql-popup-btn-apply {
            background: #6366f1;
            color: #fff;
        }

        .ql-popup-btn-apply:hover {
            background: #4f46e5;
        }

        /* Narasi trigger buttons in sidebar */
        .narasi-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1.5px dashed rgba(100, 116, 139, 0.35);
            background: rgba(248, 250, 252, 0.5);
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            text-align: left;
        }

        .narasi-btn:hover {
            border-color: #6366f1;
            background: rgba(99, 102, 241, 0.05);
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
            border-color: rgba(99, 102, 241, 0.4);
            background: rgba(99, 102, 241, 0.04);
        }

        .narasi-btn.has-content .preview-text {
            color: #64748b;
        }

        /* ---- Drag & Drop sort indicators ---- */
        [draggable="true"] {
            transition: opacity 0.15s;
        }

        .drag-over-top {
            border-top: 2px solid #6366f1 !important;
            border-radius: 6px 6px 4px 4px;
        }

        .drag-over-bottom {
            border-bottom: 2px solid #6366f1 !important;
            border-radius: 4px 4px 6px 6px;
        }

        .drag-handle {
            touch-action: none;
        }
    </style>

    <script src="https://pchen66.github.io/js/three/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.105.0/examples/js/loaders/GLTFLoader.js"></script>
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

        // Add Lights for 3D Models
        const ambientLight = new THREE.AmbientLight(0xffffff, 1.2);
        viewer.add(ambientLight);
        const sunLight = new THREE.DirectionalLight(0xffffff, 0.8);
        sunLight.position.set(1000, 2000, 1000);
        viewer.add(sunLight);

        viewer.getControl().zoomSpeed = -1;



        const panorama = new PANOLENS.ImagePanorama('{{ Storage::url($scene->high_res_path) }}');
        viewer.add(panorama);

        // Apply saved initial view in Editor
        panorama.addEventListener('load', () => {
            if (panorama.material) panorama.material.depthWrite = false;

            const lon = {{ $scene->initial_lon ?? 0 }};
            const lat = {{ $scene->initial_lat ?? 0 }};
            if (lon !== 0 || lat !== 0) {
                _applyInitialView(lon, lat, 0); // Jump directly without tween duration
            }
        });

        function _applyInitialView(lon, lat, duration = 500) {
            const toRad = Math.PI / 180;
            const phi = (90 - lat) * toRad;
            const theta = lon * toRad;
            const target = new THREE.Vector3(
                Math.sin(phi) * Math.cos(theta),
                Math.cos(phi),
                Math.sin(phi) * Math.sin(theta)
            ).multiplyScalar(500);
            viewer.tweenControlCenter(target, duration);
        }

        // Initial state vars
        const existingSpots = @json($scene->infospots);
        const renderedSpots = {};
        let isAdding = false;
        let editingId = null;

        // ---- 3D Model Loading & Animation System ----
        const mixers = [];
        const clock = new THREE.Clock();
        let loader3d;
        try {
            loader3d = new THREE.GLTFLoader();
            console.log("3D Loader initialized");
        } catch (e) {
            console.error("GLTFLoader initialization failed:", e);
        }

        function animate3d() {
            requestAnimationFrame(animate3d);
            if (clock) {
                const delta = clock.getDelta();
                mixers.forEach(mixer => mixer.update(delta));
            }

            // Sync 3D/2D model positions and apply visual animations
            const time = Date.now() * 0.002; // Time factor for animations
            Object.values(renderedSpots).forEach(marker => {
                if (marker && (marker.is3DModel || marker.isPerspectiveMesh)) {
                    // If it's a 3D model proxy, sync the modelObj
                    if (marker.is3DModel && marker.modelObj) {
                        marker.modelObj.position.copy(marker.position);

                        if (editingId != marker.spotData.id) {
                            marker.modelObj.position.y += Math.sin(time) * 50;
                            marker.modelObj.rotation.y += 0.005;
                        }
                    }
                    // If it's a 2D Perspective mesh, it doesn't have modelObj, it IS the object
                    else if (marker.isPerspectiveMesh) {
                        if (editingId != marker.spotData.id) {
                            if (!marker.baseY) marker.baseY = marker.position.y;
                            marker.position.y = marker.baseY + Math.sin(time) * 50;

                            // Don't rotate navigation links
                            if (marker.spotData.type !== 'nav') {
                                marker.rotation.y += 0.005;
                            }
                        } else {
                            // While editing, we don't apply visual bounce to avoid fighting with sliders
                            // Just ensure baseY is updated if the object was moved via drag/sliders
                            marker.baseY = marker.position.y;
                        }
                    }
                }
            });
        }
        animate3d();

        async function loadGLB(url, spotData) {
            console.log("Loading GLB from:", url);
            return new Promise((resolve, reject) => {
                if (!loader3d) return reject("GLTFLoader not available");
                loader3d.load(url, (gltf) => {
                    console.log("GLB Loaded successfully:", url);
                    const model = gltf.scene;

                    // Set initial transform (Scale up significantly for world-space visibility)
                    const s = 1000;
                    model.position.set(0, 0, 0);
                    model.rotation.set(spotData.rotation_x || 0, spotData.rotation_y || 0, spotData
                        .rotation_z || 0);
                    model.scale.set(
                        (spotData.scale_x || 0.1) * s,
                        (spotData.scale_y || 0.1) * s,
                        (spotData.scale_z || spotData.scale_x || 0.1) * s
                    );

                    // Base metadata
                    model.is3DModel = true;
                    model.spotData = spotData;

                    // Set maximum renderOrder for "always on top" priority without breaking inner mesh depth
                    model.traverse(node => {
                        if (node.isMesh) {
                            node.renderOrder = 9999;
                        }
                    });


                    // Handle Animations
                    if (gltf.animations && gltf.animations.length > 0) {
                        const mixer = new THREE.AnimationMixer(model);
                        gltf.animations.forEach(clip => mixer.clipAction(clip).play());
                        mixers.push(mixer);
                        model.mixer = mixer;
                    }

                    resolve(model);
                }, undefined, reject);
            });
        }

        // Helper: Create Styled Icon
        async function createStyledIcon(iconContent, color = '#2563eb', rotation = 0, font = 'bold 80px Arial') {
            await document.fonts.ready;
            const canvas = document.createElement('canvas');
            canvas.width = 150;
            canvas.height = 150;
            const ctx = canvas.getContext('2d');
            if (rotation) {
                ctx.translate(75, 75);
                ctx.rotate(rotation * Math.PI / 180);
                ctx.translate(-75, -75);
            }
            ctx.beginPath();
            ctx.arc(75, 75, 60, 0, 2 * Math.PI);
            ctx.fillStyle = color;
            ctx.fill();
            ctx.fillStyle = "white";
            ctx.font = font;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(iconContent, 75, 80);
            return canvas.toDataURL();
        }

        function addBounce(infospot) {
            // Hentikan tween lama jika ada
            if (infospot.bounceTween) {
                infospot.bounceTween.stop();
            }

            const startY = infospot.position.y;
            infospot.bounceTween = new TWEEN.Tween(infospot.position)
                .to({
                    y: startY + 150
                }, 1500)
                .easing(TWEEN.Easing.Quadratic.InOut)
                .repeat(Infinity)
                .yoyo(true)
                .start();
        }

        let ghostMarker = null;
        let iconTextures = {};

        // Render existing spots
        const faFont = '900 80px "Font Awesome 6 Free"';
        Promise.all([
            createStyledIcon('\uf129', '#2563eb', 0, faFont), // info
            createStyledIcon('\uf062', '#4f46e5', 0, faFont), // nav
            createStyledIcon('\uf1b2', '#7c3aed', 0, faFont) // 3d
        ]).then(async ([infoUrl, navUrl, threedUrl]) => {
            iconTextures.info = infoUrl;
            iconTextures.nav = navUrl;
            iconTextures.threed = threedUrl;

            // Render existing spots sequentially to ensure clean loading
            for (const spot of existingSpots) {
                await renderMarker(spot);
            }

            ghostMarker = new PANOLENS.Infospot(600, infoUrl);
            ghostMarker.material.opacity = 0.5;
        });

        async function renderMarker(spotData) {
            if (renderedSpots[spotData.id]) {
                const oldMarker = renderedSpots[spotData.id];
                if (oldMarker.bounceTween) oldMarker.bounceTween.stop();
                if (oldMarker.mixer) {
                    const idx = mixers.indexOf(oldMarker.mixer);
                    if (idx > -1) mixers.splice(idx, 1);
                }
                if (oldMarker.modelObj) {
                    panorama.remove(oldMarker.modelObj);
                }
                panorama.remove(oldMarker);
                delete renderedSpots[spotData.id];
            }

            let iconUrl = iconTextures.info;
            if (spotData.type === 'nav') iconUrl = iconTextures.nav;
            if (spotData.type === '3d') iconUrl = iconTextures.threed;
            if (spotData.type === 'image' && spotData.model_path) iconUrl = '{{ url('storage') }}/' + spotData
                .model_path;

            let marker;
            let modelObj = null;

            // Position Handling:
            // For standard icons, normalize to a sphere.
            // For 3D models/Perspective, use absolute coordinates for precision.
            let pos;
            if (spotData.type === '3d' || spotData.type === 'image' || spotData.is_perspective) {
                pos = new THREE.Vector3(spotData.position_x, spotData.position_y, spotData.position_z);
            } else {
                pos = new THREE.Vector3(spotData.position_x, spotData.position_y, spotData.position_z).normalize()
                    .multiplyScalar(4000);
            }

            // Check for direct 3D model (.glb only) - ENFORCE type '3d'
            if (spotData.type === '3d' && spotData.model_path && spotData.model_path.toLowerCase().endsWith('.glb')) {
                try {
                    const modelUrl = '{{ Storage::url('') }}/' + spotData.model_path;
                    modelObj = await loadGLB(modelUrl, spotData);
                    // Create a Proxy Infospot for interaction (Increase size for easier hover)
                    const transparentPixel =
                        'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
                    marker = new PANOLENS.Infospot(2000, transparentPixel);
                    marker.is3DModel = true;
                    marker.modelObj = modelObj;

                    // Add directly to panorama, not as child of marker
                    modelObj.position.copy(pos);
                    panorama.add(modelObj);
                } catch (err) {
                    console.error("Failed to load GLB:", err);
                }
            }

            // Custom 2D Image - ENFORCE type 'image'
            if (!marker && spotData.type === 'image' && spotData.model_path && !spotData.model_path.toLowerCase()
                .endsWith('.glb')) {
                const customIconUrl = '{{ url('storage') }}/' + spotData.model_path;
                const geometry = new THREE.PlaneGeometry(600, 600);
                const texture = new THREE.TextureLoader().load(customIconUrl);
                const material = new THREE.MeshBasicMaterial({
                    map: texture,
                    transparent: true,
                    side: THREE.DoubleSide
                });
                marker = new THREE.Mesh(geometry, material);
                marker.renderOrder = 999;
                marker.rotation.order = 'YXZ';
                marker.rotation.set(spotData.rotation_x || 0, spotData.rotation_y || 0, spotData.rotation_z || 0);
                marker.scale.set(spotData.scale_x || 0.1, spotData.scale_y || 0.1, 1);
                marker.isPerspectiveMesh = true;
                marker.isCustomImage = true;
            }

            if (!marker) {
                if (spotData.is_perspective) {
                    const geometry = new THREE.PlaneGeometry(600, 600);
                    const texture = new THREE.TextureLoader().load(iconUrl);
                    const material = new THREE.MeshBasicMaterial({
                        map: texture,
                        transparent: true,
                        side: THREE.DoubleSide
                    });
                    marker = new THREE.Mesh(geometry, material);
                    marker.renderOrder = 999;
                    marker.rotation.order = 'YXZ';
                    marker.rotation.set(spotData.rotation_x || 0, spotData.rotation_y || 0, spotData.rotation_z || 0);
                    marker.scale.set(spotData.scale_x || 0.1, spotData.scale_y || 0.1, 1);
                    marker.isPerspectiveMesh = true;
                } else {
                    marker = new PANOLENS.Infospot(600, iconUrl);
                    marker.renderOrder = 1000;
                }
            }

            marker.spotData = spotData;
            marker.position.copy(pos);

            marker.addEventListener('click', () => {
                if (isAdding || window.wasDragging) {
                    window.wasDragging = false;
                    return;
                }
                editInfospot(spotData.id, spotData);
            });

            // Smart Hover Logic
            marker.addEventListener('hoverenter', () => {
                if (marker.is3DModel) {
                    const s = 1000 * 1.2;
                    new TWEEN.Tween(marker.modelObj.scale).to({
                        x: (spotData.scale_x || 0.1) * s,
                        y: (spotData.scale_y || 0.1) * s,
                        z: (spotData.scale_z || spotData.scale_x || 0.1) * s
                    }, 300).easing(TWEEN.Easing.Back.Out).start();
                } else if (marker.isPerspectiveMesh) {
                    new TWEEN.Tween(marker.scale).to({
                        x: (spotData.scale_x || 0.1) * 1.2,
                        y: (spotData.scale_y || 0.1) * 1.2,
                        z: 1.2
                    }, 300).easing(TWEEN.Easing.Back.Out).start();
                } else {
                    marker.scale.set(1.3, 1.3, 1.3);
                }
            });

            marker.addEventListener('hoverleave', () => {
                if (marker.is3DModel) {
                    const s = 1000;
                    new TWEEN.Tween(marker.modelObj.scale).to({
                        x: (spotData.scale_x || 0.1) * s,
                        y: (spotData.scale_y || 0.1) * s,
                        z: (spotData.scale_z || spotData.scale_x || 0.1) * s
                    }, 300).easing(TWEEN.Easing.Back.Out).start();
                } else if (marker.isPerspectiveMesh) {
                    new TWEEN.Tween(marker.scale).to({
                        x: spotData.scale_x || 0.1,
                        y: spotData.scale_y || 0.1,
                        z: 1
                    }, 300).easing(TWEEN.Easing.Back.Out).start();
                } else {
                    marker.scale.set(1, 1, 1);
                }
            });

            if (!marker.is3DModel) addBounce(marker);

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
        const fields3D = document.getElementById('fields-3d');
        const singleProductWrapper = document.getElementById('single-product-wrapper');
        const multiProductWrapper = document.getElementById('multi-product-wrapper');
        const visualWrapper = document.getElementById('wrapper-marker-visual');
        const inputPerspective = document.getElementById('input-perspective');
        const transformControls = document.getElementById('transformation-controls');
        const inputRx = document.getElementById('input-rx'),
            inputRy = document.getElementById('input-ry'),
            inputRz = document.getElementById('input-rz');
        const inputSx = document.getElementById('input-sx'),
            inputSy = document.getElementById('input-sy');
        const labelRx = document.getElementById('label-rx'),
            labelRy = document.getElementById('label-ry'),
            labelRz = document.getElementById('label-rz');
        const labelSx = document.getElementById('label-sx'),
            labelSy = document.getElementById('label-sy');

        let originalSpotState = null;

        const valX = document.getElementById('val_x'),
            valY = document.getElementById('val_y'),
            valZ = document.getElementById('val_z');
        const barX = document.getElementById('bar_x'),
            barY = document.getElementById('bar_y'),
            barZ = document.getElementById('bar_z');
        const pos_x = document.getElementById('pos_x'),
            pos_y = document.getElementById('pos_y'),
            pos_z = document.getElementById('pos_z');

        const inputModelFile = document.getElementById('input-model-file');
        const currentModelInfo = document.getElementById('current-model-info');
        const currentModelName = document.getElementById('current-model-name');

        inputPerspective.addEventListener('change', (e) => {
            if (e.target.checked) transformControls.classList.remove('hidden');
            else transformControls.classList.add('hidden');
            refreshCurrentMarkerPreview();
        });

        [inputRx, inputRy, inputRz, inputSx, inputSy].forEach(input => {
            input.addEventListener('input', () => {
                updateLabels();
                updateRealtimePreview();
            });
        });

        [inputModelFile, document.getElementById('input-marker-image')].forEach(input => {
            if (input) {
                input.addEventListener('change', () => {
                    if (currentModelInfo) currentModelInfo.classList.add('hidden');
                });
            }
        });

        const sPx = document.getElementById('slider-px'),
            sPy = document.getElementById('slider-py'),
            sPz = document.getElementById('slider-pz');
        const lPx = document.getElementById('label-px'),
            lPy = document.getElementById('label-py'),
            lPz = document.getElementById('label-pz');

        sPx.addEventListener('input', () => {
            const val = parseInt(sPx.value);
            pos_x.value = val;
            lPx.innerText = val;
            const vx = parseInt(sPx.value),
                vy = parseInt(sPy.value),
                vz = parseInt(sPz.value);
            updatePosDisplay(vx, vy, vz);

            let marker = null;
            if (editingId && renderedSpots[editingId]) marker = renderedSpots[editingId];
            else if (ghostMarker) marker = ghostMarker;

            if (marker) {
                marker.position.set(vx, vy, vz);
                if (marker.isPerspectiveMesh) marker.baseY = vy;
                if (marker.modelObj) {
                    marker.modelObj.position.set(vx, vy, vz);
                }
            }
        });

        sPy.addEventListener('input', () => {
            const val = parseInt(sPy.value);
            pos_y.value = val;
            lPy.innerText = val;
            const vx = parseInt(sPx.value),
                vy = parseInt(sPy.value),
                vz = parseInt(sPz.value);
            updatePosDisplay(vx, vy, vz);

            let marker = null;
            if (editingId && renderedSpots[editingId]) marker = renderedSpots[editingId];
            else if (ghostMarker) marker = ghostMarker;

            if (marker) {
                marker.position.set(vx, vy, vz);
                if (marker.isPerspectiveMesh) marker.baseY = vy;
                if (marker.modelObj) {
                    marker.modelObj.position.set(vx, vy, vz);
                }
            }
        });

        sPz.addEventListener('input', () => {
            const val = parseInt(sPz.value);
            pos_z.value = val;
            lPz.innerText = val;
            const vx = parseInt(sPx.value),
                vy = parseInt(sPy.value),
                vz = parseInt(sPz.value);
            updatePosDisplay(vx, vy, vz);

            let marker = null;
            if (editingId && renderedSpots[editingId]) marker = renderedSpots[editingId];
            else if (ghostMarker) marker = ghostMarker;

            if (marker) {
                marker.position.set(vx, vy, vz);
                if (marker.isPerspectiveMesh) marker.baseY = vy;
                if (marker.modelObj) {
                    marker.modelObj.position.set(vx, vy, vz);
                }
            }
        });

        function updateLabels() {
            labelRx.innerText = `${Math.round(inputRx.value * 180 / Math.PI)}°`;
            labelRy.innerText = `${Math.round(inputRy.value * 180 / Math.PI)}°`;
            labelRz.innerText = `${Math.round(inputRz.value * 180 / Math.PI)}°`;
            labelSx.innerText = inputSx.value;
            labelSy.innerText = inputSy.value;
        }

        async function refreshCurrentMarkerPreview() {
            let spotData = null;
            if (editingId) {
                const originalSpot = existingSpots.find(s => s.id == editingId);
                spotData = {
                    ...originalSpot,
                    id: editingId,
                    type: inputType.value,
                    is_perspective: inputPerspective.checked,
                    position_x: parseFloat(pos_x.value),
                    position_y: parseFloat(pos_y.value),
                    position_z: parseFloat(pos_z.value),
                    rotation_x: parseFloat(inputRx.value),
                    rotation_y: parseFloat(inputRy.value),
                    rotation_z: parseFloat(inputRz.value),
                    scale_x: parseFloat(inputSx.value),
                    scale_y: parseFloat(inputSy.value),
                };
            } else {
                spotData = {
                    id: 'ghost',
                    type: inputType.value,
                    is_perspective: inputPerspective.checked,
                    position_x: parseFloat(pos_x.value),
                    position_y: parseFloat(pos_y.value),
                    position_z: parseFloat(pos_z.value),
                    rotation_x: parseFloat(inputRx.value),
                    rotation_y: parseFloat(inputRy.value),
                    rotation_z: parseFloat(inputRz.value),
                    scale_x: parseFloat(inputSx.value),
                    scale_y: parseFloat(inputSy.value),
                };
                if (ghostMarker) {
                    panorama.remove(ghostMarker);
                    ghostMarker = null;
                }
            }

            const m = await renderMarker(spotData);
            if (!editingId) ghostMarker = m;
        }

        function updateRealtimePreview() {
            let marker = null;
            let spotData = null;

            if (editingId && renderedSpots[editingId]) {
                marker = renderedSpots[editingId];
                spotData = existingSpots.find(s => s.id == editingId);
            } else if (ghostMarker) {
                marker = ghostMarker;
                // Create a fake spotData for ghost marker updates
                spotData = {
                    rotation_x: parseFloat(inputRx.value),
                    rotation_y: parseFloat(inputRy.value),
                    rotation_z: parseFloat(inputRz.value),
                    scale_x: parseFloat(inputSx.value),
                    scale_y: parseFloat(inputSy.value),
                    is_perspective: inputPerspective.checked
                };
            }

            if (marker && spotData) {
                spotData.is_perspective = inputPerspective.checked;
                spotData.rotation_x = parseFloat(inputRx.value);
                spotData.rotation_y = parseFloat(inputRy.value);
                spotData.rotation_z = parseFloat(inputRz.value);
                spotData.scale_x = parseFloat(inputSx.value);
                spotData.scale_y = parseFloat(inputSy.value);

                if (marker.is3DModel && marker.modelObj) {
                    marker.modelObj.rotation.set(spotData.rotation_x, spotData.rotation_y, spotData.rotation_z);
                    const s = 1000;
                    marker.modelObj.scale.set(spotData.scale_x * s, spotData.scale_y * s, (spotData.scale_z || spotData
                        .scale_x) * s);
                } else if (marker.isPerspectiveMesh || (marker.isCustomImage && inputPerspective.checked)) {
                    marker.rotation.set(spotData.rotation_x, spotData.rotation_y, spotData.rotation_z);
                    marker.scale.set(spotData.scale_x, spotData.scale_y, 1);
                }
            }
        }

        inputType.addEventListener('change', (e) => {
            const type = e.target.value;

            if (type === 'nav') {
                fieldsInfo.classList.add('hidden');
                fieldsNav.classList.remove('hidden');
                if (fields3D) fields3D.classList.add('hidden');
                if (visualWrapper) visualWrapper.classList.add('hidden');
                if (singleProductWrapper) singleProductWrapper.classList.add('hidden');
                if (multiProductWrapper) multiProductWrapper.classList.add('hidden');
            } else if (type === '3d') {
                fieldsInfo.classList.remove('hidden');
                fieldsNav.classList.add('hidden');
                if (fields3D) fields3D.classList.remove('hidden');
                if (visualWrapper) visualWrapper.classList.remove('hidden');
                // Product wrappers will be toggled by the 'is_multi' input logic
            } else {
                fieldsInfo.classList.remove('hidden');
                fieldsNav.classList.add('hidden');
                if (fields3D) fields3D.classList.add('hidden');
                if (visualWrapper) visualWrapper.classList.remove('hidden');
            }
            refreshCurrentMarkerPreview();
        });

        const ctxMenu = document.getElementById('context-menu');
        let lastRightClickCoords = null;
        let lastRightClickSpot = null;

        container.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            const rect = container.getBoundingClientRect();
            const mouse = new THREE.Vector2(((e.clientX - rect.left) / rect.width) * 2 - 1, -((e.clientY - rect
                .top) / rect.height) * 2 + 1);
            const raycaster = new THREE.Raycaster();
            raycaster.setFromCamera(mouse, viewer.getCamera());

            const markers = Object.values(renderedSpots);
            const markerIntersects = raycaster.intersectObjects(markers, true);

            if (markerIntersects.length > 0) {
                let hit = markerIntersects[0].object;
                let foundId = null;
                let curr = hit;
                while (curr && !foundId) {
                    for (const id in renderedSpots) {
                        if (renderedSpots[id] === curr) {
                            foundId = id;
                            break;
                        }
                    }
                    curr = curr.parent;
                }
                if (foundId) {
                    lastRightClickSpot = {
                        id: foundId,
                        data: existingSpots.find(s => s.id == foundId)
                    };
                    showContextMenu(e.clientX, e.clientY, 'spot');
                    return;
                }
            }

            const panoramaIntersects = raycaster.intersectObject(panorama, false);
            if (panoramaIntersects.length > 0) {
                const p = panoramaIntersects[0].point;
                lastRightClickCoords = {
                    x: Math.round(p.x),
                    y: Math.round(p.y),
                    z: Math.round(p.z)
                };
                showContextMenu(e.clientX, e.clientY, 'empty');
            }
        }, true);

        function showContextMenu(x, y, mode) {
            ctxMenu.classList.remove('hidden');
            ctxMenu.style.top = `${y}px`;
            ctxMenu.style.left = `${x}px`;
            if (mode === 'spot') {
                document.getElementById('menu-add-info').classList.add('hidden');
                document.getElementById('menu-add-multi').classList.add('hidden');
                document.getElementById('menu-add-nav').classList.add('hidden');
                document.getElementById('menu-edit').classList.remove('hidden');
                document.getElementById('menu-delete').classList.remove('hidden');
                document.getElementById('menu-divider').classList.remove('hidden');
            } else {
                document.getElementById('menu-add-info').classList.remove('hidden');
                document.getElementById('menu-add-multi').classList.remove('hidden');
                document.getElementById('menu-add-nav').classList.remove('hidden');
                document.getElementById('menu-edit').classList.add('hidden');
                document.getElementById('menu-delete').classList.add('hidden');
                document.getElementById('menu-divider').classList.add('hidden');
            }
        }

        window.setMarkerType = function(type) {
            inputType.value = type;

            // Update Buttons
            document.querySelectorAll('.marker-type-btn').forEach(btn => btn.classList.remove('active'));
            const activeBtn = document.getElementById(`btn-marker-${type === 'nav' ? 'info' : type}`);
            if (activeBtn) activeBtn.classList.add('active');

            // Toggle Fields
            const imgUpload = document.getElementById('marker-image-upload');
            if (imgUpload) imgUpload.classList.toggle('hidden', type !== 'image');

            const f3d = document.getElementById('fields-3d');
            if (f3d) f3d.classList.toggle('hidden', type !== '3d');

            const vWrap = document.getElementById('wrapper-marker-visual');
            if (vWrap) vWrap.classList.toggle('hidden', type === 'nav');

            if (type === 'nav') {
                if (fieldsInfo) fieldsInfo.classList.add('hidden');
                if (singleProductWrapper) singleProductWrapper.classList.add('hidden');
                if (multiProductWrapper) multiProductWrapper.classList.add('hidden');
            }

            // Perspective setting defaults
            if (type === '3d' || type === 'image') {
                inputPerspective.checked = false;
            } else {
                inputPerspective.checked = false;
            }
            inputPerspective.dispatchEvent(new Event('change'));
            inputType.dispatchEvent(new Event('change'));

            // Update Instruction/Labels
            updateLabels();
        }

        function hideContextMenu() {
            ctxMenu.classList.add('hidden');
        }
        window.addEventListener('click', (e) => {
            if (!ctxMenu.contains(e.target)) hideContextMenu();
        });

        window.handleMenuAction = function(action) {
            hideContextMenu();
            if (action === 'add_info' || action === 'add_multi' || action === 'add_nav') {
                let type = 'info';
                if (action === 'add_nav') type = 'nav';

                document.getElementById('input-is-multi').value = (action === 'add_multi' ? '1' : '0');
                document.getElementById('input-title').value = ''; // Reset first

                if (lastRightClickCoords) {
                    setMarkerType(type);
                    openForm('create');

                    if (action === 'add_multi') {
                        document.getElementById('input-title').value = "Multi Product Info";
                        addNewAssetRow();
                    } else if (action === 'add_info') {
                        document.getElementById('input-title').value = "New Product";
                    } else if (action === 'add_nav') {
                        document.getElementById('input-title').value = "New Navigation";
                    }

                    pos_x.value = lastRightClickCoords.x;
                    pos_y.value = lastRightClickCoords.y;
                    pos_z.value = lastRightClickCoords.z;
                    updatePosDisplay(lastRightClickCoords.x, lastRightClickCoords.y, lastRightClickCoords.z);

                    // Update ghost marker texture to match type
                    if (ghostMarker) {
                        let textureUrl = iconTextures.info;
                        if (type === 'nav') textureUrl = iconTextures.nav;
                        if (type === '3d') textureUrl = iconTextures.threed;

                        const loader = new THREE.TextureLoader();
                        ghostMarker.material.map = loader.load(textureUrl);
                        ghostMarker.material.needsUpdate = true;

                        if (!ghostMarker.parent) panorama.add(ghostMarker);
                        ghostMarker.position.set(lastRightClickCoords.x, lastRightClickCoords.y, lastRightClickCoords
                        .z);
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
            valX.innerText = x;
            valY.innerText = y;
            valZ.innerText = z;
            const max = 10000;
            barX.style.width = Math.min(Math.abs(x) / max * 100, 100) + '%';
            barY.style.width = Math.min(Math.abs(y) / max * 100, 100) + '%';
            barZ.style.width = Math.min(Math.abs(z) / max * 100, 100) + '%';

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

        document.getElementById('btn-add-product').addEventListener('click', () => {
            resetProductForm();
            document.getElementById('product-form-wrap').classList.remove('hidden');
        });

        document.getElementById('btn-cancel-product').addEventListener('click', () => {
            document.getElementById('product-form-wrap').classList.add('hidden');
        });

        document.getElementById('btn-save-product').addEventListener('click', async () => {
            const id = document.getElementById('edit-product-id').value;
            const name = document.getElementById('product-name').value;
            const descId = document.getElementById('product-desc-id-multi').value;
            const descEn = document.getElementById('product-desc-en-multi').value;

            if (!name) {
                alert('Nama produk wajib diisi.');
                return;
            }
            if (!currentUploadInfospotId) {
                alert('Simpan node dahulu.');
                return;
            }

            try {
                const url = id ?
                    `{{ url('admin/infospot-products') }}/${id}` :
                    `{{ url('admin/infospots') }}/${currentUploadInfospotId}/products`;
                const method = id ? 'PATCH' : 'POST';

                const researcher = document.getElementById('product-researcher-multi').value;
                const contact = document.getElementById('product-contact-multi').value;

                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name,
                        description_id: descId,
                        description_en: descEn,
                        researcher,
                        contact_person: contact
                    })
                });
                const data = await res.json();
                if (data.success) {
                    if (!id) {
                        // If newly created, we could automatically switch to edit mode to allow assets
                        // For now, let's just refresh the product list
                    }
                    document.getElementById('product-form-wrap').classList.add('hidden');
                    loadProducts(currentUploadInfospotId);
                    // Also update the global asset list if we had one (but we removed it)
                    // loadExistingAssets(currentUploadInfospotId);
                }
            } catch (e) {
                alert('Gagal simpan produk.');
            }
        });

        function resetProductForm() {
            document.getElementById('edit-product-id').value = '';
            document.getElementById('product-name').value = '';
            document.getElementById('product-desc-id').value = '';
            document.getElementById('product-desc-en').value = '';
            document.getElementById('product-desc-id-multi').value = '';
            document.getElementById('product-desc-en-multi').value = '';
            document.getElementById('product-researcher').value = '';
            document.getElementById('product-contact').value = '';
            document.getElementById('product-researcher-multi').value = '';
            document.getElementById('product-contact-multi').value = '';

            // Reset previews
            ['desc', 'researcher', 'contact'].forEach(t => {
                _updateSidebarPreview(t, 'single');
                _updateSidebarPreview(t, 'multi');
            });

            // Reset assets for product
            document.getElementById('product-assets-section').classList.add('hidden');
            document.getElementById('existing-assets-list').innerHTML = '';
            document.getElementById('new-asset-rows').innerHTML = '';

            // Reset single product assets
            document.getElementById('single-existing-assets').innerHTML = '';
            document.getElementById('single-new-assets').innerHTML = '';
            document.getElementById('single-existing-assets').classList.add('hidden');
            document.getElementById('single-no-asset-hint').classList.remove('hidden');
        }

        let infospotProducts = [];

        async function loadProducts(infospotId) {
            const isMulti = document.getElementById('input-is-multi').value === '1';
            const list = document.getElementById('product-list');
            list.innerHTML = '<p class="text-[7px] text-slate-500 italic text-center py-1">Loading...</p>';

            try {
                const res = await fetch(`{{ url('admin/infospots') }}/${infospotId}/products`);
                const data = await res.json();
                infospotProducts = data.products || [];

                if (isMulti) {
                    // Multi Product Logic
                    const btnAddProduct = document.getElementById('btn-add-product');
                    if (btnAddProduct) btnAddProduct.classList.remove('hidden');

                    if (infospotProducts.length === 0) {
                        list.innerHTML =
                            '<p class="text-[7px] text-slate-600 italic text-center py-1">No products yet.</p>';
                    } else {
                        list.innerHTML = infospotProducts.map(p => `
                        <div class="flex items-center justify-between p-2 bg-slate-800 rounded border border-slate-700 group hover:border-indigo-500/50 transition-all">
                            <div class="flex flex-col">
                                <span class="text-[8px] font-bold text-slate-200">${p.name}</span>
                                <span class="text-[6px] text-slate-500 uppercase tracking-widest">${p.assets_count || 0} Assets</span>
                            </div>
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button type="button" onclick="editProductFromList(${p.id})" class="text-indigo-400 hover:text-indigo-300"><i class="fas fa-edit text-[8px]"></i></button>
                                <button type="button" onclick="deleteProduct(${p.id})" class="text-slate-500 hover:text-rose-400"><i class="fas fa-trash text-[8px]"></i></button>
                            </div>
                        </div>
                    `).join('');
                    }
                } else {
                    // Single Product Logic
                    if (infospotProducts.length > 0) {
                        const p = infospotProducts[0];
                        document.getElementById('edit-product-id').value = p.id;
                        document.getElementById('product-desc-id').value = p.description_id || '';
                        document.getElementById('product-desc-en').value = p.description_en || '';
                        document.getElementById('product-researcher').value = p.researcher || '';
                        document.getElementById('product-contact').value = p.contact_person || '';

                        _updateSidebarPreview('desc', 'single');
                        _updateSidebarPreview('researcher', 'single');
                        _updateSidebarPreview('contact', 'single');

                        // Set as active upload product for assets
                        currentUploadInfospotId = infospotId;
                        loadExistingAssets(infospotId, p.id);
                    } else {
                        // Create placeholder if none exists yet
                        document.getElementById('product-desc-id').value = '';
                        document.getElementById('product-desc-en').value = '';
                        document.getElementById('product-researcher').value = '';
                        document.getElementById('product-contact').value = '';
                        _updateSidebarPreview('desc', 'single');
                        _updateSidebarPreview('researcher', 'single');
                        _updateSidebarPreview('contact', 'single');
                    }
                }
            } catch (e) {
                console.error('Error loading products:', e);
            }
        }

        window.editProductFromList = function(id) {
            const p = infospotProducts.find(item => item.id == id);
            if (p) {
                editProduct(p.id, p.name, p.description_id, p.description_en, p.researcher, p.contact_person);
            }
        };

        window.editProduct = function(id, name, descId, descEn, researcher, contact) {
            document.getElementById('edit-product-id').value = id;
            document.getElementById('product-name').value = name;
            document.getElementById('product-desc-id-multi').value = descId;
            document.getElementById('product-desc-en-multi').value = descEn;
            document.getElementById('product-researcher-multi').value = researcher || '';
            document.getElementById('product-contact-multi').value = contact || '';

            // Update previews
            _updateSidebarPreview('desc', 'multi');
            _updateSidebarPreview('researcher', 'multi');
            _updateSidebarPreview('contact', 'multi');

            document.getElementById('product-form-wrap').classList.remove('hidden');
            document.getElementById('product-assets-section').classList.remove('hidden');

            // Reset asset fields for product
            document.getElementById('new-asset-rows').innerHTML = '';
            updateAssetUploadVisibility();

            // Load assets for THIS product
            loadExistingAssets(currentUploadInfospotId, id);
        };

        window.deleteProduct = async function(id) {
            if (!confirm('Hapus produk ini? Assets di dalamnya akan ikut terhapus.')) return;
            try {
                await fetch(`{{ url('admin/products') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                loadProducts(currentUploadInfospotId);
                updateProductSelectors();
                loadExistingAssets(currentUploadInfospotId);
            } catch (e) {
                alert('Gagal hapus.');
            }
        };

        function updateProductSelectors() {
            document.querySelectorAll('.asset-product-select').forEach(sel => {
                const currentVal = sel.value;
                sel.innerHTML = '<option value="">(No Product)</option>' + infospotProducts.map(p => `
                <option value="${p.id}">${p.name}</option>
            `).join('');
                sel.value = currentVal;
            });
        }

        function addNewAssetRow(mode = 'multi') {
            const idx = assetRowIndex++;
            const row = document.createElement('div');
            row.className = 'flex flex-col gap-1.5 p-2 bg-slate-800 rounded border border-slate-700';
            row.dataset.index = idx;
            row.innerHTML = `
            <div class="flex items-center gap-1.5">
                <select class="flex-1 bg-slate-700 border border-slate-600 text-slate-300 text-[8px] font-bold uppercase tracking-widest rounded px-2 py-1 focus:outline-none asset-type-select">
                    <option value="2d">🖼 2D Image</option>
                    <option value="video">🎥 Video (WebM)</option>
                    <option value="3d">🧊 3D GLB</option>
                </select>
                <button type="button" class="remove-asset-row text-slate-500 hover:text-rose-400 transition-colors ml-auto">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
            </div>
            <input type="text" placeholder="Label (opsional)" class="asset-label bg-slate-700 border border-slate-600 text-slate-300 text-[8px] rounded px-2 py-1 placeholder-slate-600 focus:outline-none focus:border-slate-500">
            <input type="file" accept="image/*" class="asset-file w-full text-[8px] text-slate-400 file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-[8px] file:font-bold file:bg-slate-700 file:text-slate-300 hover:file:bg-slate-600 cursor-pointer">
        `;

            const typeSelect = row.querySelector('.asset-type-select');
            const fileInput = row.querySelector('.asset-file');
            typeSelect.addEventListener('change', () => {
                if (typeSelect.value === '3d') fileInput.accept = '.glb';
                else if (typeSelect.value === 'video') fileInput.accept = 'video/webm,video/mp4';
                else fileInput.accept = 'image/*';
            });

            row.querySelector('.remove-asset-row').addEventListener('click', () => {
                row.remove();
                updateAssetUploadVisibility(mode);
            });

            const targetId = mode === 'single' ? 'single-new-assets' : 'new-asset-rows';
            document.getElementById(targetId).appendChild(row);
            updateAssetUploadVisibility(mode);
        }

        function updateAssetUploadVisibility(mode = 'multi') {
            const rowId = mode === 'single' ? 'single-new-assets' : 'new-asset-rows';
            const wrapId = mode === 'single' ? 'single-upload-wrap' : 'asset-upload-wrap';
            const hintId = mode === 'single' ? 'single-no-asset-hint' : 'no-asset-hint';

            const container = document.getElementById(rowId);
            const wrap = document.getElementById(wrapId);
            const hint = document.getElementById(hintId);

            if (container.children.length > 0) {
                wrap.classList.remove('hidden');
                if (hint) hint.classList.add('hidden');
            } else {
                wrap.classList.add('hidden');
                if (hint) hint.classList.remove('hidden');
            }
        }



        // ---- Load existing assets via AJAX ----
        async function loadExistingAssets(infospotId, productId = null) {
            const isMulti = document.getElementById('input-is-multi').value === '1';
            const targetId = isMulti ? 'existing-assets-list' : 'single-existing-assets';
            const container = document.getElementById(targetId);
            if (!container) return;

            container.innerHTML = '<p class="text-[8px] text-slate-500 italic text-center py-2">Loading assets...</p>';
            container.classList.remove('hidden');

            try {
                if (!productId) {
                    container.innerHTML =
                        '<p class="text-[8px] text-slate-600 italic text-center py-1">Select a product to view assets.</p>';
                    return;
                }
                let url = `{{ url('admin/products') }}/${productId}/assets`;

                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();

                if (!data.assets || data.assets.length === 0) {
                    container.innerHTML =
                        '<p class="text-[8px] text-slate-600 italic text-center py-1">Belum ada asset.</p>';
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
                        : (a.file_type === 'video' ? 'bg-rose-900 text-rose-300' : 'bg-blue-900 text-blue-300')
                    } uppercase tracking-widest">${a.file_type === '3d' ? '3D' : (a.file_type === 'video' ? 'Vid' : '2D')}</span>
                    <span class="text-[8px] text-slate-300 truncate flex-1 min-w-0">${a.label || a.filename}</span>
                    ${a.product ? `<span class="text-[7px] text-indigo-400 bg-indigo-900/40 px-1 rounded truncate max-w-[50px] border border-indigo-500/20">${a.product.name}</span>` : ''}
                    <button type="button" onclick="deleteAsset(${a.id}, ${productId})" title="Hapus"
                        class="shrink-0 text-slate-500 hover:text-rose-400 transition-colors">
                        <i class="fas fa-trash-alt text-[9px]"></i>
                    </button>
                </div>
            `).join('');

                // init drag-and-drop
                initDragSort(container);

            } catch (e) {
                container.innerHTML =
                    '<p class="text-[8px] text-rose-400 italic text-center py-1">Gagal memuat asset.</p>';
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
                    container.querySelectorAll('[draggable="true"]').forEach(r => r.classList.remove(
                        'drag-over'));
                    // Save new order to server
                    saveSortOrder(container);
                });

                row.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    if (row === draggingEl) return;

                    // Determine insert position
                    const rect = row.getBoundingClientRect();
                    const midY = rect.top + rect.height / 2;
                    const isAfter = e.clientY > midY;

                    container.querySelectorAll('[draggable="true"]').forEach(r => r.classList.remove(
                        'drag-over-top', 'drag-over-bottom'));
                    row.classList.add(isAfter ? 'drag-over-bottom' : 'drag-over-top');
                });

                row.addEventListener('drop', (e) => {
                    e.preventDefault();
                    if (!draggingEl || draggingEl === row) return;

                    const rect = row.getBoundingClientRect();
                    const isAfter = e.clientY > rect.top + rect.height / 2;

                    if (isAfter) {
                        row.after(draggingEl);
                    } else {
                        row.before(draggingEl);
                    }
                    container.querySelectorAll('[draggable="true"]').forEach(r => r.classList.remove(
                        'drag-over-top', 'drag-over-bottom'));
                });
            });
        }

        // ---- Save new sort order to server ----
        async function saveSortOrder(container) {
            const ids = [...container.querySelectorAll('[data-asset-id]')]
                .map(el => ({
                    id: parseInt(el.dataset.assetId)
                }));

            if (ids.length === 0) return;

            try {
                await fetch('{{ url('admin/product-assets/reorder') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        order: ids
                    })
                });
            } catch (e) {
                console.warn('Failed to save sort order:', e);
            }
        }

        // ---- Delete asset ----
        window.deleteAsset = async function(assetId, productId = null) {
            if (!confirm('Hapus asset ini?')) return;
            try {
                const res = await fetch(`{{ url('admin/product-assets') }}/${assetId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    // Refresh specific product list or loadProducts
                    if (productId) loadExistingAssets(currentUploadInfospotId, productId);
                    loadProducts(currentUploadInfospotId);
                }
            } catch (e) {
                alert('Gagal menghapus asset.');
            }
        };

        // ---- Upload new assets (Generic) ----
        async function performAssetUpload(mode = 'multi') {
            if (!currentUploadInfospotId) {
                alert('Simpan node dahulu sebelum upload asset.');
                return;
            }

            const rowId = mode === 'single' ? 'single-new-assets' : 'new-asset-rows';
            const btnId = mode === 'single' ? 'btn-upload-single-assets' : 'btn-upload-assets';
            const loadingId = mode === 'single' ? 'single-upload-loading' : 'asset-upload-loading';

            const rows = document.getElementById(rowId).querySelectorAll('[data-index]');
            if (rows.length === 0) return;

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');

            let hasFile = false;
            const productId = document.getElementById('edit-product-id').value;

            rows.forEach((row, i) => {
                const fileInput = row.querySelector('.asset-file');
                const typeSelect = row.querySelector('.asset-type-select');
                const label = row.querySelector('.asset-label');
                if (fileInput.files.length > 0) {
                    formData.append(`assets[${i}][file]`, fileInput.files[0]);
                    formData.append(`assets[${i}][file_type]`, typeSelect.value);
                    formData.append(`assets[${i}][label]`, label.value);
                    if (productId) formData.append(`assets[${i}][infospot_product_id]`, productId);
                    hasFile = true;
                }
            });

            if (!hasFile) {
                alert('Pilih minimal satu file.');
                return;
            }

            const btn = document.getElementById(btnId);
            const loading = document.getElementById(loadingId);
            btn.classList.add('hidden');
            loading.classList.remove('hidden');
            loading.style.display = 'flex';

            try {
                const res = await fetch(`{{ url('admin/products') }}/${productId}/assets`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    document.getElementById(rowId).innerHTML = '';
                    updateAssetUploadVisibility(mode);
                    loadExistingAssets(currentUploadInfospotId, productId);
                    loadProducts(currentUploadInfospotId); // Update asset counts
                    showInstruction('ASSETS UPLOADED.');
                } else {
                    alert('Upload gagal: ' + (data.message || 'Unknown error'));
                }
            } catch (e) {
                alert('Upload error: ' + e.message);
            } finally {
                btn.classList.remove('hidden');
                loading.classList.add('hidden');
                loading.style.display = '';
            }
        }

        // Attach listeners
        const btnMultiUpload = document.getElementById('btn-upload-assets');
        if (btnMultiUpload) btnMultiUpload.addEventListener('click', () => performAssetUpload('multi'));

        const btnSingleUpload = document.getElementById('btn-upload-single-assets');
        if (btnSingleUpload) btnSingleUpload.addEventListener('click', () => performAssetUpload('single'));

        function openForm(mode, spot = null) {
            listState.style.display = 'none';
            createForm.style.display = 'block';

            const submitBtn = document.getElementById('btn-submit-infospot');
            if (mode === 'create') {
                submitBtn.innerText = 'SIMPAN NODE';
            } else {
                submitBtn.innerText = 'UPDATE DATABASE';
            }

            // Always reset basic product form fields first
            resetProductForm();

            if (mode === 'create') {
                titleHeader.innerText = inputType.value === '3d' ? "New 3D Object" : "New Node";
                formEl.reset();
                currentUploadInfospotId = null;
                methodPut.innerHTML = '';
                formEl.action = "{{ route('admin.scenes.infospots.store', $scene) }}";
                formDelete.classList.add('hidden');

                const isMulti = document.getElementById('input-is-multi').value === '1';
                document.getElementById('single-product-wrapper').classList.toggle('hidden', isMulti);
                document.getElementById('multi-product-wrapper').classList.toggle('hidden', !isMulti);

                // Reset 3D Model Field
                inputModelFile.value = '';
                currentModelInfo.classList.add('hidden');
                if (inputType.value === '3d') {
                    inputPerspective.checked = false;
                } else {
                    inputPerspective.checked = false;
                }
                inputPerspective.dispatchEvent(new Event('change'));
            } else {
                titleHeader.innerText = spot.type === '3d' ? "3D Object Inspector" : (spot.type === 'image' ?
                    "2D Image Inspector" : "Inspector");
                setMarkerType(spot.type);

                // Handle 3D/Image Model Info in Edit mode
                inputModelFile.value = '';
                document.getElementById('input-marker-image').value = '';

                if ((spot.type === '3d' || spot.type === 'image') && spot.model_path) {
                    currentModelInfo.classList.remove('hidden');
                    currentModelName.innerText = spot.model_path.split('/').pop();

                    const iconBox = document.getElementById('current-model-icon-box');
                    const icon = document.getElementById('current-model-icon');

                    if (spot.model_path.toLowerCase().endsWith('.glb')) {
                        iconBox.className =
                            'w-6 h-6 rounded flex items-center justify-center text-[10px] bg-purple-500/20 text-purple-400';
                        icon.className = 'fas fa-cube';
                    } else {
                        iconBox.className =
                            'w-6 h-6 rounded flex items-center justify-center text-[10px] bg-orange-500/20 text-orange-400';
                        icon.className = 'fas fa-image';
                    }
                } else {
                    currentModelInfo.classList.add('hidden');
                }

                pos_x.value = spot.position_x;
                pos_y.value = spot.position_y;
                pos_z.value = spot.position_z;
                updatePosDisplay(spot.position_x, spot.position_y, spot.position_z);
                inputPerspective.checked = !!spot.is_perspective;
                inputPerspective.dispatchEvent(new Event('change'));
                inputRx.value = spot.rotation_x || 0;
                inputRy.value = spot.rotation_y || 0;
                inputRz.value = spot.rotation_z || 0;
                inputSx.value = spot.scale_x || 0.1;
                inputSy.value = spot.scale_y || 0.1;
                updateLabels();

                document.getElementById('input-title').value = spot.title || '';
                document.getElementById('input-target').value = spot.target_scene_id || '';
                document.getElementById('input-is-multi').value = spot.is_multi ? '1' : '0';

                // Toggle wrappers
                const isMultiMode = !!spot.is_multi;
                document.getElementById('single-product-wrapper').classList.toggle('hidden', isMultiMode);
                document.getElementById('multi-product-wrapper').classList.toggle('hidden', !isMultiMode);

                // Load existing products
                loadProducts(spot.id);

                // Populate Position Sliders
                document.getElementById('slider-px').value = spot.position_x;
                document.getElementById('slider-py').value = spot.position_y;
                document.getElementById('slider-pz').value = spot.position_z;

                // Populate Transformation Sliders
                inputRx.value = spot.rotation_x || 0;
                inputRy.value = spot.rotation_y || 0;
                inputRz.value = spot.rotation_z || 0;
                inputSx.value = spot.scale_x || 0.1;
                inputSy.value = spot.scale_y || 0.1;

                // Trigger preview update
                updateRealtimePreview();

                currentUploadInfospotId = spot.id;
                methodPut.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                formEl.action = `{{ url('admin/infospots') }}/${spot.id}`;
                formDelete.action = `{{ url('admin/infospots') }}/${spot.id}`;
                formDelete.classList.remove('hidden');
            }
        }

        window.editInfospot = function(id, spotData) {
            editingId = id;

            // UI Indicator: highlight active card
            document.querySelectorAll('.spot-card-btn').forEach(btn => btn.classList.remove('active'));
            const activeCard = document.getElementById('spot-card-' + id);
            if (activeCard) {
                activeCard.classList.add('active');
                activeCard.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }

            // Capture original state for revert on cancel
            originalSpotState = {
                position_x: spotData.position_x,
                position_y: spotData.position_y,
                position_z: spotData.position_z,
                rotation_x: spotData.rotation_x || 0,
                rotation_y: spotData.rotation_y || 0,
                rotation_z: spotData.rotation_z || 0,
                scale_x: spotData.scale_x || 0.1,
                scale_y: spotData.scale_y || 0.1,
                is_perspective: !!spotData.is_perspective
            };

            viewer.tweenControlCenter(new THREE.Vector3(spotData.position_x, spotData.position_y, spotData.position_z),
                500);
            openForm('edit', spotData);
        };

        window.cancelForm = function() {
            // If we were editing, revert changes to the marker
            if (editingId && renderedSpots[editingId] && originalSpotState) {
                const marker = renderedSpots[editingId];
                const s = originalSpotState;

                marker.position.set(s.position_x, s.position_y, s.position_z);
                if (marker.isPerspectiveMesh) marker.baseY = s.position_y;

                if (marker.is3DModel && marker.modelObj) {
                    marker.modelObj.position.copy(marker.position);
                    marker.modelObj.rotation.set(s.rotation_x, s.rotation_y, s.rotation_z);
                    const scaleVal = 1000;
                    marker.modelObj.scale.set(s.scale_x * scaleVal, s.scale_y * scaleVal, (s.scale_z || s.scale_x) *
                        scaleVal);
                } else if (marker.isPerspectiveMesh) {
                    marker.rotation.set(s.rotation_x, s.rotation_y, s.rotation_z);
                    marker.scale.set(s.scale_x, s.scale_y, 1);
                }
            }

            createForm.style.display = 'none';
            listState.style.display = 'block';
            titleHeader.innerText = "Inspector";

            // Clear active card indicator
            document.querySelectorAll('.spot-card-btn').forEach(btn => btn.classList.remove('active'));

            editingId = null;
            originalSpotState = null;
            if (ghostMarker && ghostMarker.parent) panorama.remove(ghostMarker);
            coordDisplay.classList.add('hidden');
        };

        function showInstruction(msg) {
            toastText.innerText = msg;
            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');
            setTimeout(() => toast.classList.replace('opacity-100', 'opacity-0'), 4000);
        }

        let isDragging = false,
            dragMarker = null,
            windowWasDragging = false;

        container.addEventListener('pointermove', (e) => {
            const rect = container.getBoundingClientRect();
            const mouse = new THREE.Vector2(((e.clientX - rect.left) / rect.width) * 2 - 1, -((e.clientY - rect
                .top) / rect.height) * 2 + 1);
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
                    const x = Math.round(p.x),
                        y = Math.round(p.y),
                        z = Math.round(p.z);
                    dragMarker.position.set(x, y, z);
                    if (dragMarker.baseY !== undefined) dragMarker.baseY = y; // Update base for 2D bounce
                    updatePosDisplay(x, y, z);
                    pos_x.value = x;
                    pos_y.value = y;
                    pos_z.value = z;
                }
            }
        });

        container.addEventListener('pointerdown', (e) => {
            if (e.button !== 0) return;
            const rect = container.getBoundingClientRect();
            const mouse = new THREE.Vector2(((e.clientX - rect.left) / rect.width) * 2 - 1, -((e.clientY - rect
                .top) / rect.height) * 2 + 1);
            const raycaster = new THREE.Raycaster();
            raycaster.setFromCamera(mouse, viewer.getCamera());
            const intersects = raycaster.intersectObjects(Object.values(renderedSpots), true);
            if (intersects.length > 0) {
                let hit = intersects[0].object;
                let found = null;
                let curr = hit;
                while (curr && !found) {
                    for (const id in renderedSpots) {
                        if (renderedSpots[id] === curr) {
                            found = renderedSpots[id];
                            break;
                        }
                    }
                    curr = curr.parent;
                }
                if (found) {
                    isDragging = true;
                    windowWasDragging = false;
                    dragMarker = found;
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

                isDragging = false;
                dragMarker = null;
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
            const btn = document.getElementById('btn-lock-view');
            const icon = document.getElementById('lock-icon');
            const label = document.getElementById('lock-label');
            if (locked) {
                btn.classList.add('border-indigo-500/60', 'text-indigo-300');
                btn.classList.remove('border-white/10', 'text-white/60');
                icon.className = 'fas fa-lock text-[10px] text-indigo-400';
                label.innerText = `Locked (${lon?.toFixed(1)}° / ${lat?.toFixed(1)}°)`;
            } else {
                btn.classList.remove('border-indigo-500/60', 'text-indigo-300');
                btn.classList.add('border-white/10', 'text-white/60');
                icon.className = 'fas fa-lock-open text-[10px]';
                label.innerText = 'Lock View';
            }
        }

        window.lockInitialView = async function() {
            // Read the actual camera direction from Three.js (more reliable than controls.lon/lat)
            const camera = viewer.getCamera();
            const dir = new THREE.Vector3();
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
                    body: JSON.stringify({
                        lon,
                        lat
                    })
                });
                const data = await res.json();
                if (data.success) {
                    _setLockState(true, data.lon, data.lat);
                    showInstruction(`VIEW LOCKED — LON: ${lon.toFixed(1)}° LAT: ${lat.toFixed(1)}°`);
                }
            } catch (e) {
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
                <div class="flex items-center">
                    <div id="quill-popup-title">
                        <i class="fas fa-pen-to-square" style="color:#6366f1"></i>
                        <span id="quill-popup-title-text">Edit Narration</span>
                    </div>
                    <!-- Tabs for ID/EN (Shown only for Descrition) -->
                    <div id="quill-tabs-wrap" class="hidden flex items-center bg-white/5 rounded-lg p-1 ml-6">
                        <button type="button" onclick="switchQuillTab('id')" id="qtab-id"
                            class="px-4 py-1.5 text-[8px] font-bold uppercase tracking-widest rounded-md transition-all">Indo</button>
                        <button type="button" onclick="switchQuillTab('en')" id="qtab-en"
                            class="px-4 py-1.5 text-[8px] font-bold uppercase tracking-widest rounded-md transition-all">English</button>
                    </div>
                </div>
                <button id="quill-popup-close" onclick="closeQuillEditor()">&#10005;</button>
            </div>
            <div id="quill-popup-editor">
                <div id="quill-container"></div>
            </div>
            <div id="quill-popup-footer">
                <button class="ql-popup-btn ql-popup-btn-cancel" onclick="closeQuillEditor()">Cancel</button>
                <button class="ql-popup-btn ql-popup-btn-apply" onclick="applyQuillContent()"><i
                        class="fas fa-check mr-1"></i> Apply</button>
            </div>
        </div>
    </div>

    <script>
        /* ---- Quill Editor Popup Logic ---- */
        let _quill = null;
        let _quillLang = null; // 'id' or 'en'
        let _quillMode = 'single'; // 'single' or 'multi'

        // Init Quill once DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            _quill = new Quill('#quill-container', {
                theme: 'snow',
                placeholder: 'Write your narration here...',
                modules: {
                    toolbar: [
                        [{
                            header: [1, 2, 3, false]
                        }],
                        ['bold', 'italic', 'underline'],
                        [{
                            color: []
                        }, {
                            background: []
                        }],
                        [{
                            list: 'ordered'
                        }, {
                            list: 'bullet'
                        }],
                        ['link', 'image'],
                        ['clean']
                    ],
                    clipboard: {
                        matchVisual: false
                    }
                }
            });

            // Auto-white on paste: strip incoming color/background to fallback to editor default
            _quill.clipboard.addMatcher(Node.ELEMENT_NODE, (node, delta) => {
                delta.ops.forEach(op => {
                    if (op.attributes) {
                        delete op.attributes.color;
                        delete op.attributes.background;
                    }
                });
                return delta;
            });
        });

        window.openTabbedQuill = function(type, mode = 'single') {
            _isTabbed = true;
            _quillLang = 'id'; // Default to ID
            _quillMode = mode;

            document.getElementById('quill-tabs-wrap').classList.remove('hidden');
            document.getElementById('quill-popup-title-text').innerText = 'Edit Description';

            loadQuillContent();
            document.getElementById('quill-popup-overlay').classList.add('open');
            updateQuillTabUI();
        };

        window.openQuillEditor = function(field, mode = 'single') {
            _isTabbed = false;
            _quillLang = field; // 'researcher' or 'contact'
            _quillMode = mode;

            document.getElementById('quill-tabs-wrap').classList.add('hidden');
            document.getElementById('quill-popup-title-text').innerText = field === 'researcher' ? 'Edit Peneliti' :
                'Edit Kontak Person';

            loadQuillContent();
            document.getElementById('quill-popup-overlay').classList.add('open');
        };

        window.closeQuillEditor = function() {
            document.getElementById('quill-popup-overlay').classList.remove('open');
        };

        function loadQuillContent() {
            const suffix = _quillMode === 'multi' ? '-multi' : '';
            let targetId = '';

            if (_isTabbed) {
                targetId = 'product-desc-' + _quillLang + suffix;
            } else {
                targetId = 'product-' + _quillLang + suffix; // researcher or contact
            }

            const targetEl = document.getElementById(targetId);
            const val = targetEl ? (targetEl.value || '') : '';
            _quill.root.innerHTML = val;
        }

        window.switchQuillTab = function(lang) {
            if (!_isTabbed) return;

            // Save current first
            const suffix = _quillMode === 'multi' ? '-multi' : '';
            const curId = 'product-desc-' + _quillLang + suffix;
            const curEl = document.getElementById(curId);
            if (curEl) curEl.value = _quill.root.innerHTML === '<p><br></p>' ? '' : _quill.root.innerHTML;

            _quillLang = lang;
            loadQuillContent();
            updateQuillTabUI();
        };

        function updateQuillTabUI() {
            const tId = document.getElementById('qtab-id');
            const tEn = document.getElementById('qtab-en');
            if (!tId || !tEn) return;
            [tId, tEn].forEach(t => {
                t.classList.remove('bg-indigo-600', 'text-white');
                t.classList.add('text-slate-400');
            });
            const active = document.getElementById('qtab-' + _quillLang);
            if (active) {
                active.classList.remove('text-slate-400');
                active.classList.add('bg-indigo-600', 'text-white');
            }
        }

        window.applyQuillContent = function() {
            const suffix = _quillMode === 'multi' ? '-multi' : '';
            let targetId = '';
            let type = '';

            if (_isTabbed) {
                targetId = 'product-desc-' + _quillLang + suffix;
                type = 'desc';
            } else {
                targetId = 'product-' + _quillLang + suffix;
                type = _quillLang;
            }

            const html = _quill.root.innerHTML === '<p><br></p>' ? '' : _quill.root.innerHTML;
            const targetEl = document.getElementById(targetId);
            if (targetEl) targetEl.value = html;

            // Update sidebar preview
            _updateSidebarPreview(type, _quillMode);

            closeQuillEditor();
        };

        window.closeQuillEditor = function() {
            document.getElementById('quill-popup-overlay').classList.remove('open');
        };

        function _updateSidebarPreview(type, mode) {
            const suffix = mode === 'multi' ? '-multi' : '';
            const previewId = 'preview-' + type + (mode === 'multi' ? '-multi' : '-single');
            const btnId = 'btn-open-' + type + (mode === 'multi' ? '-multi' : '-single');
            const prevEl = document.getElementById(previewId);
            const btnEl = document.getElementById(btnId);
            if (!prevEl) return;

            let content = '';
            if (type === 'desc') {
                content = document.getElementById('product-desc-id' + suffix).value || document.getElementById(
                    'product-desc-en' + suffix).value;
            } else {
                content = document.getElementById('product-' + type + suffix).value;
            }

            const tmp = document.createElement('div');
            tmp.innerHTML = content || '';
            const plain = (tmp.innerText || '').trim();

            if (plain) {
                prevEl.innerText = plain.substring(0, 40) + (plain.length > 40 ? '...' : '');
                if (btnEl) btnEl.classList.add('has-content');
            } else {
                prevEl.innerText = 'Klik untuk mengisi...';
                if (btnEl) btnEl.classList.remove('has-content');
            }
        }

        // Connect overlay close
        document.getElementById('quill-popup-overlay').addEventListener('click', (e) => {
            if (e.target.id === 'quill-popup-overlay') closeQuillEditor();
        });
    </script>

@endsection
