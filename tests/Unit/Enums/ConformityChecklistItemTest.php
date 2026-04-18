<?php

namespace Tests\Unit\Enums;

use App\Enums\ConformityChecklistItem;
use PHPUnit\Framework\TestCase;

class ConformityChecklistItemTest extends TestCase
{
    public function test_enum_has_exactly_nine_cases(): void
    {
        $this->assertCount(9, ConformityChecklistItem::cases());
    }

    public function test_all_cases_have_non_empty_labels(): void
    {
        foreach (ConformityChecklistItem::cases() as $case) {
            $this->assertNotEmpty($case->label(), "Case {$case->value} has empty label");
        }
    }

    public function test_all_cases_have_non_empty_descriptions(): void
    {
        foreach (ConformityChecklistItem::cases() as $case) {
            $this->assertNotEmpty($case->description(), "Case {$case->value} has empty description");
        }
    }

    public function test_values_are_snake_case_strings(): void
    {
        foreach (ConformityChecklistItem::cases() as $case) {
            $this->assertMatchesRegularExpression('/^[a-z_]+$/', $case->value);
        }
    }
}
