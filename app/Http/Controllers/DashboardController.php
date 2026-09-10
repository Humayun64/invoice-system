<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\Client;
use App\Models\InvoiceItem;

class DashboardController extends Controller
{
    public function index()
    {
        $invCount  = Invoice::count();
        $qtCount   = Quotation::count();
        $cliCount  = Client::count();
        $invTotal  = InvoiceItem::sum('amount');

        $recentInvoices   = Invoice::with('client')
            ->withSum('items', 'amount')
            ->latest()
            ->take(5)
            ->get();

        $recentQuotations = Quotation::with('client')
            ->withSum('items', 'amount')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'invCount','qtCount','cliCount','invTotal',
            'recentInvoices','recentQuotations'
        ));
    }
}
