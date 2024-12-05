<?php
namespace App\Services\AccessibilityAnalyzer\Rules;

use App\Contracts\HtmlParserInterface;
use App\Services\AccessibilityAnalyzer\Contracts\AccessibilityRuleInterface;

class FormLabelRule implements AccessibilityRuleInterface
{
    public function getName(): string
    {
        return 'Form Label Rule';
    }

    public function getDescription(): string
    {
        return 'Ensures form labels.';
    }

    public function evaluate(HtmlParserInterface $parser): array
    {
        return [];
    }
}
