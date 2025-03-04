<?php


namespace App\Filament\Resources\PropertyResource\Components\Core;

use Filament\Forms\Components\TextInput;

class LabelInput
{
    public static function make()
    {
        return TextInput::make('label')
            ->label("Label")
            ->required()
            ->translatable();
    }
}
