<?php
namespace App\Services\AccessibilityAnalyzer\Contracts;

use App\Contracts\HtmlParserInterface;

interface AccessibilityRuleInterface
{
    /**
     * Evaluate the rule against the given HTML parser.
     *
     * @param HtmlParserInterface $parser
     * @return array Contains 'name', 'description', 'count', and 'details'
     */
    public function evaluate(HtmlParserInterface $parser): array;

    /**
     * Get the name of the rule.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get a brief description of the rule.
     *
     * @return string
     */
    public function getDescription(): string;
}
