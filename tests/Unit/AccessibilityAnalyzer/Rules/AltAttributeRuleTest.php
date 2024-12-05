<?php
use App\Services\AccessibilityAnalyzer\Rules\AltAttributeRule;
use App\Contracts\HtmlParserInterface;
use DOMDocument;

beforeEach(function () {
    $this->parser = mock(HtmlParserInterface::class);  // Mocking the HtmlParserInterface
    $this->rule = new AltAttributeRule();
});

it('returns no issues when there are no <img> tags', function () {
    $this->parser->shouldReceive('getTags')->with('img')->andReturn([]);  // No <img> tags

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(0); // No issues found
    expect($result['details'])->toBeEmpty(); // No details should be returned
});

it('returns an issue for <img> tags missing the alt attribute', function () {
    $html = '<img src="image.jpg">';  // Missing alt attribute
    $dom = new DOMDocument();
    $dom->loadHTML($html);  // Load HTML content
    $imgTag = $dom->getElementsByTagName('img')[0]; // Get the <img> tag

    $this->parser->shouldReceive('getTags')->with('img')->andReturn([$imgTag]); // Mock the parser to return the <img> tag

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(1); // One issue detected
    expect($result['details'][0]['reason'])->toContain('Missing alt attribute');  // Reason for the issue
    expect($result['details'][0]['suggestion'])->toEqual('If the image is meaningful, add an appropriate alt text. If the image is decorative, set role="presentation" or aria-hidden="true".');  // Suggestion for the fix
});

it('returns an issue for <img> tags with an empty alt attribute, not marked as decorative', function () {
    $html = '<img src="image.jpg" alt="">';  // Empty alt attribute
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $imgTag = $dom->getElementsByTagName('img')[0];
    $imgTag->setAttribute('role', 'none');  // Not marked as decorative
    $imgTag->setAttribute('aria-hidden', 'false');  // Not marked as decorative

    $this->parser->shouldReceive('getTags')->with('img')->andReturn([$imgTag]);

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(1); // One issue detected
    expect($result['details'][0]['reason'])->toContain('Missing alt attribute');  // Reason for the issue
    expect($result['details'][0]['suggestion'])->toEqual('If the image is meaningful, add an appropriate alt text. If the image is decorative, set role="presentation" or aria-hidden="true".');   // Suggestion for the fix
});

it('does not return an issue for <img> tags with an empty alt attribute, marked as decorative', function () {
    $html = '<img src="image.jpg" alt="">';  // Empty alt attribute
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $imgTag = $dom->getElementsByTagName('img')[0];
    $imgTag->setAttribute('role', 'presentation');  // Marked as decorative
    $imgTag->setAttribute('aria-hidden', 'true');  // Marked as decorative

    $this->parser->shouldReceive('getTags')->with('img')->andReturn([$imgTag]);

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(0);  // No issue for decorative images
});

it('returns issues for multiple <img> tags, including one with an empty alt attribute', function () {
    $html = '<img src="image1.jpg" alt="Image 1"><img src="image2.jpg" alt="">';  // One valid alt, one empty alt
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $imgTags = iterator_to_array($dom->getElementsByTagName('img'));  // Get all <img> tags

    $this->parser->shouldReceive('getTags')->with('img')->andReturn($imgTags);

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(1); // One issue detected
    expect($result['details'][0]['reason'])->toContain('Missing alt attribute');  // Reason for the issue
    expect($result['details'][0]['suggestion'])->toEqual('If the image is meaningful, add an appropriate alt text. If the image is decorative, set role="presentation" or aria-hidden="true".'); // Suggestion for the fix
});
