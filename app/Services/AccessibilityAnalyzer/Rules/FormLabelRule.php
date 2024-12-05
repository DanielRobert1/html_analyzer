<?php

namespace App\Services\AccessibilityAnalyzer\Rules;

use App\Contracts\HtmlParserInterface;
use App\Services\AccessibilityAnalyzer\Contracts\AccessibilityRuleInterface;
use Illuminate\Support\Facades\Log;

class FormLabelRule implements AccessibilityRuleInterface
{
    public function getName(): string
    {
        return 'Form Label Rule';
    }

    public function getDescription(): string
    {
        return 'Ensures every input field within a form has an associated label for accessibility.';
    }

    public function evaluate(HtmlParserInterface $parser): array
    {
        $forms = $parser->getTags('form'); // Get all forms
        $issues = [];

        foreach ($forms as $form) {
            $inputs = $form->getElementsByTagName('input');
            foreach ($inputs as $input) {
                $type = $input->getAttribute('type');
                if (in_array($type, ['hidden', 'submit', 'button', 'reset'])) {
                    continue; // Skip non-visible or button inputs
                }

                $id = $input->getAttribute('id');
                $label = null;

                if ($id) {
                    // Use DOMXPath to find the label by its "for" attribute that matches the input's "id"
                    $xpath = new \DOMXPath($form->ownerDocument);
                    $label = $xpath->query("//label[@for='$id']")->item(0); // Find label by "for" attribute
                }

                if (!$label) {
                    // No label associated with the input field
                    $issues[] = [
                        'tag' => $form->ownerDocument->saveHTML($input),
                        'reason' => 'Input field is missing an associated <label> element.',
                        'suggestion' => 'Ensure that every input field has an associated <label> element, either by using the "for" attribute with the input\'s ID or by wrapping the input inside the <label> element.',
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
