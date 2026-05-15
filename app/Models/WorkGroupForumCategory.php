<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkGroupForumCategory extends Model
{
    protected $fillable = ['work_group_id', 'name', 'description', 'position'];

    public function workGroup(): BelongsTo
    {
        return $this->belongsTo(WorkGroup::class);
    }

    public function threads(): HasMany
    {
        return $this->hasMany(WorkGroupForumThread::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position')->orderBy('name');
    }
}
