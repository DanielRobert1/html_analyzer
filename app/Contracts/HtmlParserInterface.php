<?php
namespace App\Contracts;

interface HtmlParserInterface
{
    public function loadHtml(string $html): void;
    public function getTags(string $tagName): array;
}
