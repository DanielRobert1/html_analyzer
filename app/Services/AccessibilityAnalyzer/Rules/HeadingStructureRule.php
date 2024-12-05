<?php
namespace App\Services\AccessibilityAnalyzer\Rules;

use App\Contracts\HtmlParserInterface;
use App\Services\AccessibilityAnalyzer\Contracts\AccessibilityRuleInterface;

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
        $headings = $parser->getTags('h1, h2, h3, h4, h5, h6'); // Get all heading tags (h1, h2, h3, etc.)
        $issues = [];
        $previousHeadingLevel = 0; // Keeps track of the last heading level

        // Check that headings are in correct order
        foreach ($headings as $tag) {
            $headingLevel = (int) substr($tag->nodeName, 1); // Extract the heading level (e.g., h2 -> 2)

            if ($headingLevel < $previousHeadingLevel) {
                // Heading levels are out of order
                $issues[] = [
                    'tag' => $tag->ownerDocument->saveHTML($tag),
                    'reason' => 'Heading skipped levels. Expected heading level: ' . ($previousHeadingLevel + 1),
                ];
            }

            $previousHeadingLevel = $headingLevel;
        }

        // Check if at least one h1 exists
        if ($previousHeadingLevel === 0) {
            $issues[] = [
                'tag' => '',
                'reason' => 'Missing <h1> tag in the document.',
            ];
        }

        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'count' => count($issues),
            'details' => $issues,
        ];
    }
}
