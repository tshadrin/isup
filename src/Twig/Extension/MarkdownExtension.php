<?php
declare(strict_types=1);


namespace App\Twig\Extension;


use cebe\markdown\MarkdownExtra;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension
{
    /**
     * @var MarkdownExtra
     */
    private $markdownExtra;

    public function __construct(MarkdownExtra $markdownExtra)
    {
        $this->markdownExtra = $markdownExtra;
    }

    public function getFilters(): array
    {
        return [new TwigFilter('markdown', [$this, 'markdown'], ['is_safe' => ['html']]),];
    }

    public function markdown(?string $text): string
    {
        return $this->markdownExtra->parse($text);
    }
}