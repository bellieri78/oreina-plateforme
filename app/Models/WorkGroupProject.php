<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkGroupProject extends Model
{
    protected $fillable = ['work_group_id', 'title', 'description', 'status', 'deliverable_url'];

    public function workGroup(): BelongsTo
    {
        return $this->belongsTo(WorkGroup::class);
    }

    public function statusLabel(): string
    {
        return config('work_group_projects.statuses.' . $this->status, $this->status);
    }

    public function scopeOrdered($query)
    {
        return $query
            ->orderByRaw("CASE status WHEN 'a_lancer' THEN 0 WHEN 'en_cours' THEN 1 WHEN 'diffuse' THEN 2 ELSE 3 END")
            ->orderByDesc('created_at');
    }
}
