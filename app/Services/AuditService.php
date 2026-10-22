<?php
namespace App\Services;

use App\Models\AuditLog;

class AuditService
{
    public function log(string $action, ?object $subject = null, ?string $description = null): void
    {
        $log = AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $subject instanceof \Illuminate\Database\Eloquent\Model ? get_class($subject) : null,
            'subject_id' => $subject instanceof \Illuminate\Database\Eloquent\Model ? ($subject->id ?? null) : null,
            'description' => $description ?? null,
            'ip_address' => request()->ip(),
            'properties' => [
                'text_snippet' => $subject instanceof \Illuminate\Database\Eloquent\Model
                    ? substr((string) $subject->text, 0, 80)
                    : null,
            ],
        ]);
    }
}
