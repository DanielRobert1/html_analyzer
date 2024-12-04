<?php
namespace App\Services\AccessibilityAnalyzer\Rules;

use App\Contracts\HtmlParserInterface;
use App\Services\Accessibility\Contracts\AccessibilityRuleInterface;

class AltAttributeRule implements AccessibilityRuleInterface
{
    public function getName(): string
    {
        return 'Alt Attribute Rule';
    }

    public function getDescription(): string
    {
        return 'Ensures all <img> elements have appropriate alt attributes for accessibility.';
    }

    public function evaluate(HtmlParserInterface $parser): array
    {
        $imgTags = $parser->getTags('img'); // Get all <img> elements
        $issues = [];

        foreach ($imgTags as $tag) {
            $alt = $tag->getAttribute('alt');
            $role = $tag->getAttribute('role');
            $ariaHidden = $tag->getAttribute('aria-hidden');

            if ($alt === null) {
                // Missing alt attribute
                $issues[] = [
                    'tag' => $tag->ownerDocument->saveHTML($tag),
                    'reason' => 'Missing alt attribute.',
                ];
            } elseif ($alt === '' && $role !== 'presentation' && $ariaHidden !== 'true') {
                // Empty alt attribute without being decorative
                $issues[] = [
                    'tag' => $tag->ownerDocument->saveHTML($tag),
                    'reason' => 'Empty alt attribute without being marked as decorative (e.g., role="presentation" or aria-hidden="true").',
                ];
            }
        }

        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'count' => count($issues),
            'details' => $issues,
        ];
    }
}
