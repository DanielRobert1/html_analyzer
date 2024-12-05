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
                    $label = $form->ownerDocument->getElementById($id);
                }

                if (!$label || $label->tagName !== 'label') {
                    $issues[] = [
                        'tag' => $form->ownerDocument->saveHTML($input),
                        'reason' => 'Input field is missing an associated <label> element.',
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
