<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FileUploadType: string implements HasLabel
{
    case ANY = 'any';
    case PDF = 'pdf';
    case IMAGE = 'image';
    case DOCUMENT = 'document';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ANY => 'Any File',
            self::PDF => 'PDF Document',
            self::IMAGE => 'Image File',
            self::DOCUMENT => 'Word/Excel Document',
        };
    }

    public function getValidationRules(): array
    {
        return match ($this) {
            self::ANY => ['file'],
            self::PDF => ['file', 'mimes:pdf', 'max:10240'],
            self::IMAGE => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            self::DOCUMENT => ['file', 'mimes:doc,docx,xls,xlsx,ppt,pptx', 'max:10240'],
        };
    }
}
