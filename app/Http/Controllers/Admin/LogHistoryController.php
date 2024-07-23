<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyLogHistoryRequest;
use App\Http\Requests\StoreLogHistoryRequest;
use App\Http\Requests\UpdateLogHistoryRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogHistoryController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('log_history_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.logHistories.index');
    }

    public function create()
    {
        abort_if(Gate::denies('log_history_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.logHistories.create');
    }

    public function store(StoreLogHistoryRequest $request)
    {
        $logHistory = LogHistory::create($request->all());

        return redirect()->route('admin.log-histories.index');
    }

    public function edit(LogHistory $logHistory)
    {
        abort_if(Gate::denies('log_history_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.logHistories.edit', compact('logHistory'));
    }

    public function update(UpdateLogHistoryRequest $request, LogHistory $logHistory)
    {
        $logHistory->update($request->all());

        return redirect()->route('admin.log-histories.index');
    }

    public function show(LogHistory $logHistory)
    {
        abort_if(Gate::denies('log_history_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.logHistories.show', compact('logHistory'));
    }

    public function destroy(LogHistory $logHistory)
    {
        abort_if(Gate::denies('log_history_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $logHistory->delete();

        return back();
    }

    public function massDestroy(MassDestroyLogHistoryRequest $request)
    {
        $logHistories = LogHistory::find(request('ids'));

        foreach ($logHistories as $logHistory) {
            $logHistory->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
