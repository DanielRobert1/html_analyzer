<?php
namespace App\Services\AccessibilityAnalyzer;

use App\Contracts\HtmlParserInterface;
use App\Services\Accessibility\Contracts\AccessibilityRuleInterface;

class AccessibilityAnalyzer
{
    protected $parser;
    protected $rules = [];

    public function __construct(HtmlParserInterface $parser, array $rules)
    {
        $this->parser = $parser;
        // Ensure all rules implement AccessibilityRuleInterface
        foreach ($rules as $rule) {
            if (!$rule instanceof AccessibilityRuleInterface) {
                throw new \InvalidArgumentException('All rules must implement AccessibilityRuleInterface.');
            }
            $this->rules[] = $rule;
        }
    }

    public function analyze(string $html): array
    {
        $this->parser->__construct($html); // Reinitialize the parser with the new HTML
        $issues = [];
        $score = 100;

        foreach ($this->rules as $rule) {
            $result = $rule->evaluate($this->parser);
            if ($result['count'] > 0) {
                $issues[] = $result;
                $score -= $result['count'] * 5;
            }
        }

        return [
            'score' => max($score, 0),
            'issues' => $issues,
        ];
    }
}
