<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroySpamIpRequest;
use App\Http\Requests\StoreSpamIpRequest;
use App\Http\Requests\UpdateSpamIpRequest;
use App\Models\SpamIp;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SpamIpController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('spam_ip_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $spamIps = SpamIp::all();

        return view('admin.spamIps.index', compact('spamIps'));
    }

    public function create()
    {
        abort_if(Gate::denies('spam_ip_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.spamIps.create');
    }

    public function store(StoreSpamIpRequest $request)
    {
        $spamIp = SpamIp::create($request->all());

        return redirect()->route('admin.spam-ips.index');
    }

    public function edit(SpamIp $spamIp)
    {
        abort_if(Gate::denies('spam_ip_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.spamIps.edit', compact('spamIp'));
    }

    public function update(UpdateSpamIpRequest $request, SpamIp $spamIp)
    {
        $spamIp->update($request->all());

        return redirect()->route('admin.spam-ips.index');
    }

    public function show(SpamIp $spamIp)
    {
        abort_if(Gate::denies('spam_ip_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.spamIps.show', compact('spamIp'));
    }

    public function destroy(SpamIp $spamIp)
    {
        abort_if(Gate::denies('spam_ip_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $spamIp->delete();

        return back();
    }

    public function massDestroy(MassDestroySpamIpRequest $request)
    {
        $spamIps = SpamIp::find(request('ids'));

        foreach ($spamIps as $spamIp) {
            $spamIp->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
