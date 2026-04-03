<?php

namespace App\View\Composers;

use App\Models\Event;
use Illuminate\View\View;

class MemberLayoutComposer
{
    public function compose(View $view): void
    {
        if ($view->offsetExists('upcomingEvents')) {
            return;
        }

        $view->with('upcomingEvents', Event::where('status', 'published')
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->limit(10)
            ->get());
    }
}
