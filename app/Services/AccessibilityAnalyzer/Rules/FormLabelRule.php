<?php
namespace App\Services\AccessibilityAnalyzer\Rules;

use App\Contracts\HtmlParserInterface;

class FormLabelRule
{
    public function evaluate(HtmlParserInterface $parser): array
    {
        $images = $parser->getTags('img');
        $missingAltCount = 0;

        foreach ($images as $img) {
            if (!$img->hasAttribute('alt') || empty($img->getAttribute('alt'))) {
                $missingAltCount++;
            }
        }

        return [
            'type' => 'Missing alt attribute',
            'count' => $missingAltCount,
            'suggestion' => 'Add alt attributes to all <img> tags.',
        ];
    }
}
