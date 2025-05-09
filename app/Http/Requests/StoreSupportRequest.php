<?php

namespace App\Http\Requests;

use App\Models\Support;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreSupportRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('support_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'email' => [
                'required',
            ],
            'phone' => [
                'string',
                'required',
            ],
            'photo' => [
                'array',
            ],
            'message' => [
                'required',
            ],
        ];
    }
}
