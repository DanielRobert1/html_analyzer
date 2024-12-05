<?php

namespace App\Http\Controllers\Api;

use App\Contracts\HtmlParserInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnalyzeRequest;
use App\Services\AccessibilityAnalyzer\AccessibilityAnalyzer;
use App\Services\AccessibilityAnalyzer\Rules\AltAttributeRule;
use App\Services\AccessibilityAnalyzer\Rules\FormLabelRule;
use App\Services\AccessibilityAnalyzer\Rules\HeadingStructureRule;
use App\Services\AccessibilityAnalyzer\Rules\TabNavigationRule;
use App\Traits\ApiResponse;

class AnalyzerController extends Controller
{
    use ApiResponse;

    public function analyze(AnalyzeRequest $request, HtmlParserInterface $parser)
    {
        try {
            $rules = [
                new AltAttributeRule(),
                new HeadingStructureRule(),
                new FormLabelRule(),
                new TabNavigationRule(),
            ];
            $file = $request->file('file')->get();
            $analyzer = new AccessibilityAnalyzer($parser, $rules);
            $results = $analyzer->analyze($file);
            return $this->sendResponse($results, "File analyzed successfully!");
        } catch (\Throwable $th) {
            report($th);
            return $this->sendError("Unable to process file!");
        }
    }
}
