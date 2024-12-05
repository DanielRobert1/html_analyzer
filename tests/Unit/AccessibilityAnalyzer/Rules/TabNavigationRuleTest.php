<?php
use App\Contracts\HtmlParserInterface;
use App\Services\AccessibilityAnalyzer\Rules\TabNavigationRule;
use DOMDocument;

beforeEach(function () {
    $this->parser = mock(HtmlParserInterface::class);
    $this->rule = new TabNavigationRule();
});

it('returns no issues for focusable elements', function () {
    $html = '
        <form>
            <a href="https://example.com">Link</a>
            <button>Button</button>
            <input type="text" />
            <select>
                <option>Option 1</option>
            </select>
            <textarea></textarea>
        </form>
    ';

    $dom = new DOMDocument();
    @$dom->loadHTML($html); // Suppress warnings for invalid HTML in testing

    // Mocking the parser
    $this->parser->shouldReceive('getTags')->andReturn(iterator_to_array($dom->getElementsByTagName('*')));

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(0); // No issues for focusable elements
    expect($result['details'])->toBeEmpty(); // No detailed issues
});

it('returns an issue for tabindex less than zero', function () {
    $html = '<form><button tabindex="-1">Button</button></form>';

    $dom = new DOMDocument();
    @$dom->loadHTML($html); // Suppress warnings for invalid HTML in testing

    // Mocking the parser
    $this->parser->shouldReceive('getTags')->andReturn(iterator_to_array($dom->getElementsByTagName('*')));

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(1); // One issue found
    expect($result['details'][0]['reason'])->toContain('tabindex less than 0');
});

it('does not return an issue for an a tag with hyperlink', function () {
    $html = '<form><a href="https://example.com">Valid link</a></form>';

    $dom = new DOMDocument();
    @$dom->loadHTML($html); // Suppress warnings for invalid HTML in testing

    // Mocking the parser
    $this->parser->shouldReceive('getTags')->andReturn(iterator_to_array($dom->getElementsByTagName('*')));

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(0); // No issues for valid link
    expect($result['details'])->toBeEmpty(); // No detailed issues
});

it('does not return an issue for button without tabindex', function () {
    $html = '<form><button>Valid Button</button></form>';

    $dom = new DOMDocument();
    @$dom->loadHTML($html); // Suppress warnings for invalid HTML in testing

    // Mocking the parser
    $this->parser->shouldReceive('getTags')->andReturn(iterator_to_array($dom->getElementsByTagName('*')));

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(0); // No issues for button
    expect($result['details'])->toBeEmpty(); // No detailed issues
});

it('returns issues when actionable elements are not tabbable or missing tabindex', function () {
    // Example HTML with problematic elements
    $html = '
        <button>Button</button>
        <input type="text">
        <textarea></textarea>
        <a href="#">Link</a>
        <input type="text" tabindex="-1">
        <input type="text">
    ';

    $dom = new DOMDocument();
    @$dom->loadHTML($html); // Suppress warnings for invalid HTML in testing

    // Mocking the parser
    $this->parser->shouldReceive('getTags')->andReturn(iterator_to_array($dom->getElementsByTagName('*')));

    $result = $this->rule->evaluate($this->parser);

    // Assertions for issues
    expect($result['count'])->toEqual(1);  // Expect 1 issue (the element with tabindex=-1)
    expect($result['details'][0]['reason'])->toContain('tabindex less than 0');
});

it('returns no issues when all actionable elements are tabbable', function () {
    // Example HTML with correct tabbable elements
    $html = '
        <button>Button</button>
        <input type="text" tabindex="0">
        <textarea tabindex="0"></textarea>
        <a href="#">Link</a>
    ';

    $dom = new DOMDocument();
    @$dom->loadHTML($html);  // Suppress warnings from invalid HTML structure

    // Mocking the parser
    $this->parser->shouldReceive('getTags')->andReturn(iterator_to_array($dom->getElementsByTagName('*')));

    $result = $this->rule->evaluate($this->parser);

    // Assertions for no issues
    expect($result['count'])->toEqual(0);  // No issues
    expect($result['details'])->toBeEmpty();
});
