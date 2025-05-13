<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePetRequest extends FormRequest
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
            'notes' => 'nullable|string',
            'not_available' => 'boolean',
            'photo' => 'nullable|array',
        ];

        // If pet is available (not_available is false), require dates and times
        if (!$this->input('not_available')) {
            $rules['from'] = 'required|date';
            $rules['from_time'] = 'required|date_format:H:i';
            $rules['to'] = 'required|date|after_or_equal:from';
            $rules['to_time'] = 'required|date_format:H:i';
            
            // If same day, ensure to_time is after from_time
            if ($this->input('from') === $this->input('to')) {
                $rules['to_time'] .= '|after:from_time';
            }
        }

        return $rules;
    }
}
