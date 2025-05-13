<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePetAvailabilityRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'gender' => 'required|string|max:255',
            'not_available' => 'boolean',
            'photo' => 'nullable|array',
        ];

        // If pet is available (not_available is false), require dates and times
        if (!$this->input('not_available')) {
            $rules['from'] = 'required|date|after:now';
            $rules['from_time'] = 'required|date_format:H:i|after_or_equal:06:00|before:22:00';
            $rules['to'] = 'required|date|after_or_equal:from';
            $rules['to_time'] = 'required|date_format:H:i|after:from_time|after_or_equal:06:00|before:22:00';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'from.after' => 'Start date must be in the future.',
            'from_time.after_or_equal' => 'Start time must be after or equal to 6:00 AM.',
            'from_time.before' => 'Start time must be before 10:00 PM.',
            'to_time.after_or_equal' => 'End time must be after or equal to 6:00 AM.',
            'to_time.before' => 'End time must be before 10:00 PM.',
        ];
    }
} 