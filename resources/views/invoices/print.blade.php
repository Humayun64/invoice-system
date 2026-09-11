<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice {{ $invoice->invoice_no }}</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:Arial,sans-serif;background:#f0f0f0;color:#1a1a1a;font-size:13px;}
.toolbar{background:#7b1313;color:#fff;padding:12px 28px;display:flex;gap:10px;align-items:center;justify-content:space-between;}
.btn{padding:7px 16px;border-radius:6px;font-size:0.82rem;font-weight:600;cursor:pointer;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:5px;}
.btn-white{background:#fff;color:#7b1313;} .btn-green{background:#059669;color:#fff;} .btn-outline{background:transparent;border:1px solid rgba(255,255,255,0.4);color:#fff;}
.page-wrap{padding:28px;display:flex;justify-content:center;}
.invoice{background:#fff;width:794px;padding:36px 46px 46px;box-shadow:0 4px 24px rgba(0,0,0,0.12);}
.hdr-top{display:flex;align-items:center;gap:16px;}
.logo-main{width:100px;height:auto;max-height:80px;object-fit:contain;flex-shrink:0;}
.co-block{flex:1;text-align:center;}
.co-name{font-family:'Times New Roman',serif;font-size:1.75rem;font-weight:900;letter-spacing:0.5px;line-height:1.1;}
.co-info{font-size:0.82rem;color:#333;margin-top:6px;line-height:1.7;}
.co-info .blue{color:#1a73e8;}
.hdr-badges{display:flex;justify-content:space-between;align-items:flex-end;margin-top:4px;min-height:52px;}
.badge-group{display:flex;gap:10px;align-items:flex-end;}
.badge-img{height:50px;width:auto;max-width:140px;object-fit:contain;}
.inv-banner{background:#7b1313;color:#fff;text-align:center;font-size:1.45rem;font-weight:600;letter-spacing:8px;padding:8px 0;margin:6px 0 18px;}
.inv-meta{display:flex;justify-content:space-between;margin-bottom:18px;}
.client-block{font-size:0.87rem;line-height:1.75;}
.client-block .lbl{font-size:0.77rem;color:#777;} .client-block .cname{font-weight:700;font-size:0.97rem;}
.meta-right{text-align:right;font-size:0.87rem;line-height:1.9;}
.meta-right .ml{color:#777;font-size:0.77rem;} .meta-right .mv{font-weight:700;}
.proj-row{display:flex;align-items:baseline;gap:10px;border-bottom:2px solid #1a1a1a;padding-bottom:5px;margin-bottom:16px;font-size:0.87rem;}
.proj-row .pl{color:#555;} .proj-row .pt{font-weight:700;}
.scope-title{text-align:center;font-weight:700;font-size:0.97rem;margin-bottom:8px;}
.scope-table{width:100%;border-collapse:collapse;}
.scope-table th{border:1px solid #bbb;padding:8px 10px;text-align:center;font-weight:700;font-size:0.82rem;}
.scope-table td{border:1px solid #bbb;padding:8px 10px;font-size:0.86rem;vertical-align:top;}
.scope-table td:first-child{text-align:center;width:44px;}
.scope-table .col-desc{text-align:left;} .scope-table .col-qty,.scope-table .col-unit{text-align:center;width:58px;} .scope-table .col-up{text-align:right;width:95px;} .scope-table .col-amt{text-align:right;width:115px;white-space:nowrap;}
.scope-table td ul{padding-left:16px;margin:2px 0;list-style:disc;} .scope-table td ol{padding-left:16px;margin:2px 0;list-style:decimal;} .scope-table td li{margin:1px 0;}
.tr-total td,.tr-dep td,.tr-net td{text-align:right;font-weight:700;padding:8px 10px;}
.tr-net td{font-size:1rem;}
.pay-section{margin-top:26px;} .pay-title{font-weight:700;font-size:0.92rem;margin-bottom:8px;}
.pay-grid{display:grid;grid-template-columns:auto 1fr;gap:3px 14px;font-size:0.86rem;} .pay-grid .pm{font-weight:700;}
.sig{margin-top:30px;font-size:0.86rem;} .sig-name{font-weight:700;font-size:0.92rem;margin-top:2px;} .sig-role{color:#555;}
#overlay{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.55);z-index:9999;justify-content:center;align-items:center;flex-direction:column;gap:12px;}
#overlay.show{display:flex;} #overlay p{color:#fff;font-size:0.95rem;font-weight:600;}
.spinner{width:44px;height:44px;border:4px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.8s linear infinite;}
@keyframes spin{to{transform:rotate(360deg);}}
@media (max-width:860px){
  .toolbar{flex-wrap:wrap;gap:8px;padding:10px 12px;}
  .toolbar>div{flex-wrap:wrap;}
  .page-wrap{padding:10px;overflow-x:auto;justify-content:flex-start;}
}
@media print{.toolbar{display:none!important;}.page-wrap{padding:0;}body{background:#fff;}.invoice{box-shadow:none;width:100%;padding:20px 28px;}}
</style>
</head>
<body>
<div id="overlay"><div class="spinner"></div><p>Generating PDF...</p></div>
<div class="toolbar">
  <div style="display:flex;gap:8px;">
    <a href="{{ route('invoices.index') }}" class="btn btn-outline">← Back</a>
    <a href="{{ route('invoices.edit',$invoice->id) }}" class="btn btn-outline">✏️ Edit</a>
  </div>
  <div style="display:flex;gap:8px;">
    <button onclick="dlPDF()" class="btn btn-green">⬇ Download PDF</button>
    <button onclick="window.print()" class="btn btn-white">🖨️ Print</button>
  </div>
</div>
<div class="page-wrap"><div class="invoice" id="inv">

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

  <div class="inv-banner">I N V O I C E</div>

  <div class="inv-meta">
    <div class="client-block">
      <div class="lbl">Client's Name:</div>
      <div class="cname">{{ $invoice->client->name }}</div>
      @if($invoice->client->designation)<div>{{ $invoice->client->designation }}</div>@endif
      @if($invoice->client->company)<div>{{ $invoice->client->company }}</div>@endif
      @if($invoice->client->address)<div>{{ $invoice->client->address }}</div>@endif
      @if($invoice->client->postal_code)<div>{{ $invoice->client->postal_code }}</div>@endif
    </div>
    <div class="meta-right">
      <div><span class="ml">Date: </span><span class="mv">{{ $invoice->invoice_date->format('d.m.Y') }}</span></div>
      <div><span class="ml">Invoice No. </span><span class="mv">{{ $invoice->invoice_no }}</span></div>
    </div>
  </div>

  <div class="proj-row"><span class="pl">Project Title:</span><span class="pt">{{ $invoice->project_title }}</span></div>

  <div class="scope-title">Scope of Work and Cost Breakdown</div>
  <table class="scope-table">
    <thead><tr>
      <th>No.</th><th class="col-desc" style="text-align:left;">Description of Work</th>
      @if($invoice->show_qty)<th class="col-qty">QTY</th><th class="col-unit">UNIT</th><th class="col-up">UNIT PRICE ($)</th>@endif
      <th class="col-amt">AMOUNT (SGD)</th>
    </tr></thead>
    <tbody>
      @foreach($invoice->items as $k => $item)
      <tr>
        <td>{{ $k+1 }}</td>
        <td class="col-desc">{!! strip_tags($item->description,'<b><strong><i><em><u><ul><ol><li><br><p><span>') !!}</td>
        @if($invoice->show_qty)
          <td class="col-qty">{{ $item->qty }}</td>
          <td class="col-unit">{{ $item->unit }}</td>
          <td class="col-up">{{ $item->unit_price !== null ? '$'.number_format($item->unit_price,2) : '' }}</td>
        @endif
        <td class="col-amt">${{ number_format($item->amount,2) }}</td>
      </tr>
      @endforeach
      @php $cs = $invoice->show_qty ? 5 : 2; @endphp
      @if($invoice->show_discount)
        <tr class="tr-total" style="font-weight:700;"><td colspan="{{ $cs }}" style="text-align:right;border:1px solid #bbb;">Sub Total</td><td class="col-amt">${{ number_format($subtotal,2) }}</td></tr>
        <tr class="tr-dep" style="color:#b91c1c;"><td colspan="{{ $cs }}" style="text-align:right;border:1px solid #bbb;">{{ $invoice->discount_label }}{{ $invoice->discount_type==='percent' ? ' ('.rtrim(rtrim(number_format($invoice->discount_value,2,'.',''),'0'),'.').'%)' : '' }}</td><td class="col-amt">- ${{ number_format($discount,2) }}</td></tr>
      @endif
      <tr class="tr-total"><td colspan="{{ $cs }}" style="text-align:right;border:1px solid #bbb;">TOTAL PROJECT COST</td><td class="col-amt">${{ number_format($total,2) }}</td></tr>
      @if($invoice->show_deposit)
        <tr class="tr-dep"><td colspan="{{ $invoice->show_qty ? 5 : 2 }}" style="text-align:right;border:1px solid #bbb;">{{ $invoice->deposit_label }} Invoice Deposit upon acceptance</td><td class="col-amt" style="font-weight:700;">{{ (int)$invoice->deposit_percent }}%</td></tr>
        <tr class="tr-net"><td colspan="{{ $invoice->show_qty ? 5 : 2 }}" style="text-align:right;border:1px solid #bbb;font-weight:700;">TOTAL NET TO PAY</td><td class="col-amt">${{ number_format($deposit,2) }}</td></tr>
      @else
        <tr class="tr-net"><td colspan="{{ $invoice->show_qty ? 5 : 2 }}" style="text-align:right;border:1px solid #bbb;font-weight:700;">TOTAL NET TO PAY</td><td class="col-amt">${{ number_format($total,2) }}</td></tr>
      @endif
    </tbody>
  </table>

  <div class="pay-section">
    <div class="pay-title">Mode of Payment :</div>
    <div class="pay-grid">
      <span class="pm">Bank Transfer:</span><span>{{ $company->bank_name }} {{ $company->bank_account }}</span>
      <span class="pm">Cheque Payment:</span><span>{{ $company->cheque_name }}</span>
      <span class="pm">PayNow:</span><span>{{ $company->paynow }}</span>
    </div>
  </div>

  <div class="sig">
    <div>Sincerely yours,</div>
    @if($company->signature_path && file_exists(public_path($company->signature_path)))
      <img src="{{ asset($company->signature_path) }}" style="height:55px;object-fit:contain;display:block;margin:6px 0 0;">
    @else
      <div style="height:40px;"></div>
    @endif
    <div class="sig-name">{{ $company->signatory_name ?? 'Mr. Shahadat' }}</div>
    <div class="sig-role">{{ $company->signatory_title ?? 'Operations Manager' }}</div>
    @if($company->stamp_path && file_exists(public_path($company->stamp_path)))
      <img src="{{ asset($company->stamp_path) }}" style="height:85px;object-fit:contain;display:block;margin-top:8px;">
    @endif
  </div>
</div></div>

<script>
function dlPDF(){
  const ov=document.getElementById('overlay'); ov.classList.add('show');
  const el=document.getElementById('inv');
  html2canvas(el,{scale:2,useCORS:true,backgroundColor:'#ffffff',width:el.offsetWidth,height:el.offsetHeight,scrollX:0,scrollY:0}).then(canvas=>{
    const {jsPDF}=window.jspdf; const a4W=210,a4H=297;
    const cH=(canvas.height*a4W)/canvas.width;
    const pdf=new jsPDF('p','mm','a4');
    if(cH<=a4H){pdf.addImage(canvas.toDataURL('image/png'),'PNG',0,0,a4W,cH);}
    else{
      const ph=Math.floor((a4H/cH)*canvas.height);let rem=canvas.height,oy=0,pg=0;
      while(rem>0){const sh=Math.min(ph,rem);const pc=document.createElement('canvas');pc.width=canvas.width;pc.height=sh;
        pc.getContext('2d').drawImage(canvas,0,oy,canvas.width,sh,0,0,canvas.width,sh);
        if(pg>0)pdf.addPage();pdf.addImage(pc.toDataURL('image/png'),'PNG',0,0,a4W,(sh*a4W)/canvas.width);
        oy+=sh;rem-=sh;pg++;}
    }
    pdf.save('Invoice-{{ $invoice->invoice_no }}.pdf'); ov.classList.remove('show');
  }).catch(()=>{ov.classList.remove('show');alert('Failed. Use Print instead.');});
}
</script>
</body></html>
