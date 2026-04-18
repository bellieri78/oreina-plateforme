<?php

namespace Tests\Unit\Mail;

use App\Mail\AccountInvitation;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_mail_contains_subject_and_signed_claim_url(): void
    {
        $author = User::factory()->ghost()->create(['name' => 'Jean Test']);
        $editor = User::factory()->create(['name' => 'Greg']);
        $submission = Submission::create([
            'author_id' => $author->id,
            'submitted_by_user_id' => $editor->id,
            'title' => 'Mon super papier',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => 'submitted',
        ]);

        $mail = new AccountInvitation($author, $submission, $editor);

        $mail->assertSeeInHtml('Jean Test');
        $mail->assertSeeInHtml('Greg');
        $mail->assertSeeInHtml('Mon super papier');
        $mail->assertSeeInHtml('/claim-account/'.$author->id);
        $mail->assertSeeInHtml('signature=');
        $this->assertSame('Un article vous concernant a été déposé sur Chersotis', $mail->envelope()->subject);
    }
}
