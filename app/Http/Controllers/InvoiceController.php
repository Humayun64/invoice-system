<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use App\Models\Company;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('client')->withSum('items', 'amount');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('invoice_no', 'like', '%'.$request->search.'%')
                  ->orWhere('project_title', 'like', '%'.$request->search.'%')
                  ->orWhereHas('client', fn($c) => $c->where('name', 'like', '%'.$request->search.'%'));
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest('invoice_date')->paginate(15)->withQueryString();
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $nextNo  = $this->generateInvoiceNo();
        return view('invoices.create', compact('clients', 'nextNo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_no'    => 'required|unique:invoices,invoice_no',
            'client_id'     => 'required|exists:clients,id',
            'project_title' => 'required',
            'invoice_date'  => 'required|date',
        ]);

        $invoice = Invoice::create([
            'invoice_no'      => $request->invoice_no,
            'client_id'       => $request->client_id,
            'project_title'   => $request->project_title,
            'invoice_date'    => $request->invoice_date,
            'show_qty'        => $request->has('show_qty') ? 1 : 0,
            'show_deposit'    => $request->has('show_deposit') ? 1 : 0,
            'deposit_percent' => $request->has('show_deposit') ? $request->deposit_percent : 0,
            'deposit_label'   => $request->deposit_label ?? '1st',
            'show_discount'   => $request->has('show_discount') ? 1 : 0,
            'discount_label'  => $request->discount_label ?: 'Goodwill Discount',
            'discount_type'   => $request->discount_type ?? 'amount',
            'discount_value'  => $request->has('show_discount') ? (float)$request->discount_value : 0,
            'status'          => $request->status ?? 'draft',
        ]);

        $this->saveItems($invoice->id, $request, 'invoice');

        return redirect()->route('invoices.index')->with('success', 'Invoice saved successfully!');
    }

    public function edit($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);
        $clients = Client::orderBy('name')->get();
        return view('invoices.create', compact('invoice', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $request->validate([
            'invoice_no'    => 'required|unique:invoices,invoice_no,'.$id,
            'client_id'     => 'required|exists:clients,id',
            'project_title' => 'required',
            'invoice_date'  => 'required|date',
        ]);

        $invoice->update([
            'invoice_no'      => $request->invoice_no,
            'client_id'       => $request->client_id,
            'project_title'   => $request->project_title,
            'invoice_date'    => $request->invoice_date,
            'show_qty'        => $request->has('show_qty') ? 1 : 0,
            'show_deposit'    => $request->has('show_deposit') ? 1 : 0,
            'deposit_percent' => $request->has('show_deposit') ? $request->deposit_percent : 0,
            'deposit_label'   => $request->deposit_label ?? '1st',
            'show_discount'   => $request->has('show_discount') ? 1 : 0,
            'discount_label'  => $request->discount_label ?: 'Goodwill Discount',
            'discount_type'   => $request->discount_type ?? 'amount',
            'discount_value'  => $request->has('show_discount') ? (float)$request->discount_value : 0,
            'status'          => $request->status ?? 'draft',
        ]);

        $invoice->items()->delete();
        $this->saveItems($invoice->id, $request, 'invoice');

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully!');
    }

    public function destroy($id)
    {
        Invoice::findOrFail($id)->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }

    public function print($id)
    {
        $invoice = Invoice::with(['client', 'items'])->findOrFail($id);
        $company = Company::first();
        $subtotal = $invoice->items->sum('amount');
        $discount = $invoice->discountAmount($subtotal);
        $total    = $subtotal - $discount;
        $deposit  = $invoice->show_deposit ? $total * $invoice->deposit_percent / 100 : $total;
        $badgesLeft  = \App\Models\Badge::where('position','left')->orderBy('sort_order')->get();
        $badgesRight = \App\Models\Badge::where('position','right')->orderBy('sort_order')->get();
        return view('invoices.print', compact('invoice', 'company', 'subtotal', 'discount', 'total', 'deposit', 'badgesLeft', 'badgesRight'));
    }

    private function saveItems($invoiceId, $request, $type)
    {
        $descriptions = $request->description ?? [];
        $amounts      = $request->amount ?? [];
        $qtys         = $request->qty ?? [];
        $units        = $request->unit ?? [];
        $unitPrices   = $request->unit_price ?? [];
        $showQty      = $request->has('show_qty');

        foreach ($descriptions as $k => $desc) {
            $desc = strip_tags(trim($desc), '<b><strong><i><em><u><ul><ol><li><br><p><span>');
            if (trim(strip_tags($desc)) === '') continue;

            $qty   = ($showQty && isset($qtys[$k]) && $qtys[$k] !== '') ? (float)$qtys[$k] : null;
            $unit  = ($showQty && isset($units[$k]) && trim($units[$k]) !== '') ? trim($units[$k]) : null;
            $up    = ($showQty && isset($unitPrices[$k]) && $unitPrices[$k] !== '') ? (float)$unitPrices[$k] : null;
            $amt   = ($showQty && $qty !== null && $up !== null) ? $qty * $up : (float)($amounts[$k] ?? 0);

            InvoiceItem::create([
                'invoice_id'  => $invoiceId,
                'item_order'  => $k + 1,
                'description' => $desc,
                'qty'         => $qty,
                'unit'        => $unit,
                'unit_price'  => $up,
                'amount'      => $amt,
            ]);
        }
    }

    private function generateInvoiceNo()
    {
        $year = date('y');
        $last = Invoice::where('invoice_no', 'like', "SKM-IN-%-{$year}")
            ->selectRaw('MAX(CAST(SUBSTRING_INDEX(invoice_no, \'-\', -2) AS UNSIGNED)) as max_n')
            ->value('max_n') ?? 0;
        return sprintf('SKM-IN-%04d-%s', $last + 1, $year);
    }
}
