@extends('layouts.app')
@section('title','Clients')
@section('page_title','👥 Clients')

@section('content')
<div class="two-col" style="display:grid;grid-template-columns:320px 1fr;gap:24px;align-items:start;">

  <!-- Form -->
  <div class="form-card">
    <h3>{{ isset($client) ? '✏️ Edit Client' : '➕ Add Client' }}</h3>
    <form method="POST" action="{{ isset($client) ? route('clients.update',$client->id) : route('clients.store') }}">
      @csrf
      @if(isset($client)) @method('PUT') @endif
      <div class="form-group" style="margin-bottom:12px;"><label>Full Name *</label><input type="text" name="name" value="{{ old('name',$client->name??'') }}" required></div>
      <div class="form-group" style="margin-bottom:12px;"><label>Designation</label><input type="text" name="designation" value="{{ old('designation',$client->designation??'') }}" placeholder="HR Manager"></div>
      <div class="form-group" style="margin-bottom:12px;"><label>Company</label><input type="text" name="company" value="{{ old('company',$client->company??'') }}"></div>
      <div class="form-group" style="margin-bottom:12px;"><label>Address</label><textarea name="address" rows="2">{{ old('address',$client->address??'') }}</textarea></div>
      <div class="form-group" style="margin-bottom:12px;"><label>Postal Code</label><input type="text" name="postal_code" value="{{ old('postal_code',$client->postal_code??'') }}"></div>
      <div class="form-group" style="margin-bottom:12px;"><label>Phone</label><input type="text" name="phone" value="{{ old('phone',$client->phone??'') }}"></div>
      <div class="form-group" style="margin-bottom:16px;"><label>Email</label><input type="email" name="email" value="{{ old('email',$client->email??'') }}"></div>
      <button type="submit" class="btn btn-primary" style="width:100%;">{{ isset($client) ? '💾 Update' : '➕ Add Client' }}</button>
      @if(isset($client))
        <a href="{{ route('clients.index') }}" style="display:block;text-align:center;margin-top:10px;color:#7b1313;font-size:0.85rem;">Cancel</a>
      @endif
    </form>
  </div>

  <!-- List -->
  <div class="card">
    <div class="card-header"><h2>All Clients ({{ $clients->count() }})</h2></div>
    <div class="table-wrap"><table class="data-table">
      <thead><tr><th>Name</th><th>Company</th><th>Phone</th><th>Invoices</th><th>Quotations</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($clients as $c)
        <tr>
          <td><strong>{{ $c->name }}</strong>@if($c->designation)<br><small style="color:#94a3b8;">{{ $c->designation }}</small>@endif</td>
          <td>{{ $c->company ?: '—' }}</td>
          <td>{{ $c->phone ?: '—' }}</td>
          <td style="text-align:center;">{{ $c->invoices_count }}</td>
          <td style="text-align:center;">{{ $c->quotations_count }}</td>
          <td>
            <div class="actions">
              <a href="{{ route('clients.edit',$c->id) }}" class="btn btn-sm btn-edit">Edit</a>
              @if($c->invoices_count == 0 && $c->quotations_count == 0)
                <form method="POST" action="{{ route('clients.destroy',$c->id) }}" style="display:inline" onsubmit="return confirm('Delete client?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-del">Del</button>
                </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
          <tr><td colspan="6" style="text-align:center;padding:32px;color:#94a3b8;">No clients yet.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
</div>
@endsection
