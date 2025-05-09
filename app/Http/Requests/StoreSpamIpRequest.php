<?php

namespace App\Http\Requests;

use App\Models\SpamIp;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreSpamIpRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('spam_ip_create');
    }

    public function rules()
    {
        return [
            'ip_address' => [
                'string',
                'nullable',
            ],
        ];
    }
}
