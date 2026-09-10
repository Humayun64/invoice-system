<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use App\Models\Company;
use App\Models\ActivityLog;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = Quotation::with('client')->withSum('items', 'amount');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('quotation_no', 'like', '%'.$request->search.'%')
                  ->orWhere('project_title', 'like', '%'.$request->search.'%')
                  ->orWhereHas('client', fn($c) => $c->where('name', 'like', '%'.$request->search.'%'));
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $quotations = $query->latest('quotation_date')->paginate(15)->withQueryString();
        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $clients  = Client::orderBy('name')->get();
        $company  = Company::first();
        $nextNo   = $this->generateQuotationNo();
        return view('quotations.create', compact('clients', 'company', 'nextNo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'quotation_no'  => 'required|unique:quotations,quotation_no',
            'client_id'     => 'required|exists:clients,id',
            'project_title' => 'required',
            'quotation_date'=> 'required|date',
        ]);

        $quotation = Quotation::create([
            'quotation_no'   => $request->quotation_no,
            'client_id'      => $request->client_id,
            'project_title'  => $request->project_title,
            'quotation_date' => $request->quotation_date,
            'valid_days'     => $request->valid_days ?? 15,
            'intro_text'     => $request->intro_text,
            'show_qty'         => $request->has('show_qty') ? 1 : 0,
            'show_discount'    => $request->has('show_discount') ? 1 : 0,
            'discount_label'   => $request->discount_label ?: 'Goodwill Discount',
            'discount_type'    => $request->discount_type ?? 'amount',
            'discount_value'   => $request->has('show_discount') ? (float)$request->discount_value : 0,
            'payment_terms'    => $request->payment_terms,
            'important_clauses'=> $request->important_clauses,
            'price_basis'      => $request->price_basis,
            'conclusion'       => $request->conclusion,
            'status'           => $request->status ?? 'draft',
        ]);

        $this->saveItems($quotation->id, $request);
        ActivityLog::record('created', 'quotation', $quotation->id, $quotation->quotation_no, 'Created quotation for '.$quotation->client->name, null, $quotation->load('items')->toArray());

        return redirect()->route('quotations.index')->with('success', 'Quotation saved successfully!');
    }

    public function edit($id)
    {
        $quotation = Quotation::with('items')->findOrFail($id);
        $clients   = Client::orderBy('name')->get();
        $company   = Company::first();
        return view('quotations.create', compact('quotation', 'clients', 'company'));
    }

    public function update(Request $request, $id)
    {
        $quotation = Quotation::findOrFail($id);
        $before    = $quotation->load('items')->toArray();

        $request->validate([
            'quotation_no'  => 'required|unique:quotations,quotation_no,'.$id,
            'client_id'     => 'required|exists:clients,id',
            'project_title' => 'required',
            'quotation_date'=> 'required|date',
        ]);

        $quotation->update([
            'quotation_no'   => $request->quotation_no,
            'client_id'      => $request->client_id,
            'project_title'  => $request->project_title,
            'quotation_date' => $request->quotation_date,
            'valid_days'     => $request->valid_days ?? 15,
            'intro_text'     => $request->intro_text,
            'show_qty'         => $request->has('show_qty') ? 1 : 0,
            'show_discount'    => $request->has('show_discount') ? 1 : 0,
            'discount_label'   => $request->discount_label ?: 'Goodwill Discount',
            'discount_type'    => $request->discount_type ?? 'amount',
            'discount_value'   => $request->has('show_discount') ? (float)$request->discount_value : 0,
            'payment_terms'    => $request->payment_terms,
            'important_clauses'=> $request->important_clauses,
            'price_basis'      => $request->price_basis,
            'conclusion'       => $request->conclusion,
            'status'           => $request->status ?? 'draft',
        ]);

        $quotation->items()->delete();
        $this->saveItems($quotation->id, $request);
        ActivityLog::record('updated', 'quotation', $quotation->id, $quotation->quotation_no, 'Updated quotation', $before, $quotation->fresh()->load('items')->toArray());

        return redirect()->route('quotations.index')->with('success', 'Quotation updated successfully!');
    }

    public function destroy($id)
    {
        $qt     = Quotation::with(['items','client'])->findOrFail($id);
        $snap   = $qt->toArray();
        $ref    = $qt->quotation_no;
        $client = $qt->client->name ?? '';
        $qt->delete();
        ActivityLog::record('deleted', 'quotation', $id, $ref, 'Deleted quotation '.$ref.' ('.$client.')', $snap, null);
        return redirect()->route('quotations.index')->with('success', 'Quotation deleted.');
    }

    public function print($id)
    {
        $quotation = Quotation::with(['client', 'items'])->findOrFail($id);
        $company   = Company::first();
        $subtotal  = $quotation->items->sum('amount');
        $discount  = $quotation->discountAmount($subtotal);
        $payable   = $subtotal - $discount;
        $badgesLeft  = \App\Models\Badge::where('position','left')->orderBy('sort_order')->get();
        $badgesRight = \App\Models\Badge::where('position','right')->orderBy('sort_order')->get();
        return view('quotations.print', compact('quotation', 'company', 'subtotal', 'discount', 'payable', 'badgesLeft', 'badgesRight'));
    }

    public function convertToInvoice($id)
    {
        $quotation = Quotation::with('items')->findOrFail($id);

        // Generate new invoice number
        $year   = date('y');
        $last   = Invoice::where('invoice_no', 'like', "SKM-IN-%-{$year}")
            ->selectRaw('MAX(CAST(SUBSTRING_INDEX(invoice_no, \'-\', -2) AS UNSIGNED)) as max_n')
            ->value('max_n') ?? 0;
        $invNo  = sprintf('SKM-IN-%04d-%s', $last + 1, $year);

        $invoice = Invoice::create([
            'invoice_no'      => $invNo,
            'client_id'       => $quotation->client_id,
            'project_title'   => $quotation->project_title,
            'invoice_date'    => now()->toDateString(),
            'show_qty'        => $quotation->show_qty,
            'show_deposit'    => 1,
            'deposit_percent' => 50,
            'deposit_label'   => '1st',
            'show_discount'   => $quotation->show_discount,
            'discount_label'  => $quotation->discount_label,
            'discount_type'   => $quotation->discount_type,
            'discount_value'  => $quotation->discount_value,
            'status'          => 'draft',
        ]);

        foreach ($quotation->items as $k => $item) {
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'item_order'  => $item->item_order,
                'description' => $item->description,
                'qty'         => $item->qty,
                'unit'        => $item->unit,
                'unit_price'  => $item->unit_price,
                'amount'      => $item->amount,
            ]);
        }

        $quotation->update(['status' => 'accepted']);
        ActivityLog::record('converted', 'quotation', $quotation->id, $quotation->quotation_no, 'Converted quotation '.$quotation->quotation_no.' to invoice '.$invNo, null, ['invoice_id'=>$invoice->id,'invoice_no'=>$invNo]);

        return redirect()->route('invoices.edit', $invoice->id)
            ->with('success', 'Quotation converted to Invoice '.$invNo.'!');
    }

    private function saveItems($quotationId, $request)
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

            $qty  = ($showQty && isset($qtys[$k]) && $qtys[$k] !== '') ? (float)$qtys[$k] : null;
            $unit = ($showQty && isset($units[$k]) && trim($units[$k]) !== '') ? trim($units[$k]) : null;
            $up   = ($showQty && isset($unitPrices[$k]) && $unitPrices[$k] !== '') ? (float)$unitPrices[$k] : null;
            $amt  = ($showQty && $qty !== null && $up !== null) ? $qty * $up : (float)($amounts[$k] ?? 0);

            QuotationItem::create([
                'quotation_id' => $quotationId,
                'item_order'   => $k + 1,
                'description'  => $desc,
                'qty'          => $qty,
                'unit'         => $unit,
                'unit_price'   => $up,
                'amount'       => $amt,
            ]);
        }
    }

    private function generateQuotationNo()
    {
        $year = date('y');
        $last = Quotation::where('quotation_no', 'like', "SKM-QT-%-{$year}")
            ->selectRaw('MAX(CAST(SUBSTRING_INDEX(quotation_no, \'-\', -2) AS UNSIGNED)) as max_n')
            ->value('max_n') ?? 0;
        return sprintf('SKM-QT-%04d-%s', $last + 1, $year);
    }
}
