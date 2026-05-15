<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class WorkGroupResource extends Model
{
    protected $fillable = [
        'work_group_id', 'category', 'title', 'description',
        'type', 'file_path', 'external_url', 'added_by_member_id',
    ];

    public function workGroup(): BelongsTo
    {
        return $this->belongsTo(WorkGroup::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'added_by_member_id');
    }

    public function categoryLabel(): string
    {
        return config('work_group_resources.categories.' . $this->category, $this->category);
    }

    public function url(): ?string
    {
        if ($this->type === 'file' && $this->file_path) {
            return Storage::url($this->file_path);
        }

        return $this->external_url;
    }

    public function isFile(): bool
    {
        return $this->type === 'file';
    }
}
