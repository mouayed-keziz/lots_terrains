<?php


namespace App\Filament\Resources\PropertyResource\Components\Core;

use Filament\Forms\Components\TextInput;

class DescriptionInput
{
    public static function make()
    {
        return TextInput::make('description')
            ->label("Description")
            ->translatable();
    }
}
