<?php
use App\Services\AccessibilityAnalyzer\Rules\FormLabelRule;
use App\Contracts\HtmlParserInterface;
use DOMDocument;

beforeEach(function () {
    $this->parser = mock(HtmlParserInterface::class);
    $this->rule = new FormLabelRule();
});

it('returns no issues when there are no forms in the document', function () {
    $html = '<div>No forms here</div>'; // No form elements in the HTML

    $dom = new DOMDocument();
    $dom->loadHTML($html);

    $this->parser->shouldReceive('getTags')->with('form')->andReturn([]);

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(0);
    expect($result['details'])->toBeEmpty();
});

it('returns an issue when an input field is missing a label', function () {
    $html = '<form><input type="text" id="username"></form>'; // Missing label for the input field

    $dom = new DOMDocument();
    $dom->loadHTML($html);

    $form = $dom->getElementsByTagName('form')[0];
    $inputs = iterator_to_array($form->getElementsByTagName('input'));

    $this->parser->shouldReceive('getTags')->with('form')->andReturn([$form]);

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(1); // One missing label issue
    expect($result['details'][0]['reason'])->toContain('Input field is missing an associated <label> element.');
    expect($result['details'][0]['tag'])->toContain('<input type="text" id="username">');
});

it('returns no issues when an input field has a corresponding label', function () {
    $html = '<form><input type="text" id="username"><label for="username">Username</label></form>'; // Input field with label

    $dom = new DOMDocument();
    $dom->loadHTML($html);

    $form = $dom->getElementsByTagName('form')[0];
    $inputs = iterator_to_array($form->getElementsByTagName('input'));

    $this->parser->shouldReceive('getTags')->with('form')->andReturn([$form]);

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(0); // No issues
    expect($result['details'])->toBeEmpty();
});

it('ignores non-visible input types (hidden, submit, button, reset)', function () {
    $html = '
        <form>
            <input type="hidden" id="hiddenInput">
            <input type="submit" id="submitButton">
            <input type="button" id="buttonInput">
            <input type="reset" id="resetButton">
        </form>'; // All these inputs should be ignored by the rule

    $dom = new DOMDocument();
    $dom->loadHTML($html);

    $form = $dom->getElementsByTagName('form')[0];
    $inputs = iterator_to_array($form->getElementsByTagName('input'));

    $this->parser->shouldReceive('getTags')->with('form')->andReturn([$form]);

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(0); // No issues because these input types are ignored
    expect($result['details'])->toBeEmpty();
});

it('returns issues for multiple missing labels in the same form', function () {
    $html = '
        <form>
            <input type="text" id="username">
            <input type="email" id="email">
        </form>'; // Both inputs are missing labels

    $dom = new DOMDocument();
    $dom->loadHTML($html);

    $form = $dom->getElementsByTagName('form')[0];
    $inputs = iterator_to_array($form->getElementsByTagName('input'));

    $this->parser->shouldReceive('getTags')->with('form')->andReturn([$form]);

    $result = $this->rule->evaluate($this->parser);

    expect($result['count'])->toEqual(2); // Two missing labels
    expect($result['details'][0]['reason'])->toContain('Input field is missing an associated <label> element.');
    expect($result['details'][1]['reason'])->toContain('Input field is missing an associated <label> element.');
});
