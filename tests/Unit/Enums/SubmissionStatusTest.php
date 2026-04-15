<?php

namespace Tests\Unit\Enums;

use App\Enums\SubmissionStatus;
use PHPUnit\Framework\TestCase;

class SubmissionStatusTest extends TestCase
{
    public function test_all_cases_have_label(): void
    {
        foreach (SubmissionStatus::cases() as $status) {
            $this->assertNotEmpty($status->label());
        }
    }

    public function test_terminal_statuses(): void
    {
        $this->assertTrue(SubmissionStatus::Published->isTerminal());
        $this->assertTrue(SubmissionStatus::Rejected->isTerminal());
        $this->assertFalse(SubmissionStatus::Draft->isTerminal());
        $this->assertFalse(SubmissionStatus::Accepted->isTerminal());
    }

    public function test_editorial_statuses(): void
    {
        $this->assertTrue(SubmissionStatus::UnderInitialReview->isEditorial());
        $this->assertTrue(SubmissionStatus::UnderPeerReview->isEditorial());
        $this->assertTrue(SubmissionStatus::RevisionRequested->isEditorial());
        $this->assertTrue(SubmissionStatus::RevisionAfterReview->isEditorial());
        $this->assertFalse(SubmissionStatus::Draft->isEditorial());
        $this->assertFalse(SubmissionStatus::Published->isEditorial());
    }

    public function test_labels_returns_value_to_label_map(): void
    {
        $labels = SubmissionStatus::labels();
        $this->assertArrayHasKey('draft', $labels);
        $this->assertArrayHasKey('under_initial_review', $labels);
        $this->assertCount(10, $labels);
    }

    public function test_colors_are_non_empty(): void
    {
        foreach (SubmissionStatus::cases() as $status) {
            $this->assertNotEmpty($status->color());
        }
    }
}
