@extends('layouts.app')
@section('title','Invoices')
@section('page_title','📄 Invoices')

@section('content')
<div class="page-header">
  <h1>All Invoices <span style="font-size:0.9rem;color:#64748b;font-weight:400;">({{ $invoices->total() }})</span></h1>
  <a href="{{ route('invoices.create') }}" class="btn btn-primary">+ New Invoice</a>
</div>

<!-- Search -->
<form method="GET" class="search-bar">
  <input type="text" name="search" value="{{ request('search') }}" placeholder="Search invoice no, client, project...">
  <select name="status">
    <option value="">All Status</option>
    @foreach(['draft','sent','paid','cancelled'] as $s)
      <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
    @endforeach
  </select>
  <button type="submit" class="btn btn-primary btn-sm">🔍 Search</button>
  @if(request('search')||request('status'))
    <a href="{{ route('invoices.index') }}" class="btn btn-secondary btn-sm">✕ Clear</a>
  @endif
</form>

<div class="card">
  <table class="data-table">
    <thead>
      <tr><th>#</th><th>Invoice No.</th><th>Client</th><th>Project</th><th>Date</th><th>Total (SGD)</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
      @forelse($invoices as $i => $inv)
        @php $colors = ['draft'=>['#6b7280','#f3f4f6'],'sent'=>['#1d4ed8','#eff6ff'],'paid'=>['#15803d','#f0fdf4'],'cancelled'=>['#b91c1c','#fef2f2']]; $bc=$colors[$inv->status]??['#555','#eee']; @endphp
        <tr>
          <td style="color:#94a3b8;">{{ $invoices->firstItem()+$i }}</td>
          <td><strong>{{ $inv->invoice_no }}</strong></td>
          <td>{{ $inv->client->name }}<br><small style="color:#94a3b8;">{{ $inv->client->company }}</small></td>
          <td style="max-width:180px;">{{ Str::limit($inv->project_title,40) }}</td>
          <td>{{ $inv->invoice_date->format('d/m/Y') }}</td>
          <td style="font-weight:700;color:#7b1313;">${{ number_format($inv->items_sum_amount,2) }}</td>
          <td><span class="badge" style="color:{{ $bc[0] }};background:{{ $bc[1] }}">{{ ucfirst($inv->status) }}</span></td>
          <td>
            <div class="actions">
              <a href="{{ route('invoices.print',$inv->id) }}" class="btn btn-sm btn-view" target="_blank">View</a>
              <a href="{{ route('invoices.print',$inv->id) }}" class="btn btn-sm btn-print" target="_blank">Print</a>
              <a href="{{ route('invoices.edit',$inv->id) }}" class="btn btn-sm btn-edit">Edit</a>
              <form method="POST" action="{{ route('invoices.destroy',$inv->id) }}" style="display:inline" onsubmit="return confirm('Delete this invoice?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-del">Del</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="8" style="text-align:center;padding:40px;color:#94a3b8;">No invoices yet. <a href="{{ route('invoices.create') }}" style="color:#7b1313;">Create first invoice →</a></td></tr>
      @endforelse
    </tbody>
  </table>
  <div class="pagination">{{ $invoices->links() }}</div>
</div>
@endsection
