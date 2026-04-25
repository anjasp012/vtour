@extends('components.admin-layout')

@section('header', 'Edit Infospot in ' . $scene->name)

@section('content')
<div class="bg-white rounded-lg shadow-sm max-w-2xl">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Edit Interactive Point</h3>
    </div>
    
    <div class="p-6">
        <form action="{{ route('admin.infospots.update', $infospot) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <select name="type" id="type-selector" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="info" {{ old('type', $infospot->type) == 'info' ? 'selected' : '' }}>Information Icon (Popup)</option>
                    <option value="nav" {{ old('type', $infospot->type) == 'nav' ? 'selected' : '' }}>Navigation Arrow (Go to Scene)</option>
                </select>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pos X</label>
                    <input type="number" name="position_x" value="{{ old('position_x', $infospot->position_x) }}" required class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pos Y</label>
                    <input type="number" name="position_y" value="{{ old('position_y', $infospot->position_y) }}" required class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pos Z</label>
                    <input type="number" name="position_z" value="{{ old('position_z', $infospot->position_z) }}" required class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                </div>
            </div>
            
            <div id="info-fields">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" name="title" value="{{ old('title', $infospot->title) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Content (ID)</label>
                    <textarea name="content_id" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('content_id', $infospot->content_id) }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Content (EN)</label>
                    <textarea name="content_en" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('content_en', $infospot->content_en) }}</textarea>
                </div>
            </div>

            <div id="nav-fields" class="hidden">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Target Scene</label>
                    <select name="target_scene_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">-- Select Scene --</option>
                        @foreach($hasTargetScenes as $ts)
                            <option value="{{ $ts->id }}" {{ old('target_scene_id', $infospot->target_scene_id) == $ts->id ? 'selected' : '' }}>{{ $ts->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="flex items-center space-x-4 mt-6 border-t pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow-sm text-sm font-medium transition-colors">
                    Update Point
                </button>
                <a href="{{ route('admin.scenes.show', $scene) }}" class="text-gray-500 hover:underline text-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================
     INFOSPOT ASSETS SECTION
     ============================================================ --}}
