@extends('components.admin-layout')

@section('header', 'General Settings')

@section('content')
<div class="p-6 space-y-6 animate-fade-in">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tighter uppercase">General Settings</h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Configure global application behavior</p>
        </div>
    </div>

    <div class="max-w-2xl">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            @csrf
            <div class="p-6 space-y-8">
                <!-- Coming Soon Toggle -->
                <div class="flex items-center justify-between">
                    <div class="space-y-1">
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-tight">Coming Soon Mode</h3>
                        <p class="text-[10px] text-slate-400 font-medium">When enabled, public visitors will see the maintenance page. Admins can still preview the tour.</p>
                    </div>
                    
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="coming_soon" value="false">
                        <input type="checkbox" name="coming_soon" value="true" class="sr-only peer" {{ ($settings['coming_soon'] ?? 'false') === 'true' ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="pt-6 border-t border-slate-100">
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 flex gap-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fas fa-info-circle text-blue-600"></i>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-slate-700 uppercase tracking-wider">Preview Bypass Active</p>
                            <p class="text-[9px] text-slate-500 leading-relaxed">The "Preview" link in the navbar uses a secure session bypass. You can always view the tour while logged in as an administrator.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 px-6 py-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-slate-900 text-white text-[10px] font-bold rounded shadow-lg hover:bg-slate-800 transition-all uppercase tracking-widest">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
