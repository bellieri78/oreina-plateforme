<?php

namespace App\Services;

use App\Models\LepisBulletin;
use Illuminate\Support\Str;

class LepisAnnouncementRenderer
{
    public function render(LepisBulletin $bulletin): array
    {
        $subject = $bulletin->announcement_subject ?? '';
        $body    = $bulletin->announcement_body ?? '';

        if ($body === '') {
            return ['subject' => $subject, 'body_html' => ''];
        }

        $withTokens = str_replace(
            '{{lien_bulletin}}',
            route('hub.lepis.bulletins.show', $bulletin),
            $body
        );

        return [
            'subject'   => $subject,
            'body_html' => (string) Str::markdown($withTokens),
        ];
    }
}
