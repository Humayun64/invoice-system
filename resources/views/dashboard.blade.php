@extends('layouts.app')
@section('title','Dashboard')
@section('page_title','🏠 Dashboard')

@section('content')

<!-- Quick Actions -->
<div class="quick-actions" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:28px;">
  <a href="{{ route('invoices.create') }}" style="background:linear-gradient(135deg,#7b1313,#9b1a1a);color:#fff;border-radius:12px;padding:28px;text-decoration:none;display:flex;align-items:center;gap:16px;box-shadow:0 4px 14px rgba(123,19,19,0.3);">
    <div style="font-size:2.5rem;">📄</div>
    <div>
      <div style="font-size:1.1rem;font-weight:800;">Create Invoice</div>
      <div style="opacity:0.8;font-size:0.85rem;margin-top:3px;">Bill your clients professionally</div>
    </div>
  </a>
  <a href="{{ route('quotations.create') }}" style="background:linear-gradient(135deg,#1e3a5f,#1d4ed8);color:#fff;border-radius:12px;padding:28px;text-decoration:none;display:flex;align-items:center;gap:16px;box-shadow:0 4px 14px rgba(29,78,216,0.3);">
    <div style="font-size:2.5rem;">📋</div>
    <div>
      <div style="font-size:1.1rem;font-weight:800;">Create Quotation</div>
      <div style="opacity:0.8;font-size:0.85rem;margin-top:3px;">Send quotes to potential clients</div>
    </div>
  </a>
</div>

<!-- Stats -->
<div class="stat-cards">
  <div class="stat-card" style="border-color:#7b1313;">
    <div class="stat-val" style="color:#7b1313;">{{ $invCount }}</div>
    <div class="stat-lbl">Total Invoices</div>
  </div>
  <div class="stat-card" style="border-color:#1d4ed8;">
    <div class="stat-val" style="color:#1d4ed8;">{{ $qtCount }}</div>
    <div class="stat-lbl">Total Quotations</div>
  </div>
  <div class="stat-card" style="border-color:#15803d;">
    <div class="stat-val" style="color:#15803d;">${{ number_format($invTotal, 0) }}</div>
    <div class="stat-lbl">Total Invoiced (SGD)</div>
  </div>
  <div class="stat-card" style="border-color:#d97706;">
    <div class="stat-val" style="color:#d97706;">{{ $cliCount }}</div>
    <div class="stat-lbl">Total Clients</div>
  </div>
</div>

<!-- Recent Invoices -->
<div class="card">
  <div class="card-header">
    <h2>📄 Recent Invoices</h2>
    <a href="{{ route('invoices.index') }}" class="btn btn-secondary btn-sm">View All</a>
  </div>
  <div class="table-wrap"><table class="data-table">
    <thead><tr><th>Invoice No.</th><th>Client</th><th>Date</th><th>Total (SGD)</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      @forelse($recentInvoices as $inv)
        @php $colors = ['draft'=>['#6b7280','#f3f4f6'],'sent'=>['#1d4ed8','#eff6ff'],'paid'=>['#15803d','#f0fdf4'],'cancelled'=>['#b91c1c','#fef2f2']]; $bc=$colors[$inv->status]??['#555','#eee']; @endphp
        <tr>
          <td><strong>{{ $inv->invoice_no }}</strong></td>
          <td>{{ $inv->client->name }}</td>
          <td>{{ $inv->invoice_date->format('d/m/Y') }}</td>
          <td style="font-weight:700;color:#7b1313;">${{ number_format($inv->items_sum_amount,2) }}</td>
          <td><span class="badge" style="color:{{ $bc[0] }};background:{{ $bc[1] }}">{{ ucfirst($inv->status) }}</span></td>
          <td class="actions">
            <a href="{{ route('invoices.print',$inv->id) }}" class="btn btn-sm btn-print" target="_blank">Print</a>
            <a href="{{ route('invoices.edit',$inv->id) }}" class="btn btn-sm btn-edit">Edit</a>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" style="text-align:center;padding:32px;color:#94a3b8;">No invoices yet.</td></tr>
      @endforelse
    </tbody>
  </table></div>
</div>

<!-- Recent Quotations -->
<div class="card">
  <div class="card-header">
    <h2>📋 Recent Quotations</h2>
    <a href="{{ route('quotations.index') }}" class="btn btn-secondary btn-sm">View All</a>
  </div>
  <div class="table-wrap"><table class="data-table">
    <thead><tr><th>Quotation No.</th><th>Client</th><th>Date</th><th>Total (SGD)</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      @forelse($recentQuotations as $qt)
        @php $colors = ['draft'=>['#6b7280','#f3f4f6'],'sent'=>['#1d4ed8','#eff6ff'],'accepted'=>['#15803d','#f0fdf4'],'rejected'=>['#b91c1c','#fef2f2']]; $bc=$colors[$qt->status]??['#555','#eee']; @endphp
        <tr>
          <td><strong>{{ $qt->quotation_no }}</strong></td>
          <td>{{ $qt->client->name }}</td>
          <td>{{ $qt->quotation_date->format('d/m/Y') }}</td>
          <td style="font-weight:700;color:#1d4ed8;">${{ number_format($qt->items_sum_amount,2) }}</td>
          <td><span class="badge" style="color:{{ $bc[0] }};background:{{ $bc[1] }}">{{ ucfirst($qt->status) }}</span></td>
          <td class="actions">
            <a href="{{ route('quotations.print',$qt->id) }}" class="btn btn-sm btn-print" target="_blank">Print</a>
            <a href="{{ route('quotations.edit',$qt->id) }}" class="btn btn-sm btn-edit">Edit</a>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" style="text-align:center;padding:32px;color:#94a3b8;">No quotations yet.</td></tr>
      @endforelse
    </tbody>
  </table></div>
</div>
@endsection
