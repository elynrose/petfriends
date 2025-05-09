<?php

namespace App\Http\Requests;

use App\Models\Pet;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdatePetRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('pet_edit');
    }

    public function rules()
    {
        return [
            'photo' => [
                'array',
                'required',
            ],
            'photo.*' => [
                'required',
            ],
            'type' => [
                'required',
            ],
            'name' => [
                'string',
                'required',
            ],
            'age' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'gender' => [
                'required',
            ],
            'notes' => [
                'string',
                'nullable',
            ],
            'from' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'from_time' => [
                'date_format:' . config('panel.time_format'),
                'nullable',
            ],
            'to' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'to_time' => [
                'date_format:' . config('panel.time_format'),
                'nullable',
            ],
        ];
    }
}
