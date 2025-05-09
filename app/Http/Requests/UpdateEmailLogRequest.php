<?php

namespace App\Http\Requests;

use App\Models\EmailLog;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateEmailLogRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('email_log_edit');
    }

    public function rules()
    {
        return [
            'message' => [
                'required',
            ],
        ];
    }
}
