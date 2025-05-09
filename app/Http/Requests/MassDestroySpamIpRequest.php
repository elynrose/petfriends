<?php

namespace App\Http\Requests;

use App\Models\SpamIp;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroySpamIpRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('spam_ip_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:spam_ips,id',
        ];
    }
}
