@props(['data', 'answerPath'])

<div class="mb-4">
    <label class="block text-gray-700 text-sm font-medium mb-2">
        {{ $data['label'][app()->getLocale()] ?? '' }}
        @if ($data['required'] ?? false)
            <span class="text-red-500">*</span>
        @endif
    </label>
    <input type="email" placeholder="{{ $data['description'][app()->getLocale()] ?? '' }}"
        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        wire:model.lazy="formData.{{ $answerPath }}" @if ($data['required'] ?? false) required @endif>
    @error("formData.{{ $answerPath }}")
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
