@extends('layouts.app')
@section('title','Activity Detail')
@section('page_title','📜 Activity Detail')

@push('styles')
<style>
.json-box { background:#0f172a; color:#e2e8f0; border-radius:8px; padding:14px; font-family:Consolas,monospace; font-size:0.78rem; line-height:1.5; overflow:auto; max-height:600px; white-space:pre; }
.kv-table { width:100%; border-collapse:collapse; font-size:0.85rem; }
.kv-table th { text-align:left; padding:6px 10px; background:#f8fafc; width:180px; color:#475569; font-weight:600; border-bottom:1px solid #e2e8f0; }
.kv-table td { padding:6px 10px; border-bottom:1px solid #f1f5f9; }
.diff-old { background:#fef2f2; }
.diff-new { background:#f0fdf4; }
.item-row { background:#fff; border:1px solid #e2e8f0; border-radius:6px; padding:8px 10px; margin-bottom:6px; font-size:0.82rem; }
</style>
@endpush

@section('content')
@php
  $skip = ['created_at','updated_at','id','client_id','items','client'];
  $old = $log->old_data ?? []; $new = $log->new_data ?? [];
  $keys = array_unique(array_merge(array_keys($old), array_keys($new)));
  $fmt = fn($v) => is_array($v) ? json_encode($v) : (string)$v;
@endphp

<a href="{{ route('activity.index') }}" class="btn btn-secondary btn-sm" style="margin-bottom:16px;">← Back to log</a>

<div class="form-card">
  <h3>Summary</h3>
  <table class="kv-table">
    <tr><th>Date / Time</th><td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td></tr>
    <tr><th>User</th><td><strong>{{ $log->user_name }}</strong> (IP {{ $log->ip_address }})</td></tr>
    <tr><th>Action</th><td><span class="badge" style="background:#f1f5f9;">{{ str_replace('_',' ',$log->action) }}</span></td></tr>
    <tr><th>Module</th><td>{{ ucfirst($log->module) }}</td></tr>
    <tr><th>Reference</th><td><strong>{{ $log->record_ref }}</strong></td></tr>
    <tr><th>Description</th><td>{{ $log->description }}</td></tr>
  </table>
</div>

@if($log->action === 'updated' && $old && $new)
  <div class="form-card">
    <h3>What changed</h3>
    @php $changed = 0; @endphp
    <table class="kv-table">
      <tr><th>Field</th><th class="diff-old">Before</th><th class="diff-new">After</th></tr>
      @foreach($keys as $k)
        @if(in_array($k,$skip)) @continue @endif
        @if($fmt($old[$k]??'') !== $fmt($new[$k]??''))
          @php $changed++; @endphp
          <tr><th>{{ $k }}</th><td class="diff-old">{{ $fmt($old[$k]??'') }}</td><td class="diff-new">{{ $fmt($new[$k]??'') }}</td></tr>
        @endif
      @endforeach
      @if(isset($old['items']) && json_encode($old['items']) !== json_encode($new['items']??[]))
        @php $changed++; @endphp
        <tr><th>Line items</th>
          <td class="diff-old">@foreach($old['items'] as $it)<div class="item-row">{!! strip_tags($it['description'],'<b><i><u>') !!} — <strong>${{ number_format($it['amount'],2) }}</strong></div>@endforeach</td>
          <td class="diff-new">@foreach($new['items']??[] as $it)<div class="item-row">{!! strip_tags($it['description'],'<b><i><u>') !!} — <strong>${{ number_format($it['amount'],2) }}</strong></div>@endforeach</td>
        </tr>
      @endif
    </table>
    @if(!$changed)<p style="color:#94a3b8;font-size:0.85rem;padding:8px;">No field changes detected (re-saved without edits).</p>@endif
  </div>
@endif

@if($log->action === 'deleted' && $old)
  <div class="form-card" style="border-left:4px solid #b91c1c;">
    <h3 style="color:#b91c1c;">Deleted record (snapshot)</h3>
    <table class="kv-table">
      @foreach($old as $k => $v)
        @if(in_array($k,['items','client','created_at','updated_at'])) @continue @endif
        <tr><th>{{ $k }}</th><td>{{ $fmt($v) }}</td></tr>
      @endforeach
      @if(isset($old['client']['name']))<tr><th>Client</th><td>{{ $old['client']['name'] }} {{ $old['client']['company']??'' }}</td></tr>@endif
    </table>
    @if(!empty($old['items']))
      <div style="margin-top:14px;font-weight:600;font-size:0.85rem;">Line items ({{ count($old['items']) }})</div>
      @foreach($old['items'] as $i => $it)
        <div class="item-row">{{ $i+1 }}. {!! strip_tags($it['description'],'<b><i><u>') !!} — <strong>${{ number_format($it['amount'],2) }}</strong></div>
      @endforeach
      <div style="text-align:right;font-weight:700;margin-top:6px;">Total: ${{ number_format(array_sum(array_column($old['items'],'amount')),2) }}</div>
    @endif
  </div>
@endif

<div class="form-card">
  <h3>Raw data</h3>
  <div class="two-col" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div><div style="font-size:0.8rem;font-weight:600;margin-bottom:6px;color:#b91c1c;">Before</div><div class="json-box">{{ $old ? json_encode($old, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) : '—' }}</div></div>
    <div><div style="font-size:0.8rem;font-weight:600;margin-bottom:6px;color:#15803d;">After</div><div class="json-box">{{ $new ? json_encode($new, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) : '—' }}</div></div>
  </div>
</div>
@endsection
