<?php
namespace App\Services\AccessibilityAnalyzer\Rules;

use App\Contracts\HtmlParserInterface;
use App\Services\AccessibilityAnalyzer\Contracts\AccessibilityRuleInterface;
use Illuminate\Support\Facades\Log;

class TabNavigationRule implements AccessibilityRuleInterface
{
    public function getName(): string
    {
        return 'Tab Navigation Rule';
    }

    public function getDescription(): string
    {
        return 'Ensures all actionable elements can be navigated using the Tab key.';
    }


    public function evaluate(HtmlParserInterface $parser): array
    {
        // Get actionable elements
        $actionableElements = $parser->getTags('a, button, input, select, textarea, [tabindex]');

        $issues = [];

        foreach ($actionableElements as $element) {
            $tabIndex = $element->getAttribute('tabindex');

            // Check if the element is tabbable
            if ($tabIndex !== null && (int)$tabIndex < 0) {
                $issues[] = [
                    'tag' => $element->ownerDocument->saveHTML($element),
                    'reason' => 'Element has a tabindex less than 0, making it inaccessible via Tab navigation.',
                    'severity' => 10,
                ];
            }

            // Check if the element is not focusable
            if (!$element->hasAttribute('tabindex') && !$this->isFocusable($element)) {
                $issues[] = [
                    'tag' => $element->ownerDocument->saveHTML($element),
                    'reason' => 'Element is not tabbable and lacks an explicit tabindex.',
                    'severity' => 10,
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

    private function isFocusable($element): bool
    {
        $tagName = $element->nodeName;

        // Default focusable elements
        $focusableTags = ['a', 'button', 'input', 'select', 'textarea'];
        if (in_array($tagName, $focusableTags)) {
            return true;
        }

        // Check if the element has a valid href (for <a> tags)
        if ($tagName === 'a' && $element->hasAttribute('href')) {
            return true;
        }

        return false;
    }
}
