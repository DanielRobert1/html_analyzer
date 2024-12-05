<?php
use App\Contracts\HtmlParserInterface;
use App\Services\AccessibilityAnalyzer\Rules\HeadingStructureRule;

it('detects missing h1 tag', function () {
    $html = '<h2>Heading 2</h2>'; // Missing <h1>

    /** @var HtmlParserInterface|Mockery\MockInterface $mockParser */
    $mockParser = mock(HtmlParserInterface::class);
    $mockParser->shouldReceive('getTags')->andReturn([
        createMockTag('<h2>Heading 2</h2>', 'Heading 2')
    ]);

    $rule = new HeadingStructureRule();
    $result = $rule->evaluate($mockParser);

    expect($result['count'])->toEqual(1);
    expect($result['details'][0]['reason'])->toEqual('Missing <h1> tag in the document.');
});

it('detects skipped heading levels', function () {
    $html = '<h1>Main Heading</h1><h3>Subheading</h3>'; // Skipped heading level (H1 -> H3)

    /** @var HtmlParserInterface|Mockery\MockInterface $mockParser */
    $mockParser = mock(HtmlParserInterface::class);
    $mockParser->shouldReceive('getTags')->andReturn([
        createMockTag('<h1>Main Heading</h1>', 'Main Heading'),
        createMockTag('<h3>Subheading</h3>', 'Subheading')
    ]);

    $rule = new HeadingStructureRule();
    $result = $rule->evaluate($mockParser);

    expect($result['count'])->toEqual(1);
    expect($result['details'][0]['reason'])->toEqual('Heading skipped levels. Expected heading level: 2');
});

it('detects no issues for valid headings', function () {
    $html = '<h1>Main Heading</h1><h2>Subheading</h2><h3>Sub-subheading</h3>'; // Valid

    /** @var HtmlParserInterface|Mockery\MockInterface $mockParser */
    $mockParser = mock(HtmlParserInterface::class);
    $mockParser->shouldReceive('getTags')->andReturn([
        createMockTag('<h1>Main Heading</h1>', 'Main Heading'),
        createMockTag('<h2>Subheading</h2>', 'Subheading'),
        createMockTag('<h3>Sub-subheading</h3>', 'Sub-subheading')
    ]);

    $rule = new HeadingStructureRule();
    $result = $rule->evaluate($mockParser);

    expect($result['count'])->toEqual(0);
    expect($result['details'])->toBeEmpty();
});

function createMockTag(string $html, string $content): \DOMElement
{
    $dom = new \DOMDocument();
    @$dom->loadHTML($html);

    $tag = $dom->getElementsByTagName('h1')->item(0) ?? $dom->getElementsByTagName('h2')->item(0);
    if ($tag) {
        $tag->nodeValue = $content;
    }

    return $tag;
}
