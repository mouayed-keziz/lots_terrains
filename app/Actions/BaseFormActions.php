<?php

namespace App\Actions;

use App\Models\Property;
use App\Enums\FormField;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

abstract class BaseFormActions
{
    /**
     * Initialize a single field structure
     */
    protected function initializeField(array $field): array
    {
        $fieldData = [
            'type' => $field['type'],
            'data' => [
                'label' => $field['data']['label'],
                'description' => $field['data']['description'] ?? null,
            ],
            'answer' => null
        ];

        // Copy any additional field-specific data
        if (isset($field['data']['type'])) {
            $fieldData['data']['type'] = $field['data']['type'];
        }
        if (isset($field['data']['required'])) {
            $fieldData['data']['required'] = $field['data']['required'];
        }
        if (isset($field['data']['options'])) {
            $fieldData['data']['options'] = $field['data']['options'];
        }
        if (isset($field['data']['file_type'])) {
            $fieldData['data']['file_type'] = $field['data']['file_type'];
        }

        // Initialize answer based on field type
        switch ($field['type']) {
            case FormField::CHECKBOX->value:
                $fieldData['answer'] = [];
                break;
            case FormField::UPLOAD->value:
                $fieldData['answer'] = null;
                break;
            default:
                $fieldData['answer'] = '';
        }

        return $fieldData;
    }

    /**
     * Get validation rules for a specific field
     */
    protected function getFieldValidationRules(array $field): string
    {
        $fieldRules = [];

        // Check if field is required
        if (Arr::get($field, 'data.required', false)) {
            $fieldRules[] = 'required';
        } else {
            $fieldRules[] = 'nullable';
        }

        // Add specific validation rules based on field type
        switch ($field['type']) {
            case FormField::INPUT->value:
                $fieldRules = array_merge($fieldRules, $this->getInputFieldRules($field));
                break;
            case FormField::UPLOAD->value:
                $fieldRules = array_merge($fieldRules, $this->getFileUploadRules($field));
                break;
            case FormField::CHECKBOX->value:
                $fieldRules[] = 'array';
                break;
        }

        return implode('|', $fieldRules);
    }

    /**
     * Get validation rules for input fields based on input type
     */
    protected function getInputFieldRules(array $field): array
    {
        $rules = [];

        switch ($field['data']['type'] ?? '') {
            case \App\Enums\FormInputType::EMAIL->value:
                $rules[] = 'email';
                break;
            case \App\Enums\FormInputType::NUMBER->value:
                $rules[] = 'numeric';
                break;
            case \App\Enums\FormInputType::PHONE->value:
                $rules[] = 'string';
                break;
            case \App\Enums\FormInputType::DATE->value:
                $rules[] = 'date';
                break;
            default:
                $rules[] = 'string';
                break;
        }

        return $rules;
    }

    /**
     * Get validation rules for file upload fields
     */
    protected function getFileUploadRules(array $field): array
    {
        $rules = ['file'];

        // Add file type validation based on field definition
        $fileType = $field['data']['file_type'] ?? \App\Enums\FileUploadType::ANY;

        if ($fileType === \App\Enums\FileUploadType::IMAGE) {
            $rules[] = 'mimes:jpg,jpeg,png,gif,bmp,webp';
            $rules[] = 'max:10240'; // 10MB max for images
        } elseif ($fileType === \App\Enums\FileUploadType::PDF) {
            $rules[] = 'mimes:pdf';
            $rules[] = 'max:20480'; // 20MB max for PDFs
        } else {
            // For any file type, set a general size limit
            $rules[] = 'max:25600'; // 25MB general limit
        }

        return $rules;
    }

    /**
     * Find the option translations for a given answer value
     */
    protected function findOptionTranslations(array $options, $answerValue): array
    {
        $currentLocale = app()->getLocale();

        // Find the option with matching value in current locale
        foreach ($options as $option) {
            if (isset($option['option'][$currentLocale]) && $option['option'][$currentLocale] === $answerValue) {
                return $option['option'];
            }
        }

        // Fallback: Return the answer value keyed by current locale
        return [$currentLocale => $answerValue];
    }

    /**
     * Process form data for saving, handling file uploads through Spatie Media Library
     */
    protected function processFormDataCommon(array $formData, array $processedFormData): array
    {
        $filesToProcess = [];

        // Process choice fields and other data types
        $processedFormData = $this->processFormData($processedFormData);

        // Return both the processed form data and the files to be processed
        return [
            'processedData' => $processedFormData,
            'filesToProcess' => $filesToProcess
        ];
    }

    /**
     * Process form data before saving
     */
    abstract public function processFormData(array $formData): array;

    /**
     * Process form data for submission including file handling
     */
    abstract public function processFormDataForSubmission(array $formData): array;

    /**
     * Initialize form data structure
     */
    abstract public function initFormData(Property $property): array;

    /**
     * Get validation rules for the form
     */
    abstract public function getValidationRules(Property $property, int $currentStep = null): array;

    /**
     * Save the form submission to the database
     */
    abstract public function saveFormSubmission(Property $property, array $formData): bool;
}
