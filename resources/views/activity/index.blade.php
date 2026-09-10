@extends('layouts.app')
@section('title','Activity Log')
@section('page_title','📜 Activity Log')

@section('content')
@php
  $actionColor = [
    'created'=>['#15803d','#f0fdf4'],'updated'=>['#d97706','#fffbeb'],'deleted'=>['#b91c1c','#fef2f2'],
    'converted'=>['#7c3aed','#f5f3ff'],'login'=>['#1d4ed8','#eff6ff'],'logout'=>['#6b7280','#f3f4f6'],
    'login_failed'=>['#b91c1c','#fef2f2'],'password_changed'=>['#0e7490','#ecfeff'],
  ];
  $moduleIcon = ['invoice'=>'📄','quotation'=>'📋','client'=>'👥','settings'=>'⚙️','auth'=>'🔐'];
@endphp

<div class="page-header">
  <h1>Activity Log <span style="font-size:0.9rem;color:#64748b;font-weight:400;">({{ $logs->total() }})</span></h1>
</div>

<form method="GET" class="search-bar" style="flex-wrap:wrap;">
  <input type="text" name="search" value="{{ request('search') }}" placeholder="Invoice/Quotation no, description...">
  <input type="text" name="user" value="{{ request('user') }}" placeholder="User" style="max-width:140px;">
  <select name="module">
    <option value="">All Modules</option>
    @foreach(['invoice','quotation','client','settings','auth'] as $m)
      <option value="{{ $m }}" {{ request('module')===$m?'selected':'' }}>{{ ucfirst($m) }}</option>
    @endforeach
  </select>
  <select name="action">
    <option value="">All Actions</option>
    @foreach(array_keys($actionColor) as $a)
      <option value="{{ $a }}" {{ request('action')===$a?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$a)) }}</option>
    @endforeach
  </select>
  <input type="date" name="from" value="{{ request('from') }}" style="max-width:150px;">
  <input type="date" name="to"   value="{{ request('to') }}"   style="max-width:150px;">
  <button type="submit" class="btn btn-primary btn-sm">🔍 Filter</button>
  @if(request()->hasAny(['search','user','module','action','from','to']))
    <a href="{{ route('activity.index') }}" class="btn btn-secondary btn-sm">✕ Clear</a>
  @endif
</form>

<div class="card">
  <table class="data-table">
    <thead><tr><th>Date / Time</th><th>User</th><th>Action</th><th>Module</th><th>Reference</th><th>Description</th><th>IP</th><th></th></tr></thead>
    <tbody>
      @forelse($logs as $log)
        @php $c = $actionColor[$log->action] ?? ['#555','#eee']; @endphp
        <tr>
          <td style="white-space:nowrap;">{{ $log->created_at->format('d/m/Y') }}<br><small style="color:#94a3b8;">{{ $log->created_at->format('H:i:s') }}</small></td>
          <td><strong>{{ $log->user_name }}</strong></td>
          <td><span class="badge" style="color:{{ $c[0] }};background:{{ $c[1] }}">{{ str_replace('_',' ',$log->action) }}</span></td>
          <td>{{ $moduleIcon[$log->module] ?? '' }} {{ ucfirst($log->module) }}</td>
          <td><strong>{{ $log->record_ref }}</strong></td>
          <td style="max-width:280px;">{{ $log->description }}</td>
          <td><small style="color:#94a3b8;">{{ $log->ip_address }}</small></td>
          <td>
            @if($log->old_data || $log->new_data)
              <a href="{{ route('activity.show',$log->id) }}" class="btn btn-sm btn-view">Details</a>
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="8" style="text-align:center;padding:40px;color:#94a3b8;">No activity yet.</td></tr>
      @endforelse
    </tbody>
  </table>
  <div class="pagination">{{ $logs->links() }}</div>
</div>
@endsection