<div class="bg-white rounded-lg shadow-sm max-w-2xl mt-6">
    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-800">
            📎 Media Assets
            @php $allAssets = $infospot->products->flatMap->assets; @endphp
            <span class="ml-2 text-sm font-normal text-gray-400">({{ $allAssets->count() }} file)</span>
        </h3>
    </div>

    {{-- Existing assets list --}}
    <div class="p-6">
        @if($allAssets->isEmpty())
            <p class="text-sm text-gray-400 italic mb-4">Belum ada asset. Upload di bawah.</p>
        @else
            <div class="space-y-3 mb-6">
                @foreach($allAssets as $asset)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-gray-50">
                        <div class="flex items-center gap-3 min-w-0">
                            {{-- Type badge --}}
                            @if($asset->file_type === '3d')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-700 shrink-0">
                                    🧊 3D
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700 shrink-0">
                                    🖼️ 2D
                                </span>
                            @endif

                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-700 truncate">{{ $asset->label ?: basename($asset->file_path) }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ basename($asset->file_path) }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0 ml-3">
                            {{-- Preview link --}}
                            @if($asset->file_type === '2d')
                                <a href="{{ asset('storage/' . $asset->file_path) }}" target="_blank"
                                   class="text-xs text-blue-500 hover:underline">Preview</a>
                            @else
                                <a href="{{ asset('storage/' . $asset->file_path) }}" target="_blank"
                                   class="text-xs text-purple-500 hover:underline">Download</a>
                            @endif

                            {{-- Delete --}}
                            <form action="{{ route('admin.product-assets.destroy', $asset) }}" method="POST"
                                  onsubmit="return confirm('Hapus asset ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-xs text-red-500 hover:text-red-700 font-medium">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Upload new assets form --}}
        @php $firstProduct = $infospot->products->first(); @endphp
        @if($firstProduct)
        <form action="{{ route('admin.products.assets.store', $firstProduct) }}" method="POST" enctype="multipart/form-data" id="asset-upload-form">
        @else
        <p class="text-xs text-rose-500">Error: No product found for this infospot. Please create one in the editor.</p>
        <form style="display:none">
        @endif
            @csrf

            <div class="border-t border-gray-200 pt-5">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-semibold text-gray-700">Upload File Baru</h4>
                    <button type="button" id="add-asset-row"
                            class="text-xs bg-green-50 hover:bg-green-100 text-green-700 border border-green-300 px-3 py-1.5 rounded font-medium transition-colors">
                        + Tambah File
                    </button>
                </div>

                <div id="asset-rows" class="space-y-3">
                    {{-- Dynamic rows will be added here --}}
                </div>

                <div id="no-rows-hint" class="text-sm text-gray-400 italic text-center py-4">
                    Klik "+ Tambah File" untuk menambahkan.
                </div>

                <div id="upload-actions" class="hidden mt-4">
                    <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded shadow-sm text-sm font-medium transition-colors">
                        Upload Semua
                    </button>
                </div>
            </div>
        </form>

        @if($errors->any())
            <div class="mt-3 text-sm text-red-600">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        /* ---- type toggle ---- */
        const typeSelector = document.getElementById('type-selector');
        const infoFields   = document.getElementById('info-fields');
        const navFields    = document.getElementById('nav-fields');

        function toggleFields() {
            if (typeSelector.value === 'info') {
                infoFields.classList.remove('hidden');
                navFields.classList.add('hidden');
            } else {
                infoFields.classList.add('hidden');
                navFields.classList.remove('hidden');
            }
        }

        typeSelector.addEventListener('change', toggleFields);
        toggleFields();

        /* ---- asset upload rows ---- */
        let rowIndex = 0;
        const assetRows    = document.getElementById('asset-rows');
        const addBtn       = document.getElementById('add-asset-row');
        const noRowsHint   = document.getElementById('no-rows-hint');
        const uploadActions = document.getElementById('upload-actions');

        function updateVisibility() {
            const hasRows = assetRows.children.length > 0;
            noRowsHint.classList.toggle('hidden', hasRows);
            uploadActions.classList.toggle('hidden', !hasRows);
        }

        addBtn.addEventListener('click', function () {
            const idx = rowIndex++;
            const row = document.createElement('div');
            row.className = 'flex flex-col sm:flex-row gap-2 p-3 border border-gray-200 rounded-lg bg-gray-50 asset-row';
            row.dataset.index = idx;

            row.innerHTML = `
                <div class="flex-1">
                    <label class="block text-xs text-gray-500 mb-1">Tipe File</label>
                    <select name="assets[${idx}][file_type]"
                            class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-400 file-type-select">
                        <option value="2d">🖼️ 2D (Gambar)</option>
                        <option value="3d">🧊 3D (GLB)</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs text-gray-500 mb-1">Label / Keterangan (opsional)</label>
                    <input type="text" name="assets[${idx}][label]" placeholder="cth: Tampak Depan"
                           class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                </div>
                <div class="flex-1">
                    <label class="block text-xs text-gray-500 mb-1">File</label>
                    <input type="file" name="assets[${idx}][file]" required
                           accept="image/*,.glb"
                           class="w-full text-sm file-input">
                </div>
                <div class="flex items-end">
                    <button type="button" class="remove-row text-red-400 hover:text-red-600 p-1.5 rounded transition-colors" title="Hapus baris">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            `;

            // Change accepted file types when type changes
            const typeSelect = row.querySelector('.file-type-select');
            const fileInput  = row.querySelector('.file-input');
            typeSelect.addEventListener('change', function () {
                if (this.value === '3d') {
                    fileInput.accept = '.glb';
                } else {
                    fileInput.accept = 'image/*';
                }
                fileInput.value = ''; // reset selection when type changes
            });

            // Remove row button
            row.querySelector('.remove-row').addEventListener('click', function () {
                row.remove();
                updateVisibility();
            });

            assetRows.appendChild(row);
            updateVisibility();
        });
    });
</script>
@endsection
