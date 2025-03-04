<?php
use Livewire\Volt\Component;
use App\Models\Property;
use App\Actions\FormActions;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    // Event and form data
    public Property $property;
    public array $formData = [];
    public bool $formSubmitted = false;
    public string $successMessage = '';

    // Define validation rules as a property
    protected function rules()
    {
        $actions = new FormActions();
        return $actions->getValidationRules($this->property);
    }

    public function mount(Property $property)
    {
        $this->property = $property;

        $submission = $property
            ->submissions()
            ->where('user_id', auth()->id())
            ->first();

        if ($submission) {
            $this->formSubmitted = true;
            $this->successMessage = __('Vous avez déjà soumis ce formulaire.');
        } else {
            $this->initFormData();
        }
    }

    protected function initFormData()
    {
        $actions = new FormActions();
        $this->formData = $actions->initFormData($this->property);
    }

    public function submitForm()
    {
        $actions = new FormActions();

        try {
            // Validate using the rules
            $this->validate($this->rules());

            $processResult = $actions->processFormDataForSubmission($this->formData);

            $success = $actions->saveFormSubmission($this->property, $this->formData);

            if ($success) {
                $this->formSubmitted = true;
                $this->successMessage = __('Formulaire soumis avec succès !');
            } else {
                session()->flash('error', __('Une erreur s\'est produite lors de la soumission du formulaire. Veuillez réessayer.'));
            }
        } catch (\Exception $e) {
            report($e);
            session()->flash('error', __('Quelque chose s\'est mal passé'));
        }
    }
}; ?>

<div class="container mx-auto py-8 px-4">
    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif
    @if (!$this->formSubmitted && !session('error'))
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        {{ __('Veuillez remplir le formulaire pour cette propriété.') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
    @if ($formSubmitted)
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4 shadow-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ $successMessage }}</p>
                </div>
            </div>
        </div>
    @else
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form wire:submit.prevent="submitForm">
                        @if (!empty($formData))
                            @foreach ($formData as $sectionIndex => $section)
                                @include('website.components.forms.input.section_title', [
                                    'title' => $section['title'][app()->getLocale()] ?? $section['title']['fr'],
                                ])

                                @foreach ($section['fields'] as $fieldIndex => $field)
                                    @php
                                        $answerPath = "{$sectionIndex}.fields.{$fieldIndex}.answer";
                                    @endphp

                                    @include('website.components.forms.fields', [
                                        'fields' => [$field],
                                        'answerPath' => $answerPath,
                                    ])

                                    @error("formData.{$sectionIndex}.fields.{$fieldIndex}.answer")
                                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                @endforeach

                                <div class="h-8"></div>
                            @endforeach

                            <div class="flex justify-end mt-6">
                                <button type="submit"
                                    class="inline-flex items-center disabled:opacity-50 disabled:cursor-not-allowed
                                     px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="submitForm">{{ __('Soumettre') }}</span>
                                    <span wire:loading wire:target="submitForm"
                                        class="flex flex-row justify-center items-center gap-2">
                                        {{ __('Soumission en cours...') }}
                                    </span>
                                </button>
                            </div>
                        @else
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            {{ __('Aucun formulaire disponible pour cette propriété.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
