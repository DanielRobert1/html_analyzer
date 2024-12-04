<?php
namespace App\Contracts;

interface HtmlParserInterface
{
    public function __construct(string $html);
    public function getTags(string $tagName): array;
}
