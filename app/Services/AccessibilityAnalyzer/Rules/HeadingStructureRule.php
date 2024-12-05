<?php
namespace App\Services\AccessibilityAnalyzer\Rules;

use App\Contracts\HtmlParserInterface;
use App\Services\AccessibilityAnalyzer\Contracts\AccessibilityRuleInterface;
use Illuminate\Support\Facades\Log;

class HeadingStructureRule implements AccessibilityRuleInterface
{
    public function getName(): string
    {
        return 'Headings Rule';
    }

    public function getDescription(): string
    {
        return 'Ensures headings are used in a logical order without skipping levels.';
    }

    public function evaluate(HtmlParserInterface $parser): array
    {
        // Get all heading tags (h1, h2, h3, ..., h6)
        $headings = $parser->getTags('h1, h2, h3, h4, h5, h6');

        $issues = [];
        $previousHeadingLevel = 0; // Keeps track of the last heading level

        if (empty($headings)) {
            // No headings present, so no issues to report
            return [
                'name' => $this->getName(),
                'description' => $this->getDescription(),
                'count' => 0,
                'details' => [],
            ];
        }

        // Check that headings are in correct order
        foreach ($headings as $tag) {
            $headingLevel = (int) substr($tag->nodeName, 1); // Extract the heading level (e.g., h2 -> 2)

            if ($previousHeadingLevel > 0 && $headingLevel > $previousHeadingLevel + 1) {
                // Heading levels are skipped
                $issues[] = [
                    'tag' => $tag->ownerDocument->saveHTML($tag),
                    'reason' => 'Skipped heading levels. Expected heading level: ' . ($previousHeadingLevel + 1),
                    'suggestion' => 'Ensure headings follow a logical order. If the current heading level is skipped, adjust the heading hierarchy.',
                    'severity' => 3,
                ];
            }

            $previousHeadingLevel = $headingLevel;
        }

        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'count' => count($issues),
            'details' => $issues,
        ];
    }
}
