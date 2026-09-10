<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ActivityLog;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::withCount(['invoices','quotations'])->orderBy('name')->get();
        return view('clients.index', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:200']);
        $cl = Client::create($request->only(['name','designation','company','address','postal_code','phone','email']));
        ActivityLog::record('created', 'client', $cl->id, $cl->name, 'Added client '.$cl->name, null, $cl->toArray());
        return redirect()->route('clients.index')->with('success', 'Client added.');
    }

    public function edit($id)
    {
        $client  = Client::findOrFail($id);
        $clients = Client::withCount(['invoices','quotations'])->orderBy('name')->get();
        return view('clients.index', compact('clients', 'client'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:200']);
        $cl = Client::findOrFail($id); $before = $cl->toArray();
        $cl->update($request->only(['name','designation','company','address','postal_code','phone','email']));
        ActivityLog::record('updated', 'client', $cl->id, $cl->name, 'Updated client '.$cl->name, $before, $cl->fresh()->toArray());
        return redirect()->route('clients.index')->with('success', 'Client updated.');
    }

    public function destroy($id)
    {
        $client = Client::withCount(['invoices','quotations'])->findOrFail($id);
        if ($client->invoices_count > 0 || $client->quotations_count > 0) {
            return redirect()->route('clients.index')->with('error', 'Cannot delete client with invoices or quotations.');
        }
        $snap = $client->toArray(); $name = $client->name;
        $client->delete();
        ActivityLog::record('deleted', 'client', $id, $name, 'Deleted client '.$name, $snap, null);
        return redirect()->route('clients.index')->with('success', 'Client deleted.');
    }
}
