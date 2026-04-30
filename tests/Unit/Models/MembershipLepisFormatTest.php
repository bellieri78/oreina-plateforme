<?php

namespace Tests\Unit\Models;

use App\Models\Membership;
use Tests\TestCase;

class MembershipLepisFormatTest extends TestCase
{
    public function test_constants_are_defined(): void
    {
        $this->assertSame('paper', Membership::LEPIS_FORMAT_PAPER);
        $this->assertSame('digital', Membership::LEPIS_FORMAT_DIGITAL);
    }

    public function test_lepis_format_or_default_returns_value_when_set(): void
    {
        $m = new Membership(['lepis_format' => 'digital']);
        $this->assertSame('digital', $m->lepisFormatOrDefault());
    }

    public function test_lepis_format_or_default_returns_paper_when_null(): void
    {
        $m = new Membership(['lepis_format' => null]);
        $this->assertSame('paper', $m->lepisFormatOrDefault());
    }
}
