<?php

namespace App\Filament\Resources\PropertyResource\Components;

use App\Enums\FileUploadType;
use App\Filament\Resources\PropertyResource\Components\Core\DescriptionInput;
use App\Filament\Resources\PropertyResource\Components\Core\LabelInput;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Group;

class UploadBlock
{
    public static function make(string $name)
    {
        return Block::make($name)
            ->columns(2)
            ->schema([
                LabelInput::make(),
                DescriptionInput::make(),

                Select::make('file_type')
                    ->columnSpan(2)
                    ->label('File Type')
                    ->required()
                    ->options(FileUploadType::class),

            ]);
    }
}
