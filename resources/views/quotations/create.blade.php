@extends('layouts.app')
@section('title', isset($quotation) ? 'Edit Quotation' : 'Create Quotation')
@section('page_title', isset($quotation) ? '✏️ Edit Quotation' : '➕ Create Quotation')

@section('content')
@php
  $isEdit  = isset($quotation);
  $items   = $isEdit ? $quotation->items : collect([]);
  $action  = $isEdit ? route('quotations.update',$quotation->id) : route('quotations.store');
  $defConc = $company->conclusion_text ?? '';
@endphp

<form method="POST" action="{{ $action }}" onsubmit="return syncRTE()">
  @csrf
  @if($isEdit) @method('PUT') @endif

  <!-- Quotation Details -->
  <div class="form-card">
    <h3>Quotation Details</h3>
    <div class="form-row">
      <div class="form-group">
        <label>Quotation No *</label>
        <input type="text" name="quotation_no" value="{{ old('quotation_no', $isEdit ? $quotation->quotation_no : $nextNo ?? '') }}" required>
      </div>
      <div class="form-group">
        <label>Date *</label>
        <input type="date" name="quotation_date" value="{{ old('quotation_date', $isEdit ? $quotation->quotation_date->format('Y-m-d') : date('Y-m-d')) }}" required>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Valid for (days)</label>
        <input type="number" name="valid_days" value="{{ old('valid_days', $isEdit ? $quotation->valid_days : 15) }}" min="1">
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          @foreach(['draft','sent','accepted','rejected'] as $s)
            <option value="{{ $s }}" {{ old('status', $isEdit ? $quotation->status : 'draft') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Client *</label>
        <select name="client_id" required>
          <option value="">— Choose Client —</option>
          @foreach($clients as $c)
            <option value="{{ $c->id }}" {{ old('client_id', $isEdit ? $quotation->client_id : '') == $c->id ? 'selected' : '' }}>
              {{ $c->name }}{{ $c->company ? ' — '.$c->company : '' }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>Project Title *</label>
        <input type="text" name="project_title" value="{{ old('project_title', $isEdit ? $quotation->project_title : '') }}" required>
      </div>
    </div>
    <p style="font-size:0.8rem;color:#64748b;margin-top:4px;">Need a new client? <a href="{{ route('clients.index') }}" target="_blank" style="color:#7b1313;">Add here →</a></p>
  </div>

  <!-- Intro -->
  <div class="form-card">
    <h3>Introduction (Dear...)</h3>
    <p style="font-size:0.82rem;color:#64748b;margin-bottom:10px;">This paragraph appears below the client name — "Dear [Name], This scope covers..."</p>
    <div class="form-group">
      <label>Intro Paragraph</label>
      <textarea name="intro_text" rows="4" placeholder="e.g. This scope covers painting works...">{{ old('intro_text', $isEdit ? $quotation->intro_text : '') }}</textarea>
    </div>
  </div>

  <!-- Options -->
  <div class="form-card">
    <h3>Options</h3>
    <div class="toggle-row">
      <label class="switch"><input type="checkbox" name="show_qty" id="toggleQty" {{ ($isEdit && $quotation->show_qty) ? 'checked' : '' }}><span class="slider"></span></label>
      <div><div class="toggle-label">Use QTY / UNIT / UNIT PRICE</div><div class="toggle-sub">Auto-calculates amount from quantity × unit price</div></div>
    </div>
    <div class="toggle-row">
      <label class="switch"><input type="checkbox" name="show_discount" id="toggleDisc" {{ ($isEdit && $quotation->show_discount) ? 'checked' : '' }}><span class="slider"></span></label>
      <div><div class="toggle-label">Apply Discount</div><div class="toggle-sub">Shows Sub Total → Discount → Payable Amount</div></div>
    </div>
    <div id="discSection" style="{{ ($isEdit && $quotation->show_discount) ? '' : 'display:none' }};background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-top:4px;">
      <div class="form-row three">
        <div class="form-group">
          <label>Discount Label</label>
          <input type="text" name="discount_label" value="{{ old('discount_label', $isEdit ? $quotation->discount_label : 'Goodwill Discount') }}" placeholder="Goodwill Discount">
        </div>
        <div class="form-group">
          <label>Discount Type</label>
          <select name="discount_type" id="discType" onchange="calcTotal()">
            <option value="amount"  {{ old('discount_type', $isEdit ? $quotation->discount_type : 'amount') === 'amount'  ? 'selected' : '' }}>Fixed Amount (SGD)</option>
            <option value="percent" {{ old('discount_type', $isEdit ? $quotation->discount_type : 'amount') === 'percent' ? 'selected' : '' }}>Percentage (%)</option>
          </select>
        </div>
        <div class="form-group">
          <label id="discValLbl">Discount Value</label>
          <input type="number" name="discount_value" id="discVal" value="{{ old('discount_value', $isEdit ? $quotation->discount_value : 0) }}" min="0" step="0.01" oninput="calcTotal()">
        </div>
      </div>
    </div>
  </div>

  <!-- Line Items -->
  <div class="form-card">
    <h3>Scope of Works</h3>
    <div id="tableWrap" class="{{ ($isEdit && $quotation->show_qty) ? 'show-qty' : '' }}">
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
      <div class="total-row"><span>Sub Total:</span><span>SGD <strong id="totalAmt">0.00</strong></span></div>
      <div class="total-row dep-row" id="discRow" style="{{ ($isEdit && $quotation->show_discount) ? '' : 'display:none' }}"><span id="discRowLbl">Discount:</span><span>- SGD <strong id="discDisp">0.00</strong></span></div>
      <div class="total-row grand"><span>PAYABLE AMOUNT:</span><span>SGD <strong id="netAmt">0.00</strong></span></div>
    </div>
  </div>

  <!-- Important Clauses -->
  <div class="form-card">
    <h3>Important Clauses</h3>
    <p style="font-size:0.82rem;color:#64748b;margin-bottom:10px;">Shown below the scope table. Leave empty to hide. Default from Settings.</p>
    <div class="form-group">
      <label>Clauses</label>
      <textarea name="important_clauses" rows="10">{{ old('important_clauses', $isEdit ? $quotation->important_clauses : ($company->important_clauses ?? '')) }}</textarea>
    </div>
  </div>

  <!-- Terms & Conditions -->
  <div class="form-card">
    <h3>Terms & Conditions</h3>
    <div class="form-group" style="margin-bottom:14px;">
      <label>Payment Terms</label>
      <textarea name="payment_terms" rows="4" placeholder="e.g. 50% downpayment upon signing...">{{ old('payment_terms', $isEdit ? $quotation->payment_terms : "50% downpayment upon signing of quotation\n50% full payment upon completion of work") }}</textarea>
    </div>
    <div class="form-group">
      <label>Price Basis / Notes</label>
      <textarea name="price_basis" rows="3" placeholder="e.g. All prices in SGD and exclude GST...">{{ old('price_basis', $isEdit ? $quotation->price_basis : "All prices are in Singapore Dollars (SGD) and exclude GST (if applicable).\nAny additional works not stated in the quotation will be considered Variation Order and will be billed accordingly.") }}</textarea>
    </div>
  </div>

  <!-- Conclusion -->
  <div class="form-card">
    <h3>Conclusion</h3>
    <div class="form-group">
      <label>Conclusion Paragraph</label>
      <textarea name="conclusion" rows="5">{{ old('conclusion', $isEdit ? $quotation->conclusion : $defConc) }}</textarea>
    </div>
  </div>

  <div style="display:flex;gap:12px;justify-content:flex-end;margin-bottom:40px;">
    <a href="{{ route('quotations.index') }}" class="btn btn-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary">💾 Save Quotation</button>
  </div>
</form>

@push('scripts')
<script>
document.getElementById('toggleQty').addEventListener('change', function() {
  document.getElementById('tableWrap').classList.toggle('show-qty', this.checked); calcTotal();
});
document.getElementById('toggleDisc').addEventListener('change', function() {
  document.getElementById('discSection').style.display = this.checked ? '' : 'none';
  document.getElementById('discRow').style.display = this.checked ? '' : 'none';
  calcTotal();
});
function calcTotal() {
  let total = 0; document.querySelectorAll('.amt-f').forEach(f => total += parseFloat(f.value)||0);
  const showDisc = document.getElementById('toggleDisc').checked;
  const type = document.getElementById('discType').value;
  const val  = parseFloat(document.getElementById('discVal').value)||0;
  const disc = !showDisc ? 0 : (type === 'percent' ? total * val / 100 : val);
  document.getElementById('discValLbl').textContent = type === 'percent' ? 'Discount (%)' : 'Discount Amount (SGD)';
  document.getElementById('discRowLbl').textContent = type === 'percent' ? 'Discount (' + val + '%):' : 'Discount:';
  document.getElementById('totalAmt').textContent = total.toFixed(2);
  document.getElementById('discDisp').textContent = disc.toFixed(2);
  document.getElementById('netAmt').textContent   = (total - disc).toFixed(2);
}
calcTotal();
</script>
@endpush
@endsection
