<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            self::writeLog('created', $model, [], $model->getAttributes());
        });

        static::updated(function ($model) {
            $changed = $model->getChanges();
            $old     = array_intersect_key($model->getOriginal(), $changed);
            // Jangan log timestamp saja
            unset($changed['updated_at'], $old['updated_at']);
            if (!empty($changed)) {
                self::writeLog('updated', $model, $old, $changed);
            }
        });

        static::deleted(function ($model) {
            self::writeLog('deleted', $model, $model->getAttributes(), []);
        });
    }

    protected static function writeLog(string $action, $model, array $old, array $new): void
    {
        // Field sensitif yang tidak perlu di-log
        $hidden = ['password', 'remember_token', 'api_token'];
        $old    = array_diff_key($old, array_flip($hidden));
        $new    = array_diff_key($new, array_flip($hidden));

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'model_type'  => get_class($model),
            'model_id'    => $model->getKey(),
            'model_label' => method_exists($model, 'getActivityLabel')
                                ? $model->getActivityLabel()
                                : (string) $model->getKey(),
            'old_values'  => $old ?: null,
            'new_values'  => $new ?: null,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
        ]);
    }

    // Override di model untuk label yang lebih deskriptif
    // public function getActivityLabel(): string { return $this->nomor_pengajuan; }
}
