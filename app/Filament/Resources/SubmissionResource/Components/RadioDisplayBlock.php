<?php

namespace App\Filament\Resources\SubmissionResource\Components;

use App\Enums\FormField;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\View;

class RadioDisplayBlock extends Block
{
    public static function make(string $name = 'radio'): static
    {
        return parent::make($name)
            ->icon(FormField::RADIO->getIcon())
            ->schema([
                View::make('filament.forms.components.answer-display')
                    ->label(__("panel/visitor_submissions.field_answer"))
                    ->viewData([
                        'type' => 'radio',
                        'label' => function ($get) {
                            $label = $get('data.label');
                            return $label[app()->getLocale()] ?? '';
                        },
                        'answer' => function ($get) {
                            $answer = $get('answer');
                            return $answer[app()->getLocale()] ?? $answer;
                        }
                    ]),
            ]);
    }
}
