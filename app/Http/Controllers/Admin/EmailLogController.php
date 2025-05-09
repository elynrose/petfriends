<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyEmailLogRequest;
use App\Http\Requests\StoreEmailLogRequest;
use App\Http\Requests\UpdateEmailLogRequest;
use App\Models\EmailLog;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailLogController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('email_log_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $emailLogs = EmailLog::all();

        return view('admin.emailLogs.index', compact('emailLogs'));
    }

    public function create()
    {
        abort_if(Gate::denies('email_log_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.emailLogs.create');
    }

    public function store(StoreEmailLogRequest $request)
    {
        $emailLog = EmailLog::create($request->all());

        return redirect()->route('admin.email-logs.index');
    }

    public function edit(EmailLog $emailLog)
    {
        abort_if(Gate::denies('email_log_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.emailLogs.edit', compact('emailLog'));
    }

    public function update(UpdateEmailLogRequest $request, EmailLog $emailLog)
    {
        $emailLog->update($request->all());

        return redirect()->route('admin.email-logs.index');
    }

    public function show(EmailLog $emailLog)
    {
        abort_if(Gate::denies('email_log_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.emailLogs.show', compact('emailLog'));
    }

    public function destroy(EmailLog $emailLog)
    {
        abort_if(Gate::denies('email_log_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $emailLog->delete();

        return back();
    }

    public function massDestroy(MassDestroyEmailLogRequest $request)
    {
        $emailLogs = EmailLog::find(request('ids'));

        foreach ($emailLogs as $emailLog) {
            $emailLog->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
