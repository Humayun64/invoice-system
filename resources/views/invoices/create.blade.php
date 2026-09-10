@extends('layouts.app')
@section('title', isset($invoice) ? 'Edit Invoice' : 'Create Invoice')
@section('page_title', isset($invoice) ? '✏️ Edit Invoice' : '➕ Create Invoice')

@section('content')

@php
  $isEdit     = isset($invoice);
  $curShowQty = $isEdit ? $invoice->show_qty : 0;
  $curShowDep = $isEdit ? $invoice->show_deposit : 1;
  $curDep     = $isEdit ? $invoice->deposit_percent : 50;
  $curDepLbl  = $isEdit ? $invoice->deposit_label : '1st';
  $curShowDisc= $isEdit ? $invoice->show_discount : 0;
  $curDiscLbl = $isEdit ? $invoice->discount_label : 'Goodwill Discount';
  $curDiscTyp = $isEdit ? $invoice->discount_type : 'amount';
  $curDiscVal = $isEdit ? $invoice->discount_value : 0;
  $items      = $isEdit ? $invoice->items : collect([]);
  $action     = $isEdit ? route('invoices.update',$invoice->id) : route('invoices.store');
@endphp

<form method="POST" action="{{ $action }}" onsubmit="return syncRTE()">
  @csrf
  @if($isEdit) @method('PUT') @endif

  <!-- Invoice Details -->
  <div class="form-card">
    <h3>Invoice Details</h3>
    <div class="form-row">
      <div class="form-group">
        <label>Invoice No *</label>
        <input type="text" name="invoice_no" value="{{ old('invoice_no', $isEdit ? $invoice->invoice_no : $nextNo ?? '') }}" required>
      </div>
      <div class="form-group">
        <label>Date *</label>
        <input type="date" name="invoice_date" value="{{ old('invoice_date', $isEdit ? $invoice->invoice_date->format('Y-m-d') : date('Y-m-d')) }}" required>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          @foreach(['draft','sent','paid','cancelled'] as $s)
            <option value="{{ $s }}" {{ old('status', $isEdit ? $invoice->status : 'draft') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>Client *</label>
        <select name="client_id" required>
          <option value="">— Choose Client —</option>
          @foreach($clients as $c)
            <option value="{{ $c->id }}" {{ old('client_id', $isEdit ? $invoice->client_id : '') == $c->id ? 'selected' : '' }}>
              {{ $c->name }}{{ $c->company ? ' — '.$c->company : '' }}
            </option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="form-group">
      <label>Project Title *</label>
      <input type="text" name="project_title" value="{{ old('project_title', $isEdit ? $invoice->project_title : '') }}" required>
    </div>
    <p style="font-size:0.8rem;color:#64748b;margin-top:8px;">Need a new client? <a href="{{ route('clients.index') }}" target="_blank" style="color:#7b1313;">Add here →</a></p>
  </div>

  <!-- Options -->
  <div class="form-card">
    <h3>Options</h3>
    <div class="toggle-row">
      <label class="switch"><input type="checkbox" name="show_qty" id="toggleQty" {{ $curShowQty ? 'checked' : '' }}><span class="slider"></span></label>
      <div><div class="toggle-label">Use QTY / UNIT / UNIT PRICE</div><div class="toggle-sub">Auto-calculates amount from quantity × unit price</div></div>
    </div>
    <div class="toggle-row">
      <label class="switch"><input type="checkbox" name="show_deposit" id="toggleDep" {{ $curShowDep ? 'checked' : '' }}><span class="slider"></span></label>
      <div><div class="toggle-label">Show Invoice Deposit</div><div class="toggle-sub">Show deposit row with percentage and invoice number</div></div>
    </div>
    <div class="toggle-row">
      <label class="switch"><input type="checkbox" name="show_discount" id="toggleDisc" {{ $curShowDisc ? 'checked' : '' }}><span class="slider"></span></label>
      <div><div class="toggle-label">Apply Discount</div><div class="toggle-sub">Shows Sub Total → Discount → Total Project Cost</div></div>
    </div>
    <div id="discSection" style="{{ $curShowDisc ? '' : 'display:none' }};background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin:4px 0 14px;">
      <div class="form-row three">
        <div class="form-group"><label>Discount Label</label><input type="text" name="discount_label" value="{{ $curDiscLbl }}"></div>
        <div class="form-group"><label>Discount Type</label>
          <select name="discount_type" id="discType" onchange="calcTotal()">
            <option value="amount"  {{ $curDiscTyp==='amount'  ? 'selected':'' }}>Fixed Amount (SGD)</option>
            <option value="percent" {{ $curDiscTyp==='percent' ? 'selected':'' }}>Percentage (%)</option>
          </select>
        </div>
        <div class="form-group"><label id="discValLbl">Discount Value</label><input type="number" name="discount_value" id="discVal" value="{{ $curDiscVal }}" min="0" step="0.01" oninput="calcTotal()"></div>
      </div>
    </div>
    <div id="depSection" style="{{ $curShowDep ? '' : 'display:none' }};background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-top:4px;">
      <div class="form-row three">
        <div class="form-group">
          <label>Invoice Number</label>
          <select name="deposit_label" id="depLabel">
            @foreach(['1st','2nd','3rd','4th','5th','Final'] as $l)
              <option value="{{ $l }}" {{ $curDepLbl === $l ? 'selected' : '' }}>{{ $l }} Invoice</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Deposit %</label>
          <input type="number" name="deposit_percent" id="depPct" value="{{ $curDep }}" min="0" max="100" step="0.01">
        </div>
        <div class="form-group">
          <label>Preview</label>
          <div id="depPreview" style="padding:9px 12px;background:#fff;border:1px solid #e2e8f0;border-radius:6px;font-size:0.85rem;color:#1d4ed8;font-weight:600;"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Line Items -->
  <div class="form-card">
    <h3>Scope of Work & Line Items</h3>
    <div id="tableWrap" class="{{ $curShowQty ? 'show-qty' : '' }}">
      <table class="items-table">
        <thead>
          <tr>
            <th style="width:40px;">No.</th>
            <th>Description of Work</th>
            <th class="qty-col" style="width:75px;">QTY</th>
            <th class="qty-col" style="width:85px;">UNIT</th>
            <th class="qty-col" style="width:120px;">UNIT PRICE</th>
            <th style="width:130px;">AMOUNT (SGD)</th>
            <th style="width:46px;"></th>
          </tr>
        </thead>
        <tbody id="itemsBody">
          @if($items->isEmpty())
            <tr>
              <td>1</td>
              <td>
                <div class="rte-wrap"><div class="rte-toolbar">
                  <button type="button" class="rte-btn" title="Bold" onclick="fmt('bold',this)"><b>B</b></button>
                  <button type="button" class="rte-btn" title="Italic" onclick="fmt('italic',this)"><i>I</i></button>
                  <button type="button" class="rte-btn" title="Underline" onclick="fmt('underline',this)"><u>U</u></button>
                  <div class="rte-sep"></div>
                  <button type="button" class="rte-btn" title="Bullet" onclick="fmt('insertUnorderedList',this)">• Bullet</button>
                  <button type="button" class="rte-btn" title="Numbered" onclick="fmt('insertOrderedList',this)">1. Number</button>
                  <div class="rte-sep"></div>
                  <button type="button" class="rte-btn" style="font-size:0.72rem;color:#94a3b8;" onclick="clearFmt(this)">✕ Clear</button>
                </div><div class="rte-editor" contenteditable="true" data-placeholder="Describe work done here..."></div></div>
                <textarea class="rte-hidden" name="description[]"></textarea>
              </td>
              <td class="qty-col"><input type="number" name="qty[]" step="0.01" min="0" placeholder="0" class="num-field qty-f" oninput="calcRow(this)"></td>
              <td class="qty-col"><input type="text" name="unit[]" placeholder="m², no..."></td>
              <td class="qty-col"><input type="number" name="unit_price[]" step="0.01" min="0" placeholder="0.00" class="num-field up-f" oninput="calcRow(this)"></td>
              <td><input type="number" name="amount[]" step="0.01" min="0" placeholder="0.00" class="num-field amt-f" oninput="calcTotal()"></td>
              <td><button type="button" class="remove-row" onclick="removeRow(this)">✕</button></td>
            </tr>
          @else
            @foreach($items as $k => $item)
            <tr>
              <td>{{ $k+1 }}</td>
              <td>
                <div class="rte-wrap"><div class="rte-toolbar">
                  <button type="button" class="rte-btn" title="Bold" onclick="fmt('bold',this)"><b>B</b></button>
                  <button type="button" class="rte-btn" title="Italic" onclick="fmt('italic',this)"><i>I</i></button>
                  <button type="button" class="rte-btn" title="Underline" onclick="fmt('underline',this)"><u>U</u></button>
                  <div class="rte-sep"></div>
                  <button type="button" class="rte-btn" title="Bullet" onclick="fmt('insertUnorderedList',this)">• Bullet</button>
                  <button type="button" class="rte-btn" title="Numbered" onclick="fmt('insertOrderedList',this)">1. Number</button>
                  <div class="rte-sep"></div>
                  <button type="button" class="rte-btn" style="font-size:0.72rem;color:#94a3b8;" onclick="clearFmt(this)">✕ Clear</button>
                </div><div class="rte-editor" contenteditable="true" data-placeholder="Describe work done here...">{!! $item->description !!}</div></div>
                <textarea class="rte-hidden" name="description[]">{{ $item->description }}</textarea>
              </td>
              <td class="qty-col"><input type="number" name="qty[]" value="{{ $item->qty }}" step="0.01" min="0" placeholder="0" class="num-field qty-f" oninput="calcRow(this)"></td>
              <td class="qty-col"><input type="text" name="unit[]" value="{{ $item->unit }}" placeholder="m², no..."></td>
              <td class="qty-col"><input type="number" name="unit_price[]" value="{{ $item->unit_price }}" step="0.01" min="0" placeholder="0.00" class="num-field up-f" oninput="calcRow(this)"></td>
              <td><input type="number" name="amount[]" value="{{ $item->amount }}" step="0.01" min="0" placeholder="0.00" class="num-field amt-f" oninput="calcTotal()"></td>
              <td><button type="button" class="remove-row" onclick="removeRow(this)">✕</button></td>
            </tr>
            @endforeach
          @endif
        </tbody>
      </table>
    </div>
    <button type="button" class="btn-add-row" onclick="addRow()">+ Add Line Item</button>

    <div class="total-box">
      <div class="total-row"><span>Sub Total:</span><span>SGD <strong id="subAmt">0.00</strong></span></div>
      <div class="total-row dep-row" id="discRow" style="{{ $curShowDisc ? '' : 'display:none' }}"><span id="discRowLbl">Discount:</span><span>- SGD <strong id="discDisp">0.00</strong></span></div>
      <div class="total-row" style="font-weight:700;"><span>Total Project Cost:</span><span>SGD <strong id="totalAmt">0.00</strong></span></div>
      <div class="total-row dep-row" id="depRowDisp" style="{{ $curShowDep ? '' : 'display:none' }}">
        <span id="depRowLbl">{{ $curDepLbl }} Invoice Deposit (<span id="depPctLbl">{{ $curDep }}</span>%):</span>
        <span>SGD <strong id="depAmt">0.00</strong></span>
      </div>
      <div class="total-row grand"><span>TOTAL NET TO PAY:</span><span>SGD <strong id="netAmt">0.00</strong></span></div>
    </div>
  </div>

  <div style="display:flex;gap:12px;justify-content:flex-end;margin-bottom:40px;">
    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary">💾 Save Invoice</button>
  </div>
</form>

@push('scripts')
<script>
document.getElementById('toggleQty').addEventListener('change', function() {
  document.getElementById('tableWrap').classList.toggle('show-qty', this.checked); calcTotal();
});
document.getElementById('toggleDep').addEventListener('change', function() {
  document.getElementById('depSection').style.display = this.checked ? '' : 'none';
  document.getElementById('depRowDisp').style.display = this.checked ? '' : 'none';
  calcTotal();
});
function updateDepPreview() {
  const l = document.getElementById('depLabel').value;
  const p = document.getElementById('depPct').value || '0';
  document.getElementById('depPreview').textContent = l + ' Invoice Deposit — ' + p + '%';
  document.getElementById('depRowLbl').innerHTML = l + ' Invoice Deposit (<span id="depPctLbl">' + p + '</span>%):';
  calcTotal();
}
document.getElementById('toggleDisc').addEventListener('change', function() {
  document.getElementById('discSection').style.display = this.checked ? '' : 'none';
  document.getElementById('discRow').style.display = this.checked ? '' : 'none';
  calcTotal();
});
function calcTotal() {
  let sub = 0; document.querySelectorAll('.amt-f').forEach(f => sub += parseFloat(f.value)||0);
  const showDisc = document.getElementById('toggleDisc').checked;
  const dtype = document.getElementById('discType').value;
  const dval  = parseFloat(document.getElementById('discVal').value)||0;
  const disc  = !showDisc ? 0 : (dtype==='percent' ? sub*dval/100 : dval);
  const total = sub - disc;
  document.getElementById('discValLbl').textContent = dtype==='percent' ? 'Discount (%)' : 'Discount Amount (SGD)';
  document.getElementById('discRowLbl').textContent = dtype==='percent' ? 'Discount ('+dval+'%):' : 'Discount:';
  const showDep = document.getElementById('toggleDep').checked;
  const dep = showDep ? (parseFloat(document.getElementById('depPct').value)||0) : 0;
  document.getElementById('subAmt').textContent   = sub.toFixed(2);
  document.getElementById('discDisp').textContent = disc.toFixed(2);
  document.getElementById('totalAmt').textContent = total.toFixed(2);
  document.getElementById('depAmt').textContent   = (total*dep/100).toFixed(2);
  document.getElementById('netAmt').textContent   = (showDep ? total*dep/100 : total).toFixed(2);
}
document.getElementById('depLabel').addEventListener('change', updateDepPreview);
document.getElementById('depPct').addEventListener('input', updateDepPreview);
updateDepPreview();
calcTotal();
</script>
@endpush
@endsection
