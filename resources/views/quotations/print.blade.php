<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Quotation {{ $quotation->quotation_no }}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:Arial,sans-serif;background:#f0f0f0;color:#000;font-size:13px;}
.invoice{color:#000;}
.toolbar{background:#1d4ed8;color:#fff;padding:12px 28px;display:flex;gap:10px;align-items:center;justify-content:space-between;}
.btn{padding:7px 16px;border-radius:6px;font-size:0.82rem;font-weight:600;cursor:pointer;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:5px;}
.btn-white{background:#fff;color:#1d4ed8;} .btn-green{background:#059669;color:#fff;} .btn-outline{background:transparent;border:1px solid rgba(255,255,255,0.4);color:#fff;}
.page-wrap{padding:28px;display:flex;justify-content:center;}
.invoice{background:#fff;width:794px;padding:36px 46px 46px;box-shadow:0 4px 24px rgba(0,0,0,0.12);}

/* Header */
.hdr-top{display:flex;align-items:center;gap:16px;}
.logo-main{width:100px;height:auto;max-height:80px;object-fit:contain;flex-shrink:0;}
.co-block{flex:1;text-align:center;}
.co-name{font-family:'Times New Roman',serif;font-size:1.75rem;font-weight:900;letter-spacing:0.5px;line-height:1.1;}
.co-info{font-size:0.82rem;color:#000;margin-top:6px;line-height:1.7;}
.co-info .blue{color:#1a73e8 !important;}
.hdr-badges{display:flex;justify-content:space-between;align-items:flex-end;margin-top:4px;min-height:52px;}
.badge-group{display:flex;gap:10px;align-items:flex-end;}
.badge-img{height:50px;width:auto;max-width:140px;object-fit:contain;}
.qt-banner{background:#7b1313;color:#fff !important;text-align:center;font-size:1.45rem;font-weight:600;letter-spacing:8px;padding:8px 0;margin:6px 0 18px;}

.inv-meta{display:flex;justify-content:space-between;margin-bottom:14px;}
.client-block{font-size:0.87rem;line-height:1.75;}
.client-block .lbl{font-size:0.77rem;color:#444;} .client-block .cname{font-weight:700;font-size:0.97rem;}
.meta-right{text-align:right;font-size:0.87rem;line-height:1.9;}
.meta-right .ml{color:#444;font-size:0.77rem;} .meta-right .mv{font-weight:700;}
.proj-row{display:flex;align-items:baseline;gap:10px;border-bottom:2px solid #1a1a1a;padding-bottom:5px;margin-bottom:14px;font-size:0.87rem;}
.proj-row .pl{color:#333;} .proj-row .pt{font-weight:700;}
.dear-section{margin-bottom:18px;font-size:0.88rem;line-height:1.7;}
.dear-section .dear-name{font-weight:700;}

.scope-title{text-align:center;font-weight:700;font-size:1rem;margin-bottom:8px;}
.scope-table{width:100%;border-collapse:collapse;margin-bottom:18px;}
.scope-table th{border:1px solid #bbb;background:#f5f5f5;padding:8px 10px;text-align:center;font-weight:700;font-size:0.82rem;}
.scope-table td{border:1px solid #bbb;padding:8px 10px;font-size:0.86rem;vertical-align:top;}
.scope-table td:first-child{text-align:center;width:44px;}
.scope-table .col-desc{text-align:left;}
.scope-table .col-qty,.scope-table .col-unit{text-align:center;width:58px;}
.scope-table .col-up{text-align:right;width:95px;}
.scope-table .col-amt{text-align:right;width:115px;white-space:nowrap;}
.scope-table td ul{padding-left:16px;margin:2px 0;list-style:disc;} .scope-table td ol{padding-left:16px;margin:2px 0;list-style:decimal;} .scope-table td li{margin:1px 0;}
.tr-sub td{text-align:right;font-weight:700;padding:8px 10px;}
.tr-disc td{text-align:right;font-weight:600;padding:8px 10px;color:#b91c1c !important;}
.tr-total td{text-align:right;font-weight:900;font-size:1rem;padding:8px 10px;}

.clauses{margin-bottom:18px;font-size:0.84rem;line-height:1.65;}
.clauses .ct{font-weight:700;font-size:0.92rem;margin-bottom:6px;}
.clauses p{margin-bottom:4px;}
.clauses .ch{font-weight:700;margin-top:6px;}

.tc-section{margin-bottom:18px;} .tc-title{font-weight:700;font-size:0.95rem;margin-bottom:8px;}
.tc-row{display:flex;margin-bottom:5px;font-size:0.86rem;line-height:1.6;}
.tc-label{font-weight:700;font-style:italic;min-width:120px;flex-shrink:0;} .tc-content{flex:1;} .tc-content p{margin-bottom:3px;}
.pay-section{margin-bottom:18px;} .pay-title{font-weight:700;font-size:0.92rem;margin-bottom:6px;}
.pay-grid{display:grid;grid-template-columns:auto 1fr;gap:3px 14px;font-size:0.86rem;} .pay-grid .pm{font-weight:700;}
.conclusion-section{margin-bottom:24px;font-size:0.86rem;line-height:1.7;}
.sig-section{display:flex;justify-content:space-between;align-items:flex-start;margin-top:10px;}
.sig-left,.sig-right{font-size:0.86rem;} .sig-right{text-align:right;}
.sig-line{border-top:1px solid #1a1a1a;width:220px;margin:32px 0 6px;}
.sig-name{font-weight:700;font-size:0.92rem;margin-top:26px;} .sig-role{color:#333;}
#overlay{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.55);z-index:9999;justify-content:center;align-items:center;flex-direction:column;gap:12px;}
#overlay.show{display:flex;} #overlay p{color:#fff;} .spinner{width:44px;height:44px;border:4px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.8s linear infinite;}
@keyframes spin{to{transform:rotate(360deg);}}
@media (max-width:860px){
  .toolbar{flex-wrap:wrap;gap:8px;padding:10px 12px;position:sticky;top:0;z-index:10;}
  .toolbar>div{flex-wrap:wrap;}
  .page-wrap{padding:10px;overflow-x:auto;justify-content:flex-start;display:block;}
  .invoice{min-width:794px;width:794px;flex-shrink:0;}
  .mobile-hint{display:block;background:#fff8e1;color:#7a5a00;font-size:0.78rem;padding:6px 12px;text-align:center;border-bottom:1px solid #f0d78c;}
}
.mobile-hint{display:none;}

/* ===== PRINT / PDF ===== */
@page { size: A4; margin: 12mm 12mm 14mm 12mm; }
@media print {
  * { -webkit-print-color-adjust:exact !important; print-color-adjust:exact !important; color-adjust:exact !important; }
  html, body { background:#fff !important; }
  .toolbar, .mobile-hint, .pdf-hint, #overlay { display:none !important; }
  .page-wrap { padding:0; display:block; }
  .invoice { box-shadow:none; width:100%; min-width:0; padding:0; margin:0; }
  /* tables: repeat header on every page, never split a row */
  table { page-break-inside:auto; border-collapse:collapse; }
  thead { display:table-header-group; }
  tfoot { display:table-footer-group; }
  tr { page-break-inside:avoid; break-inside:avoid; }
  td, th { page-break-inside:avoid; }
  /* keep blocks together */
  .tc-row, .clauses p, .pay-section, .sig-section, .conclusion-section, .dear-section { page-break-inside:avoid; break-inside:avoid; }
  .clauses .ch, .tc-title, .scope-title, .pay-title { page-break-after:avoid; break-after:avoid; }
  .sig-section { page-break-before:auto; }
  a { text-decoration:none; color:inherit; }
  /* footer page numbers via counter */
  .print-footer { display:block; position:fixed; bottom:0; left:0; right:0; text-align:right; font-size:8pt; color:#888; }
}
.print-footer { display:none; }
</style>
</head>
<body>
<div class="toolbar">
  <div style="display:flex;gap:8px;">
    <a href="{{ route('quotations.index') }}" class="btn btn-outline">← Back</a>
    <a href="{{ route('quotations.edit',$quotation->id) }}" class="btn btn-outline">✏️ Edit</a>
  </div>
  <div style="display:flex;gap:8px;">
    <button onclick="downloadPDF()" class="btn btn-green">⬇ Download PDF</button>
    <button onclick="window.print()" class="btn btn-white">🖨️ Print</button>
  </div>
</div>
<div class="pdf-hint" style="background:#eef6ff;color:#1e40af;font-size:0.8rem;padding:6px 12px;text-align:center;border-bottom:1px solid #bfdbfe;">Click <b>Download PDF</b> → choose <b>Save as PDF</b>. Under <b>More settings</b> turn <b>OFF</b> “Headers and footers” and turn <b>ON</b> “Background graphics” (only needed once — the browser remembers).</div>
<div class="mobile-hint">↔ Swipe sideways to view the full A4 page. PDF download is unaffected.</div>
<div class="page-wrap"><div class="invoice" id="inv">

  <!-- Header -->
  <div class="hdr-top">
    <img src="{{ asset('assets/logo.png') }}" class="logo-main" alt="SKM" onerror="this.style.display='none'">
    <div class="co-block">
      <div class="co-name">{{ $company->name }}</div>
      <div class="co-info">
        📍 {{ $company->address }}<br>
        ☎ <span class="blue">{{ $company->phone }}</span> &nbsp; ✉ <span class="blue">{{ $company->email }}</span>
      </div>
    </div>
    <div style="width:100px;flex-shrink:0;"></div>
  </div>
  <div class="hdr-badges">
    <div class="badge-group">
      @foreach($badgesLeft as $b)<img src="{{ asset($b->image_path) }}" class="badge-img" alt="">@endforeach
    </div>
    <div class="badge-group">
      @foreach($badgesRight as $b)<img src="{{ asset($b->image_path) }}" class="badge-img" alt="">@endforeach
    </div>
  </div>

  <div class="qt-banner">Q U O T A T I O N</div>

  <div class="inv-meta">
    <div class="client-block">
      <div class="lbl">Client's Name:</div>
      <div class="cname">{{ $quotation->client->name }}</div>
      @if($quotation->client->designation)<div>{{ $quotation->client->designation }}</div>@endif
      @if($quotation->client->company)<div>{{ $quotation->client->company }}</div>@endif
      @if($quotation->client->address)<div>{{ $quotation->client->address }}</div>@endif
      @if($quotation->client->postal_code)<div>{{ $quotation->client->postal_code }}</div>@endif
    </div>
    <div class="meta-right">
      <div><span class="ml">Date: </span><span class="mv">{{ $quotation->quotation_date->format('d.m.Y') }}</span></div>
      <div><span class="ml">Quotation No. </span><span class="mv">{{ $quotation->quotation_no }}</span></div>
    </div>
  </div>

  <div class="proj-row"><span class="pl">Project Title:</span><span class="pt">{{ $quotation->project_title }}</span></div>

  @if($quotation->intro_text)
  <div class="dear-section">
    <div>Dear <span class="dear-name">{{ $quotation->client->name }},</span></div>
    <div style="margin-top:6px;">{{ $quotation->intro_text }}</div>
  </div>
  @endif

  <div class="scope-title">Scope of Work and Cost Breakdown</div>
  <table class="scope-table">
    <thead><tr>
      <th>No.</th><th class="col-desc" style="text-align:left;">Description of Work</th>
      @if($quotation->show_qty)<th class="col-up">Unit Rate</th><th class="col-qty">Qty</th><th class="col-unit">Unit</th>@endif
      <th class="col-amt">Total (SGD)</th>
    </tr></thead>
    <tbody>
      @foreach($quotation->items as $k => $item)
      <tr>
        <td>{{ $k+1 }}</td>
        <td class="col-desc">{!! strip_tags($item->description,'<b><strong><i><em><u><ul><ol><li><br><p><span>') !!}</td>
        @if($quotation->show_qty)
          <td class="col-up">{{ $item->unit_price !== null ? '$'.number_format($item->unit_price,2) : '' }}</td>
          <td class="col-qty">{{ $item->qty !== null ? rtrim(rtrim(number_format($item->qty,2,'.',''),'0'),'.') : '' }}</td>
          <td class="col-unit">{{ $item->unit }}</td>
        @endif
        <td class="col-amt">${{ number_format($item->amount,2) }}</td>
      </tr>
      @endforeach
      @php $cs = $quotation->show_qty ? 5 : 2; @endphp
      @if($quotation->show_discount)
        <tr class="tr-sub"><td colspan="{{ $cs }}" style="text-align:right;border:1px solid #bbb;">Sub Total</td><td class="col-amt">${{ number_format($subtotal,2) }}</td></tr>
        <tr class="tr-disc"><td colspan="{{ $cs }}" style="text-align:right;border:1px solid #bbb;">{{ $quotation->discount_label }}{{ $quotation->discount_type==='percent' ? ' ('.rtrim(rtrim(number_format($quotation->discount_value,2,'.',''),'0'),'.').'%)' : '' }}</td><td class="col-amt">- ${{ number_format($discount,2) }}</td></tr>
        <tr class="tr-total"><td colspan="{{ $cs }}" style="text-align:right;border:1px solid #bbb;">Payable Amount</td><td class="col-amt">${{ number_format($payable,2) }}</td></tr>
      @else
        <tr class="tr-total"><td colspan="{{ $cs }}" style="text-align:right;border:1px solid #bbb;">TOTAL PROJECT COST</td><td class="col-amt">${{ number_format($subtotal,2) }}</td></tr>
      @endif
    </tbody>
  </table>

  @if($quotation->important_clauses)
  <div class="clauses">
    <div class="ct">IMPORTANT CLAUSES</div>
    @foreach(explode("\n",$quotation->important_clauses) as $line)
      @php $l = trim($line); @endphp
      @if($l === '')
        <br>
      @elseif(preg_match('/^\d+\.\s*/',$l))
        <p class="ch">{{ $l }}</p>
      @else
        <p>{{ $l }}</p>
      @endif
    @endforeach
  </div>
  @endif

  <div class="tc-section">
    <div class="tc-title">Terms and Conditions:</div>
    <div class="tc-row">
      <div class="tc-label">Validity:</div>
      <div class="tc-content">This quotation is valid for {{ $quotation->valid_days }} days from the date of issue.</div>
    </div>
    @if($quotation->payment_terms)
    <div class="tc-row">
      <div class="tc-label">Payment Terms:</div>
      <div class="tc-content">
        @foreach(explode("\n",$quotation->payment_terms) as $line)
          @if(trim($line))<p>{{ trim($line) }}</p>@endif
        @endforeach
      </div>
    </div>
    @endif
    @if($quotation->price_basis)
    <div class="tc-row">
      <div class="tc-label">Note:</div>
      <div class="tc-content">
        @foreach(explode("\n",$quotation->price_basis) as $line)
          @if(trim($line))<p>{{ trim($line) }}</p>@endif
        @endforeach
      </div>
    </div>
    @endif
  </div>

  <div class="pay-section">
    <div class="pay-title">Payment Method:</div>
    <div class="pay-grid">
      <span class="pm">Bank Transfer :</span><span>{{ $company->bank_name }} {{ $company->bank_account }}</span>
      <span class="pm">Cheque Payment :</span><span>{{ $company->cheque_name }}</span>
      <span class="pm">PayNow :</span><span>{{ $company->paynow }}</span>
    </div>
  </div>

  @if($quotation->conclusion)
  <div class="conclusion-section">
    @foreach(explode("\n",$quotation->conclusion) as $line)
      @if(trim($line))<p style="margin-bottom:6px;">{{ trim($line) }}</p>@endif
    @endforeach
  </div>
  @endif

  <div class="sig-section">
    <div class="sig-left">
      <div>Sincerely yours,</div>
      @if($company->signature_path && file_exists(public_path($company->signature_path)))
        <img src="{{ asset($company->signature_path) }}" style="height:55px;object-fit:contain;display:block;margin:6px 0 0;">
      @else
        <div style="height:40px;"></div>
      @endif
      <div class="sig-name" style="margin-top:2px;">{{ $company->signatory_name ?? 'Mr. Shahadat' }}</div>
      <div class="sig-role">{{ $company->signatory_title ?? 'Operations Manager' }}</div>
      @if($company->stamp_path && file_exists(public_path($company->stamp_path)))
        <img src="{{ asset($company->stamp_path) }}" style="height:85px;object-fit:contain;display:block;margin-top:8px;">
      @endif
    </div>
    <div class="sig-right">
      <div>Accepted and Signed by:</div>
      <div class="sig-line"></div>
      <div style="font-size:0.82rem;color:#555;">Date:</div>
    </div>
  </div>

</div></div>
<script>
function downloadPDF(){
  const t = document.title;
  document.title = 'Quotation-{{ $quotation->quotation_no }}';
  window.print();
  setTimeout(()=>{ document.title = t; }, 1000);
}
</script>
</body></html>
