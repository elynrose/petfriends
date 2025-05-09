<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreBookingRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('booking_create');
    }

    public function rules()
    {
        return [
            'pet_id' => [
                'required',
                'integer',
            ],
            'status' => [
                'required',
            ],
            'photos' => [
                'array',
            ],
            'user_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
