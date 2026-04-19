<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleEvent extends Model
{
    public const TYPE_VIEW = 'view';
    public const TYPE_PDF_DOWNLOAD = 'pdf_download';
    public const TYPE_SHARE = 'share';

    public const NETWORKS = ['twitter', 'linkedin', 'mail', 'copy', 'native'];

    protected $fillable = [
        'submission_id',
        'event_type',
        'hashed_ip',
        'cookie_id',
        'network',
        'user_agent',
        'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}
