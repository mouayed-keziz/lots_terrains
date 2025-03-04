<?php

namespace App\Actions;

use App\Models\Property;
use App\Enums\FormField;
use App\Enums\FileUploadType;
use App\Enums\FormInputType;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;


class FormActions
{
    /**
     * Initialize form data structure based on the property's visitor form
     */
    public function initFormData(Property $property): array
    {
        if (!$property->visitorForm) {
            return [];
        }

        $formData = [];

        // Create the structured formData with sections and fields
        foreach ($property->visitorForm->sections as $section) {
            $sectionData = [
                'title' => $section['title'],
                'fields' => []
            ];

            foreach ($section['fields'] as $field) {
                $fieldType = FormField::tryFrom($field['type']);
                if ($fieldType) {
                    $sectionData['fields'][] = $fieldType->initializeField($field);
                }
            }

            $formData[] = $sectionData;
        }

        return $formData;
    }

    /**
     * Get validation rules for the form
     */
    public function getValidationRules(Property $property, int $currentStep = null): array
    {
        $rules = [];

        if (!$property->visitorForm) {
            return $rules;
        }

        foreach ($property->visitorForm->sections as $sectionIndex => $section) {
            foreach ($section['fields'] as $fieldIndex => $field) {
                $fieldKey = "formData.{$sectionIndex}.fields.{$fieldIndex}.answer";

                $fieldType = FormField::tryFrom($field['type']);
                if ($fieldType) {
                    $rules[$fieldKey] = implode('|', $fieldType->getValidationRules($field));
                }
            }
        }

        return $rules;
    }

    /**
     * Process form data before saving
     */
    public function processFormData(array $formData): array
    {
        if (empty($formData)) {
            return $formData;
        }

        $processedFormData = $formData;

        foreach ($processedFormData as $sectionIndex => $section) {
            if (!isset($section['fields']) || !is_array($section['fields'])) {
                continue;
            }

            foreach ($section['fields'] as $fieldIndex => $field) {
                if (!isset($field['type']) || !isset($field['answer'])) {
                    continue;
                }

                $fieldType = FormField::tryFrom($field['type']);
                if ($fieldType) {
                    $processedFormData[$sectionIndex]['fields'][$fieldIndex]['answer'] =
                        $fieldType->processFieldAnswer($field['answer'], $field['data'] ?? []);
                }
            }
        }

        return $processedFormData;
    }

    /**
     * Process form data for saving, handling file uploads through Spatie Media Library
     */
    public function processFormDataForSubmission(array $formData): array
    {
        if (empty($formData)) {
            return $formData;
        }

        $processedFormData = $formData;
        $filesToProcess = [];

        // First pass: identify all files and replace them with unique identifiers
        foreach ($processedFormData as $sectionIndex => $section) {
            if (!isset($section['fields']) || !is_array($section['fields'])) {
                continue;
            }

            foreach ($section['fields'] as $fieldIndex => $field) {
                // Process file uploads
                if (isset($field['type']) && $field['type'] === FormField::UPLOAD->value && isset($field['answer'])) {
                    if ($field['answer'] instanceof TemporaryUploadedFile) {
                        // Generate unique identifier for the file
                        $fileId = (string) Str::uuid();

                        // Save file information for later processing
                        $filesToProcess[] = [
                            'file' => $field['answer'],
                            'fileId' => $fileId,
                            'fieldData' => $field['data'] ?? [],
                        ];

                        // Replace the file in form data with the identifier
                        $processedFormData[$sectionIndex]['fields'][$fieldIndex]['answer'] = $fileId;
                    }
                }
            }
        }

        // Process choice fields and other data types
        $processedFormData = $this->processFormData($processedFormData);

        // Return both the processed form data and the files to be processed
        return [
            'processedData' => $processedFormData,
            'filesToProcess' => $filesToProcess
        ];
    }

    /**
     * Save the form submission to the database
     */
    public function saveFormSubmission(Property $property, array $formData): bool
    {
        try {
            // Process the form data (handle file uploads, translatable fields, etc.)
            $processResult = $this->processFormDataForSubmission($formData);
            $processedData = $processResult['processedData'];
            $filesToProcess = $processResult['filesToProcess'];

            // Get visitor ID if user is authenticated as a visitor
            $visitorId = null;
            if (auth('visitor')->check()) {
                $visitorId = auth('visitor')->user()->id;
            }

            // Create a new submission with nullable visitor_id
            $submission = \App\Models\Submission::create([
                'visitor_id' => $visitorId,
                'property_id' => $property->id,
                'answers' => $processedData,
                'status' => 'approved',
            ]);
            Log::info("Submission created: {$submission->id}");

            // Process any files by adding them to the Spatie Media Library
            foreach ($filesToProcess as $fileInfo) {
                $media = $submission->addMedia($fileInfo['file']->getRealPath())
                    ->usingFileName($fileInfo['file']->getClientOriginalName())
                    ->withCustomProperties([
                        'fileId' => $fileInfo['fileId'],
                        'fileType' => $fileInfo['fieldData']['file_type'] ?? null,
                        'fieldLabel' => $fileInfo['fieldData']['label'] ?? null,
                    ])
                    ->toMediaCollection('attachments');
                Log::info("Media added: {$media->id} with fileId: {$fileInfo['fileId']}");
            }

            return true;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }
}
