<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model {
    protected $fillable = [
        'user_id','user_name','action','module','record_id','record_ref',
        'description','old_data','new_data','ip_address'
    ];
    protected $casts = ['old_data' => 'array', 'new_data' => 'array'];

    /**
     * Quick logger.
     * ActivityLog::record('deleted', 'invoice', $inv->id, $inv->invoice_no, 'Deleted invoice', $inv->toArray());
     */
    public static function record(string $action, string $module, $recordId = null, $ref = null, $desc = null, $old = null, $new = null): void
    {
        try {
            static::create([
                'user_id'     => session('user_id'),
                'user_name'   => session('user_name', 'System'),
                'action'      => $action,
                'module'      => $module,
                'record_id'   => $recordId,
                'record_ref'  => $ref,
                'description' => $desc,
                'old_data'    => $old,
                'new_data'    => $new,
                'ip_address'  => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            // never break the app because of logging
        }
    }
}
