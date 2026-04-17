@extends('components.admin-layout')

@section('header', 'Tours Management')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">All Tours</h3>
        <a href="{{ route('admin.tours.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow-sm text-sm font-medium transition-colors">
            + Create New Tour
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm border-b">
                    <th class="p-4 font-medium">ID</th>
                    <th class="p-4 font-medium">Name</th>
                    <th class="p-4 font-medium">Created At</th>
                    <th class="p-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($tours as $tour)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4 text-gray-500">{{ $tour->id }}</td>
                        <td class="p-4 font-medium text-gray-800">{{ $tour->name }}</td>
                        <td class="p-4 text-gray-500">{{ $tour->created_at->format('M d, Y') }}</td>
                        <td class="p-4 text-right space-x-2">
                            <a href="{{ url('/') }}" target="_blank" class="text-green-600 hover:underline">View Live</a>
                            <a href="{{ route('admin.tours.show', $tour) }}" class="text-blue-600 hover:underline">Manage Scenes</a>
                            <a href="{{ route('admin.tours.edit', $tour) }}" class="text-slate-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.tours.destroy', $tour) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this tour? This will delete all scenes and infospots inside it.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-500 bg-white">
                            No tours found. Please create one to get started.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
