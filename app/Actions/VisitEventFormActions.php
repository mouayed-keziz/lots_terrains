<?php

namespace App\Actions;

use App\Models\Property;
use App\Enums\FormField;
use Illuminate\Support\Facades\Log;

class VisitEventFormActions extends FormActions
{
    /**
     * Initialize form data structure specifically for visit events
     */
    public function initFormData(Property $property): array
    {
        // Use the base implementation from FormActions
        return parent::initFormData($property);
    }

    /**
     * Get validation rules for visit event forms
     */
    public function getValidationRules(Property $property, int $currentStep = null): array
    {
        // Use the base implementation from FormActions
        return parent::getValidationRules($property, $currentStep);
    }

    /**
     * Process form data before saving (specific to visit events)
     */
    public function processFormData(array $formData): array
    {
        // Use the base implementation from FormActions
        return parent::processFormData($formData);
    }

    /**
     * Save the form submission to the database for a visit event
     */
    public function saveFormSubmission(Property $property, array $formData): bool
    {
        try {
            // Use the base implementation from FormActions
            $result = parent::saveFormSubmission($property, $formData);

            // Add any visit-event specific processing here if needed

            return $result;
        } catch (\Exception $e) {
            Log::error('Visit event form submission failed: ' . $e->getMessage());
            report($e);
            return false;
        }
    }
}
