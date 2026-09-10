@extends('layouts.app')
@section('title','Settings')
@section('page_title','⚙️ Settings')

@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start;">

  <!-- LEFT COLUMN -->
  <div>
    <!-- Company Settings -->
    <div class="form-card">
      <h3>🏢 Company Information</h3>
      <form method="POST" action="{{ route('settings.company') }}">
        @csrf
        <div class="form-group" style="margin-bottom:12px;"><label>Company Name *</label><input type="text" name="name" value="{{ old('name',$company->name??'') }}" required></div>
        <div class="form-group" style="margin-bottom:12px;"><label>Address</label><textarea name="address" rows="2">{{ old('address',$company->address??'') }}</textarea></div>
        <div class="form-row" style="margin-bottom:12px;">
          <div class="form-group"><label>Phone</label><input type="text" name="phone" value="{{ old('phone',$company->phone??'') }}"></div>
          <div class="form-group"><label>Email</label><input type="email" name="email" value="{{ old('email',$company->email??'') }}"></div>
        </div>
        <div class="form-row" style="margin-bottom:12px;">
          <div class="form-group"><label>Bank Name</label><input type="text" name="bank_name" value="{{ old('bank_name',$company->bank_name??'') }}"></div>
          <div class="form-group"><label>Bank Account</label><input type="text" name="bank_account" value="{{ old('bank_account',$company->bank_account??'') }}"></div>
        </div>
        <div class="form-row" style="margin-bottom:12px;">
          <div class="form-group"><label>Cheque Name</label><input type="text" name="cheque_name" value="{{ old('cheque_name',$company->cheque_name??'') }}"></div>
          <div class="form-group"><label>PayNow</label><input type="text" name="paynow" value="{{ old('paynow',$company->paynow??'') }}"></div>
        </div>
        <div class="form-row" style="margin-bottom:12px;">
          <div class="form-group"><label>Signatory Name</label><input type="text" name="signatory_name" value="{{ old('signatory_name',$company->signatory_name??'Mr. Shahadat') }}"></div>
          <div class="form-group"><label>Signatory Title</label><input type="text" name="signatory_title" value="{{ old('signatory_title',$company->signatory_title??'Operations Manager') }}"></div>
        </div>
        <div class="form-group" style="margin-bottom:12px;"><label>Default Conclusion (Quotation)</label><textarea name="conclusion_text" rows="4">{{ old('conclusion_text',$company->conclusion_text??'') }}</textarea></div>
        <div class="form-group" style="margin-bottom:16px;"><label>Default Important Clauses (Quotation)</label><textarea name="important_clauses" rows="10">{{ old('important_clauses',$company->important_clauses??'') }}</textarea></div>
        <button type="submit" class="btn btn-primary" style="width:100%;">💾 Save Company Info</button>
      </form>
    </div>
  </div>

  <!-- RIGHT COLUMN -->
  <div>
    <!-- Main Logo -->
    <div class="form-card">
      <h3>🖼️ Main Logo</h3>
      @if(file_exists(public_path('assets/logo.png')))
        <img src="{{ asset('assets/logo.png') }}?{{ time() }}" style="height:70px;object-fit:contain;margin-bottom:12px;border:1px solid #e2e8f0;border-radius:8px;padding:6px;background:#fff;">
      @endif
      <form method="POST" action="{{ route('settings.logo') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group" style="margin-bottom:10px;"><label>Upload PNG/JPG (max 2MB)</label><input type="file" name="logo" accept="image/png,image/jpeg" required></div>
        <button type="submit" class="btn btn-primary btn-sm">📤 Upload Main Logo</button>
      </form>
    </div>

    <!-- Trust Badges -->
    <div class="form-card">
      <h3>🏅 Trust Badges (Quotation header)</h3>
      <p style="font-size:0.8rem;color:#64748b;margin-bottom:14px;"><strong>Unlimited badges.</strong> Choose a file → click <strong>+ Add</strong>. Repeat to add more. Hover ✕ to remove.</p>

      @foreach(['left' => 'Left Side', 'right' => 'Right Side'] as $pos => $label)
      <div style="border:1px solid #e2e8f0;border-radius:8px;padding:12px;margin-bottom:12px;">
        <div style="font-weight:600;font-size:0.85rem;margin-bottom:8px;">{{ $label }}</div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
          @forelse($badges->where('position',$pos) as $b)
            <div style="position:relative;border:1px solid #e2e8f0;border-radius:6px;padding:6px;background:#fff;">
              <img src="{{ asset($b->image_path) }}" style="height:46px;object-fit:contain;display:block;">
              <form method="POST" action="{{ route('settings.badge.remove',$b->id) }}" style="position:absolute;top:-8px;right:-8px;" onsubmit="return confirm('Remove this badge?')">
                @csrf @method('DELETE')
                <button type="submit" style="width:20px;height:20px;border-radius:50%;background:#dc2626;color:#fff;border:none;cursor:pointer;font-size:11px;line-height:1;">✕</button>
              </form>
            </div>
          @empty
            <span style="font-size:0.8rem;color:#94a3b8;">No badges yet.</span>
          @endforelse
        </div>
        <form method="POST" action="{{ route('settings.badge.add') }}" enctype="multipart/form-data" style="display:flex;gap:8px;align-items:center;">
          @csrf <input type="hidden" name="position" value="{{ $pos }}">
          <input type="file" name="badge" accept="image/png,image/jpeg" required style="flex:1;">
          <button type="submit" class="btn btn-primary btn-sm">+ Add Badge</button>
        </form>
      </div>
      @endforeach
    </div>

    <!-- Signature & Stamp -->
    <div class="form-card">
      <h3>✍️ Signature & Company Stamp</h3>
      <p style="font-size:0.8rem;color:#64748b;margin-bottom:14px;">Shown at the bottom of quotations under "Sincerely yours". Use PNG with transparent background for best result.</p>

      @foreach(['signature' => 'Manager Signature', 'stamp' => 'Company Stamp'] as $type => $label)
      @php $path = $company->{$type.'_path'} ?? null; @endphp
      <div style="border:1px solid #e2e8f0;border-radius:8px;padding:12px;margin-bottom:12px;">
        <div style="font-weight:600;font-size:0.85rem;margin-bottom:8px;">{{ $label }}</div>
        @if($path && file_exists(public_path($path)))
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
            <img src="{{ asset($path) }}?{{ time() }}" style="height:60px;object-fit:contain;border:1px solid #e2e8f0;border-radius:6px;padding:4px;background:#fff;">
            <form method="POST" action="{{ route('settings.sign.remove') }}" onsubmit="return confirm('Remove {{ $label }}?')">
              @csrf <input type="hidden" name="type" value="{{ $type }}">
              <button type="submit" class="btn btn-del btn-sm">✕ Remove</button>
            </form>
          </div>
        @endif
        <form method="POST" action="{{ route('settings.sign.upload') }}" enctype="multipart/form-data" style="display:flex;gap:8px;align-items:center;">
          @csrf <input type="hidden" name="type" value="{{ $type }}">
          <input type="file" name="file" accept="image/png,image/jpeg" required style="flex:1;">
          <button type="submit" class="btn btn-primary btn-sm">{{ $path ? 'Replace' : 'Upload' }}</button>
        </form>
      </div>
      @endforeach
    </div>

    <!-- Change Password -->
    <div class="form-card">
      <h3>🔐 Change Password</h3>
      <form method="POST" action="{{ route('settings.password') }}">
        @csrf
        <div class="form-group" style="margin-bottom:12px;">
          <label>Current Password</label>
          <input type="password" name="current_password" required>
          @error('current_password')<p style="color:#b91c1c;font-size:0.82rem;margin-top:4px;">{{ $message }}</p>@enderror
        </div>
        <div class="form-group" style="margin-bottom:12px;"><label>New Password</label><input type="password" name="new_password" placeholder="Minimum 6 characters" required></div>
        <div class="form-group" style="margin-bottom:16px;"><label>Confirm New Password</label><input type="password" name="new_password_confirmation" required></div>
        <button type="submit" class="btn btn-primary" style="width:100%;">💾 Update Password</button>
      </form>
    </div>
  </div>

</div>
@endsection
