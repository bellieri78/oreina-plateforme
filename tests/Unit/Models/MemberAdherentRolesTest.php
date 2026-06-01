<?php

namespace Tests\Unit\Models;

use App\Models\Member;
use Tests\TestCase;

class MemberAdherentRolesTest extends TestCase
{
    public function test_bureau_cascades_to_ca(): void
    {
        $m = new Member(['adherent_roles' => ['bureau']]);

        $eff = $m->effectiveAdherentRoles();

        $this->assertContains('bureau', $eff);
        $this->assertContains('ca', $eff);
        $this->assertTrue($m->hasAdherentRole('ca'));
        $this->assertTrue($m->hasAdherentRole('bureau'));
    }

    public function test_validateur_is_orthogonal(): void
    {
        $m = new Member(['adherent_roles' => ['validateur']]);

        $this->assertTrue($m->hasAdherentRole('validateur'));
        $this->assertFalse($m->hasAdherentRole('ca'));
    }

    public function test_simple_member_has_no_roles(): void
    {
        $m = new Member(['adherent_roles' => null]);

        $this->assertSame([], $m->effectiveAdherentRoles());
        $this->assertFalse($m->hasAdherentRole('ca'));
    }
}
