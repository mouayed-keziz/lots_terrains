@props(['data', 'answerPath'])

<div class="mb-6">
    <label class="block text-gray-700 text-sm font-medium mb-2">
        {{ $data['label'][app()->getLocale()] ?? '' }}
        @if ($data['required'] ?? false)
            <span class="text-red-500">*</span>
        @endif
    </label>
    @if ($data['description'][app()->getLocale()] ?? false)
        <p class="text-gray-500 text-sm mb-2">{{ $data['description'][app()->getLocale()] }}</p>
    @endif
    <div class="space-y-2">
        @foreach ($data['options'] as $option)
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="checkboxes[]" wire:model.lazy="formData.{{ $answerPath }}"
                    value="{{ $option['option'][app()->getLocale()] }}"
                    class="h-4 w-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500"
                    @if ($data['required'] ?? false) required @endif>
                <span class="ml-2 text-sm text-gray-700">{{ $option['option'][app()->getLocale()] }}</span>
            </label>
        @endforeach
    </div>
    @error("formData.{{ $answerPath }}")
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
