<?php

namespace App\Http\Requests;

use App\Models\SpamIp;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateSpamIpRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('spam_ip_edit');
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
