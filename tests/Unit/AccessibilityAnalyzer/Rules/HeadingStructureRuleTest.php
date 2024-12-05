<?php
use App\Services\AccessibilityAnalyzer\Rules\HeadingStructureRule;
use App\Contracts\HtmlParserInterface;
use DOMDocument;

beforeEach(function () {
    $this->parser = mock(HtmlParserInterface::class);
    $this->rule = new HeadingStructureRule();
});

it('returns no issues when there are no heading tags', function () {
    $this->parser->shouldReceive('getTags')->with('h1, h2, h3, h4, h5, h6')->andReturn([]);

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(0);
    expect($result['details'])->toBeEmpty();
});

it('returns an issue for skipped heading levels', function () {
    $html = '<h1>Heading 1</h1><h3>Heading 3</h3>';
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $headings = [];
    foreach (['h1', 'h2', 'h3', 'h4', 'h5', 'h6'] as $tag) {
        $headings = array_merge($headings, iterator_to_array($dom->getElementsByTagName($tag)));
    }

    $this->parser->shouldReceive('getTags')->with('h1, h2, h3, h4, h5, h6')->andReturn($headings);

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(1);
    expect($result['details'][0]['reason'])->toContain('Skipped heading levels');
    expect($result['details'][0]['suggestion'])->toEqual('Ensure headings follow a logical order. If the current heading level is skipped, adjust the heading hierarchy.');
});

it('does not return an issue for headings in a proper order', function () {
    $html = '<h1>Heading 1</h1><h2>Heading 2</h2><h3>Heading 3</h3>';
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $headings = iterator_to_array($dom->getElementsByTagName('h1'));

    $this->parser->shouldReceive('getTags')->with('h1, h2, h3, h4, h5, h6')->andReturn($headings);

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(0);
    expect($result['details'])->toBeEmpty();
});

it('returns no issue when headings are in a proper order with multiple heading tags', function () {
    $html = '<h1>Heading 1</h1><h2>Heading 2</h2><h3>Heading 3</h3><h2>Heading 2.2</h2>';
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $headings = iterator_to_array($dom->getElementsByTagName('h1'));

    $this->parser->shouldReceive('getTags')->with('h1, h2, h3, h4, h5, h6')->andReturn($headings);

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(0);
    expect($result['details'])->toBeEmpty();
});

it('returns multiple issues for skipped heading levels', function () {
    $html = '<h1>Heading 1</h1><h3>Heading 3</h3><h5>Heading 5</h5>';
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $headings = [];
    foreach (['h1', 'h2', 'h3', 'h4', 'h5', 'h6'] as $tag) {
        $headings = array_merge($headings, iterator_to_array($dom->getElementsByTagName($tag)));
    }


    $this->parser->shouldReceive('getTags')->with('h1, h2, h3, h4, h5, h6')->andReturn($headings);

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(2); // Two skipped levels (h1 -> h3 and h3 -> h5)
    expect($result['details'][0]['reason'])->toContain('Skipped heading levels');
    expect($result['details'][0]['suggestion'])->toEqual('Ensure headings follow a logical order. If the current heading level is skipped, adjust the heading hierarchy.');
    expect($result['details'][1]['reason'])->toContain('Skipped heading levels');
    expect($result['details'][1]['suggestion'])->toEqual('Ensure headings follow a logical order. If the current heading level is skipped, adjust the heading hierarchy.');
});
