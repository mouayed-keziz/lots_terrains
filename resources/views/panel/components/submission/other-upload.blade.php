{{-- Other File Types Upload Display Component --}}
<div class="space-y-2">
    <div class="flex items-center gap-2">
        <x-heroicon-o-document class="w-5 h-5" />
        <span>{{ $fileType ?? 'Type non défini' }} </span>

    </div>
    <div class="flex items-center gap-2">
        <a href="{{ $fileUrl }}" class="text-primary-500 hover:text-primary-400 inline-flex items-center gap-1"
            download>
            <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
            Télécharger
        </a>
    </div>
</div>
