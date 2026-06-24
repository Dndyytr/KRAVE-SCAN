<?php

namespace App\Traits;

use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    /**
     * Boot the LogsActivity trait for a model.
     */
    public static function bootLogsActivity(): void
    {
        static::created(function (Model $model) {
            self::logActivity('created', $model);
        });

        static::updated(function (Model $model) {
            self::logActivity('updated', $model);
        });

        static::deleted(function (Model $model) {
            self::logActivity('deleted', $model);
        });
    }

    /**
     * Log the activity.
     */
    protected static function logActivity(string $action, Model $model): void
    {
        // Don't log ActivityLog model changes to avoid infinite loop
        if ($model instanceof ActivityLog) {
            return;
        }

        $ignoredAttributes = ['created_at', 'updated_at', 'password', 'remember_token'];

        $oldValues = null;
        $newValues = null;

        if ($action === 'created') {
            $newValues = array_diff_key($model->getAttributes(), array_flip($ignoredAttributes));
        } elseif ($action === 'updated') {
            $dirty = $model->getDirty();
            $newValues = [];
            $oldValues = [];

            foreach ($dirty as $key => $value) {
                if (in_array($key, $ignoredAttributes)) {
                    continue;
                }
                $oldValues[$key] = $model->getOriginal($key);
                $newValues[$key] = $value;
            }

            // If no tracked values changed (e.g. only updated_at changed), don't log
            if (empty($newValues)) {
                return;
            }
        } elseif ($action === 'deleted') {
            $oldValues = array_diff_key($model->getRawOriginal(), array_flip($ignoredAttributes));
        }

        app(ActivityLogger::class)->log(
            action: $action,
            model: $model,
            oldValues: $oldValues,
            newValues: $newValues
        );
    }
}
