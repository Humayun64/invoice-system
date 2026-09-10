<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $q = ActivityLog::query();
        if ($request->module) $q->where('module', $request->module);
        if ($request->action) $q->where('action', $request->action);
        if ($request->user)   $q->where('user_name', 'like', '%'.$request->user.'%');
        if ($request->search) $q->where(function($x) use ($request) {
            $x->where('record_ref','like','%'.$request->search.'%')
              ->orWhere('description','like','%'.$request->search.'%');
        });
        if ($request->from) $q->whereDate('created_at', '>=', $request->from);
        if ($request->to)   $q->whereDate('created_at', '<=', $request->to);

        $logs = $q->latest()->paginate(30)->withQueryString();
        return view('activity.index', compact('logs'));
    }

    public function show($id)
    {
        $log = ActivityLog::findOrFail($id);
        return view('activity.show', compact('log'));
    }
}
