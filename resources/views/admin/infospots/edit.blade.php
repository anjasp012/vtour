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
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Replace 3D Model (.glb file) - Optional</label>
                    @if($infospot->model_path)
                        <div class="text-sm text-gray-600 mb-1">Current: {{ basename($infospot->model_path) }}</div>
                    @endif
                    <input type="file" name="model_file" accept=".glb" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelector = document.getElementById('type-selector');
        const infoFields = document.getElementById('info-fields');
        const navFields = document.getElementById('nav-fields');

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
    });
</script>
@endsection
