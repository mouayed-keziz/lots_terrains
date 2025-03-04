<?php


namespace App\Filament\Resources\PropertyResource\Components\Core;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Lang;

class Options
{
    public static function make()
    {

        return Repeater::make('options')
            ->label("Options")
            ->columnSpan(2)
            ->collapsible()
            ->collapsed()
            ->itemLabel(function ($state) {
                return "Option" . ($state['option'] ? ": " . ($state['option'][app()->getLocale()] ?? '') : '');
            })
            ->schema([
                TextInput::make('option')
                    ->label("Option")
                    ->required()
                    ->translatable()
            ]);
    }
}
