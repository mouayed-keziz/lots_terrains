<?php

namespace App\Filament\Resources;

use App\Enums\FormField;
use App\Filament\Resources\PropertyResource\Pages;
use App\Filament\Resources\PropertyResource\RelationManagers;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Components\Builder as ComponentsBuilder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\PropertyResource\Components;
use App\Filament\Resources\PropertyResource\RelationManagers\SubmissionsRelationManager;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;

class PropertyResource extends Resource
{
    use NestedResource;

    public static function getAncestor(): ?Ancestor
    {
        return null;
    }


    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $modelLabel = 'Propriété';
    protected static ?string $pluralModelLabel = 'Propriétés';
    public static function getNavigationGroup(): ?string
    {
        return "Gestion des propriétés";
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Section::make('Informations Principales')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Titre')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('description')
                                    ->label('Description')
                                    ->rows(3)
                                    ->required(),
                                RichEditor::make('content')
                                    ->label('Contenu')
                                    ->required()
                                    ->toolbarButtons([
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'underline',
                                        'undo',
                                    ]),
                            ])
                            ->columns(1),

                        Section::make('Sections')
                            ->schema([
                                Forms\Components\Repeater::make('sections')
                                    ->collapsed()
                                    ->collapsible()
                                    ->addActionLabel('Ajouter une section')
                                    ->label('Sections')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Titre')
                                            ->required()
                                            ->translatable(),
                                        ComponentsBuilder::make('fields')
                                            ->collapsed()
                                            ->collapsible()
                                            ->label('Champs')
                                            ->addActionLabel('Ajouter un champ')
                                            ->blocks([
                                                Components\InputBlock::make(FormField::INPUT->value)
                                                    ->icon('heroicon-o-pencil')
                                                    ->label(function ($state) {
                                                        return FormField::INPUT->getLabel() . (isset($state['label']) && is_array($state['label']) && isset($state['label'][app()->getLocale()]) ? ": " . $state['label'][app()->getLocale()] : '');
                                                    }),
                                                Components\SelectBlock::make(FormField::SELECT->value)
                                                    ->icon('heroicon-o-bars-3')
                                                    ->label(function ($state) {
                                                        return FormField::SELECT->getLabel() . (isset($state['label']) && is_array($state['label']) && isset($state['label'][app()->getLocale()]) ? ": " . $state['label'][app()->getLocale()] : '');
                                                    }),
                                                Components\CheckboxBlock::make(FormField::CHECKBOX->value)
                                                    ->icon('heroicon-o-check-circle')
                                                    ->label(function ($state) {
                                                        return FormField::CHECKBOX->getLabel() . (isset($state['label']) && is_array($state['label']) && isset($state['label'][app()->getLocale()]) ? ": " . $state['label'][app()->getLocale()] : '');
                                                    }),
                                                Components\RadioBlock::make(FormField::RADIO->value)
                                                    ->icon('heroicon-o-check-circle')
                                                    ->label(function ($state) {
                                                        return FormField::RADIO->getLabel() . (isset($state['label']) && is_array($state['label']) && isset($state['label'][app()->getLocale()]) ? ": " . $state['label'][app()->getLocale()] : '');
                                                    }),
                                                Components\UploadBlock::make(FormField::UPLOAD->value)
                                                    ->icon('heroicon-o-arrow-up-on-square-stack')
                                                    ->label(function ($state) {
                                                        return FormField::UPLOAD->getLabel() . (isset($state['label']) && is_array($state['label']) && isset($state['label'][app()->getLocale()]) ? ": " . $state['label'][app()->getLocale()] : '');
                                                    }),
                                            ]),
                                    ]),
                            ])
                            ->collapsible(),
                    ])
                    ->columnSpan(3),

                Forms\Components\Group::make()
                    ->schema([
                        Section::make('Image Principale')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('image')
                                    ->label('')
                                    ->collection('image')
                                    ->image()
                                    ->imageEditor()
                            ]),
                    ])
                    ->columnSpan(2),
            ])
            ->columns(5);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
                    ->label('Image')
                    ->collection('image'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date de création')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Date de modification')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make("submissions")
                    ->label("Gérer les soumissions")
                    ->url(function ($record) {
                        return "/admin/properties/{$record->id}/submissions";
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'view' => Pages\ViewProperty::route('/{record}'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
            'submissions' => Pages\ManagePropertySubmissions::route('/{record}/submissions'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
