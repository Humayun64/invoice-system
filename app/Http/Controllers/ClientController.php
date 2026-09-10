<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

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
        Client::create($request->only(['name','designation','company','address','postal_code','phone','email']));
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
        Client::findOrFail($id)->update($request->only(['name','designation','company','address','postal_code','phone','email']));
        return redirect()->route('clients.index')->with('success', 'Client updated.');
    }

    public function destroy($id)
    {
        $client = Client::withCount(['invoices','quotations'])->findOrFail($id);
        if ($client->invoices_count > 0 || $client->quotations_count > 0) {
            return redirect()->route('clients.index')->with('error', 'Cannot delete client with invoices or quotations.');
        }
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client deleted.');
    }
}
