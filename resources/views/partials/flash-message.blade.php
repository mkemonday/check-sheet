<!-- Flash Message -->
@if (session()->has('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
    <span class="block sm:inline">{{ session('success') }}</span>
</div>
@endif

@if (session()->has('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
    <span class="block sm:inline">{{ session('error') }}</span>
</div>
@endif