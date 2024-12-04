<?php
namespace App\Utilities;

use App\Contracts\HtmlParserInterface;
use DOMDocument;

class HtmlParser implements HtmlParserInterface
{
    protected $dom;

    public function __construct(string $html)
    {
        $this->dom = new DOMDocument();
        @$this->dom->loadHTML($html); // Suppress warnings for malformed HTML
    }

    public function getTags(string $tagName): array
    {
        return iterator_to_array($this->dom->getElementsByTagName($tagName));
    }
}
