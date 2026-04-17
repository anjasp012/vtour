@extends('components.admin-layout')

@section('header', 'Create Tour')

@section('content')
<div class="bg-white rounded-lg shadow-sm max-w-2xl">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">New Virtual Tour</h3>
    </div>
    
    <div class="p-6">
        <form action="{{ route('admin.tours.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tour Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('description') }}</textarea>
                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div class="flex items-center space-x-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow-sm text-sm font-medium transition-colors">
                    Create Tour
                </button>
                <a href="{{ route('admin.tours.index') }}" class="text-gray-500 hover:underline text-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
