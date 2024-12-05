<?php
namespace App\Utilities;

use App\Contracts\HtmlParserInterface;
use DOMDocument;

class HtmlParser implements HtmlParserInterface
{
    protected DOMDocument $dom;

    public function __construct()
    {
        $this->dom = new DOMDocument();
    }


    public function loadHtml(string $html): void
    {
        @$this->dom->loadHTML($html); // Suppress warnings for malformed HTML
    }

    public function getTags(string $tagName): array
    {
        return iterator_to_array($this->dom->getElementsByTagName($tagName));
    }
}
