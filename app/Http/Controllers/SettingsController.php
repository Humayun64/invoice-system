<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        $company = Company::first();
        $badges  = Badge::orderBy('position')->orderBy('sort_order')->get();
        return view('settings.index', compact('company', 'badges'));
    }

    public function updateCompany(Request $request)
    {
        $request->validate(['name' => 'required|string|max:200', 'email' => 'nullable|email|max:100']);
        $data = $request->only([
            'name','address','phone','email','paynow','bank_name','bank_account','cheque_name',
            'conclusion_text','signatory_name','signatory_title','important_clauses'
        ]);
        $company = Company::first();
        $company ? $company->update($data) : Company::create($data);
        return redirect()->route('settings.index')->with('success', 'Company settings updated!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate(['current_password' => 'required', 'new_password' => 'required|min:6|confirmed']);
        $user = User::find(session('user_id'));
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        $user->update(['password' => Hash::make($request->new_password)]);
        return redirect()->route('settings.index')->with('success', 'Password changed successfully!');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate(['logo' => 'required|image|mimes:png,jpg,jpeg|max:2048']);
        if (!file_exists(public_path('assets'))) mkdir(public_path('assets'), 0755, true);
        $request->file('logo')->move(public_path('assets'), 'logo.png');
        return redirect()->route('settings.index')->with('success', 'Main logo updated!');
    }

    public function addBadge(Request $request)
    {
        $request->validate([
            'position' => 'required|in:left,right',
            'badge'    => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);
        if (!file_exists(public_path('assets/badges'))) mkdir(public_path('assets/badges'), 0755, true);
        $name = 'badge_'.time().'_'.rand(100,999).'.'.$request->file('badge')->getClientOriginalExtension();
        $request->file('badge')->move(public_path('assets/badges'), $name);
        $max = Badge::where('position', $request->position)->max('sort_order') ?? 0;
        Badge::create(['position' => $request->position, 'image_path' => 'assets/badges/'.$name, 'sort_order' => $max + 1]);
        return redirect()->route('settings.index')->with('success', 'Badge added!');
    }

    public function uploadSign(Request $request)
    {
        $request->validate([
            'type' => 'required|in:signature,stamp',
            'file' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);
        if (!file_exists(public_path('assets'))) mkdir(public_path('assets'), 0755, true);
        $name = $request->type.'.png';
        $request->file('file')->move(public_path('assets'), $name);
        Company::first()?->update([$request->type.'_path' => 'assets/'.$name]);
        return redirect()->route('settings.index')->with('success', ucfirst($request->type).' uploaded!');
    }

    public function removeSign(Request $request)
    {
        $request->validate(['type' => 'required|in:signature,stamp']);
        $c = Company::first();
        $col = $request->type.'_path';
        if ($c->$col && file_exists(public_path($c->$col))) unlink(public_path($c->$col));
        $c->update([$col => null]);
        return redirect()->route('settings.index')->with('success', ucfirst($request->type).' removed.');
    }

    public function removeBadge($id)
    {
        $badge = Badge::findOrFail($id);
        $path  = public_path($badge->image_path);
        if (file_exists($path)) unlink($path);
        $badge->delete();
        return redirect()->route('settings.index')->with('success', 'Badge removed.');
    }
}
