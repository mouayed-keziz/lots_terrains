<?php

namespace App\Filament\Resources\PropertyResource\Pages;

// use App\Filament\Components\VisitorSubmissionFormDisplay;
use App\Filament\Resources\PropertyResource;
use App\Filament\Resources\PropertyResource\Components\SubmissionDisplay;
use App\Models\Submission;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\Concerns;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ViewSubmission extends ViewRecord
{
    // use HasPageSidebar;
    // use NestedPage;

    protected static string $resource = PropertyResource::class;

    // protected static string $view = 'filament.resources.property-resource.pages.view-visitor-submission';

    /**
     * Get a fresh instance of the model represented by the resource.
     */
    protected function resolveRecord(int | string $key): Model
    {
        // Get route parameters to resolve the visitor submission
        $PropertyId = request()->route('record');
        $SubmissionId = request()->route('submission');

        // Find the visitor submission that belongs to the right event announcement
        $submission = Submission::query()
            ->where('id', $SubmissionId)
            ->where('property_id', $PropertyId)
            ->firstOrFail();

        return $submission;
    }

    /**
     * Configure the infolist
     *
     * @param Infolist $infolist
     * @return Infolist
     */
    public function infolist(Infolist $infolist): Infolist
    {
        $answers = $this->getRecord()->answers ?? [];

        // Get form definitions from the event announcement's visitor form
        $property = $this->getRecord()->property;
        $sections = $property->sections ?? [];

        // Merge form definition with answers
        if (!empty($sections) && !empty($answers)) {
            // Deep merge the answers into the form sections
            // This ensures we follow the same structure but with answers included
            foreach ($sections as $sIndex => $section) {
                foreach ($section['fields'] ?? [] as $fIndex => $field) {
                    if (isset($answers[$sIndex]['fields'][$fIndex]['answer'])) {
                        $sections[$sIndex]['fields'][$fIndex]['answer'] = $answers[$sIndex]['fields'][$fIndex]['answer'];
                    }
                }
            }
        }

        // Visitor details section (french labels only)
        $userDetailsSection = Section::make('Utilisateur')
            ->schema([
                \Filament\Infolists\Components\TextEntry::make('user.name')
                    ->label("Nom"),
                \Filament\Infolists\Components\TextEntry::make('user.email')
                    ->label("Email"),
                // \Filament\Infolists\Components\TextEntry::make('status')
                //     ->label(__('panel/visitor_submissions.fields.status'))
                //     ->badge()
                //     ->color(fn(string $state): string => match ($state) {
                //         'pending' => 'warning',
                //         'approved' => 'success',
                //         'rejected' => 'danger',
                //         default => 'gray',
                //     }),
                \Filament\Infolists\Components\TextEntry::make('created_at')
                    ->label("Date de soumission")
                    ->dateTime(),
            ])
            ->columns(2);

        // Create the infolist schema
        return $infolist
            ->schema([
                $userDetailsSection,
                ...(SubmissionDisplay::make($sections)),
            ]);
    }

    /**
     * Get the header actions for the page
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Get the title for this page.
     */
    public function getTitle(): string
    {
        return $this->getRecord()->property->name . ' - ' . $this->getRecord()->user->name;
    }
}
