@extends('layouts.app')
@section('title','Quotations')
@section('page_title','📋 Quotations')

@section('content')
<div class="page-header">
  <h1>All Quotations <span style="font-size:0.9rem;color:#64748b;font-weight:400;">({{ $quotations->total() }})</span></h1>
  <a href="{{ route('quotations.create') }}" class="btn btn-primary">+ New Quotation</a>
</div>

<form method="GET" class="search-bar">
  <input type="text" name="search" value="{{ request('search') }}" placeholder="Search quotation no, client, project...">
  <select name="status">
    <option value="">All Status</option>
    @foreach(['draft','sent','accepted','rejected'] as $s)
      <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
    @endforeach
  </select>
  <button type="submit" class="btn btn-primary btn-sm">🔍 Search</button>
  @if(request('search')||request('status'))
    <a href="{{ route('quotations.index') }}" class="btn btn-secondary btn-sm">✕ Clear</a>
  @endif
</form>

<div class="card">
  <div class="table-wrap"><table class="data-table">
    <thead>
      <tr><th>#</th><th>Quotation No.</th><th>Client</th><th>Project</th><th>Date</th><th>Total (SGD)</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
      @forelse($quotations as $i => $qt)
        @php $colors = ['draft'=>['#6b7280','#f3f4f6'],'sent'=>['#1d4ed8','#eff6ff'],'accepted'=>['#15803d','#f0fdf4'],'rejected'=>['#b91c1c','#fef2f2']]; $bc=$colors[$qt->status]??['#555','#eee']; @endphp
        <tr>
          <td style="color:#94a3b8;">{{ $quotations->firstItem()+$i }}</td>
          <td><strong>{{ $qt->quotation_no }}</strong></td>
          <td>{{ $qt->client->name }}<br><small style="color:#94a3b8;">{{ $qt->client->company }}</small></td>
          <td style="max-width:180px;">{{ Str::limit($qt->project_title,40) }}</td>
          <td>{{ $qt->quotation_date->format('d/m/Y') }}</td>
          <td style="font-weight:700;color:#1d4ed8;">${{ number_format($qt->items_sum_amount,2) }}</td>
          <td><span class="badge" style="color:{{ $bc[0] }};background:{{ $bc[1] }}">{{ ucfirst($qt->status) }}</span></td>
          <td>
            <div class="actions">
              <a href="{{ route('quotations.print',$qt->id) }}" class="btn btn-sm btn-view" target="_blank">View</a>
              <a href="{{ route('quotations.print',$qt->id) }}" class="btn btn-sm btn-print" target="_blank">Print</a>
              <a href="{{ route('quotations.edit',$qt->id) }}" class="btn btn-sm btn-edit">Edit</a>
              <form method="POST" action="{{ route('quotations.convert',$qt->id) }}" style="display:inline" onsubmit="return confirm('Convert this quotation to a new Invoice?')">
                @csrf
                <button type="submit" class="btn btn-sm btn-convert">→ Invoice</button>
              </form>
              <form method="POST" action="{{ route('quotations.destroy',$qt->id) }}" style="display:inline" onsubmit="return confirm('Delete this quotation?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-del">Del</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="8" style="text-align:center;padding:40px;color:#94a3b8;">No quotations yet. <a href="{{ route('quotations.create') }}" style="color:#7b1313;">Create first quotation →</a></td></tr>
      @endforelse
    </tbody>
  </table></div>
  <div class="pagination">{{ $quotations->links() }}</div>
</div>
@endsection
