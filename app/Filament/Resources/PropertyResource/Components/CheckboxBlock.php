<?php

namespace App\Filament\Resources\PropertyResource\Components;

use App\Filament\Resources\PropertyResource\Components\Core\DescriptionInput;
use App\Filament\Resources\PropertyResource\Components\Core\LabelInput;
use App\Filament\Resources\PropertyResource\Components\Core\Options;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;

class CheckboxBlock
{
    public static function make(string $name)
    {
        return Block::make($name)
            ->columns(2)
            ->schema([
                LabelInput::make(),
                DescriptionInput::make(),
                Options::make(),
            ]);
    }
}
