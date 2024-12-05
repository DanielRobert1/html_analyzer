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
        // Get all actionable elements
        $actionableElements = $this->getActionableElements($parser);

        $issues = [];

        foreach ($actionableElements as $element) {
            $tabIndex = $element->getAttribute('tabindex');

            // Check if the element is tabbable and tabindex < 0
            if ($tabIndex !== null && (int)$tabIndex < 0) {
                $issues[] = [
                    'tag' => $element->ownerDocument->saveHTML($element),
                    'reason' => 'Element has a tabindex less than 0, making it inaccessible via Tab navigation.',
                    'suggestion' => 'Consider setting tabindex to a value greater than or equal to 0 for accessibility.',
                    'severity' => 10,
                ];
            }

            // Check if the element is not focusable and lacks a tabindex
            if (!$element->hasAttribute('tabindex') && !$this->isFocusable($element)) {
                $issues[] = [
                    'tag' => $element->ownerDocument->saveHTML($element),
                    'reason' => 'Element is not tabbable and lacks an explicit tabindex.',
                    'suggestion' => 'Ensure the element is focusable, or add a tabindex attribute to make it tabbable.',
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

    /**
     * Get all actionable elements (a, button, input, select, textarea, and elements with [tabindex] attribute).
     *
     * @param HtmlParserInterface $parser
     * @return array
     */
    private function getActionableElements(HtmlParserInterface $parser): array
    {
        // Get all elements with specific tags
        $actionableElements = [];
        $elements = $parser->getTags('*'); // Get all elements

        foreach ($elements as $element) {
            // Check if the element is actionable (has the tabindex attribute or is focusable)
            if ($element->hasAttribute('tabindex') || $this->isFocusable($element)) {
                $actionableElements[] = $element;
            }
        }

        return $actionableElements;
    }

    private function isFocusable($element): bool
    {
        $tagName = $element->nodeName;

         // Check if the element is an <a> tag and has a valid href attribute
        if ($tagName === 'a' && $element->hasAttribute('href')) {
            return true; // <a> tag is focusable if it has an href attribute
        }


        // Default focusable elements
        $focusableTags = ['button', 'input', 'select', 'textarea'];
        if (in_array($tagName, $focusableTags)) {
            return true;
        }

        return false;
    }
}
