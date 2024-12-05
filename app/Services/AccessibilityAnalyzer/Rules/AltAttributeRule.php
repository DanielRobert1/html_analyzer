<?php
namespace App\Services\AccessibilityAnalyzer\Rules;

use App\Contracts\HtmlParserInterface;
use App\Services\AccessibilityAnalyzer\Contracts\AccessibilityRuleInterface;
use Illuminate\Support\Facades\Log;

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


            $alt = $tag->getAttribute('alt');
            $role = $tag->getAttribute('role');
            $ariaHidden = $tag->getAttribute('aria-hidden');

            // Check for missing or empty alt attribute
            if ($alt === null || $alt === '') {
                if ($role !== 'presentation' && $ariaHidden !== 'true') {
                    // Missing alt attribute or empty alt attribute without being marked as decorative
                    $issues[] = [
                        'tag' => $tag->ownerDocument->saveHTML($tag),
                        'reason' => 'Missing alt attribute.',
                        'suggestion' => 'If the image is meaningful, add an appropriate alt text. If the image is decorative, set role="presentation" or aria-hidden="true".',
                        'severity' => 5,
                    ];
                }
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
