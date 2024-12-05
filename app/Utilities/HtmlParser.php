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

    public function getTags(string $tagNames): array
    {
        $tags = [];
        $tagsList = explode(', ', $tagNames); // Split the tag names into an array

        foreach ($tagsList as $tagName) {
            $tags = array_merge($tags, iterator_to_array($this->dom->getElementsByTagName($tagName)));
        }

        return $tags;
    }
}
