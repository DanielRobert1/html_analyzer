<?php
namespace App\Services\AccessibilityAnalyzer;

use App\Contracts\HtmlParserInterface;
use App\Services\AccessibilityAnalyzer\Contracts\AccessibilityRuleInterface;
use Illuminate\Support\Facades\Log;

class AccessibilityAnalyzer
{
    protected $parser;
    protected $rules = [];

    public function __construct(HtmlParserInterface $parser, array $rules)
    {
        $this->parser = $parser;
        foreach ($rules as $rule) {
            if (!$rule instanceof AccessibilityRuleInterface) {
                throw new \InvalidArgumentException('All rules must implement AccessibilityRuleInterface.');
            }
            $this->rules[] = $rule;
        }
    }

    public function analyze(string $html): array
    {
        $this->parser->loadHtml($html);
        $issues = [];
        $score = 100;

        foreach ($this->rules as $rule) {
            $result = $rule->evaluate($this->parser);

            if ($result['count'] > 0) {
                $issues[] = $result;

                // Apply severity-based score reduction
                foreach ($result['details'] as $issue) {
                    $severity = $issue['severity'] ?? 5; // Default to 5 if no severity is specified
                    $score -= $severity;
                }
            }
        }

        // Ensure score doesn't go below 0
        return [
            'score' => max($score, 0),
            'issues' => $issues,
        ];
    }
}
