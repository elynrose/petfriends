<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Carbon\Carbon;

class StoreBookingRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('booking_create');
    }

    public function rules()
    {
        $rules = [
            'pet_id' => [
                'required',
                'integer',
            ],
            'status' => [
                'required',
                'in:pending,approved,rejected,completed,new',
            ],
            'user_id' => [
                'required',
                'integer',
            ],
        ];

        // Only require dates if the pet is available
        $pet = \App\Models\Pet::find($this->input('pet_id'));
        if (!$pet || !$pet->not_available) {
            $rules['from'] = [
                'required',
                'date',
            ];
            $rules['from_time'] = [
                'required',
                'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/',
            ];
            $rules['to'] = [
                'required',
                'date',
                'after_or_equal:from',
                function ($attribute, $value, $fail) {
                    $today = Carbon::today();
                    $endDate = Carbon::parse($value);
                    
                    if ($endDate->startOfDay()->lt($today)) {
                        $fail('The booking end date cannot be in the past.');
                    }
                },
            ];
            $rules['to_time'] = [
                'required',
                'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/',
                function ($attribute, $value, $fail) {
                    $from = Carbon::parse($this->input('from') . ' ' . $this->input('from_time'));
                    $to = Carbon::parse($this->input('to') . ' ' . $value);
                    
                    if ($to->lte($from)) {
                        $fail('The end date and time must be after the start date and time.');
                    }
                },
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'from_time.regex' => 'The start time must be in 24-hour format (HH:mm).',
            'to_time.regex' => 'The end time must be in 24-hour format (HH:mm).',
            'status.in' => 'Please select a valid booking status.',
            'to.after_or_equal' => 'The end date must be the same as or after the start date.',
        ];
    }
}
