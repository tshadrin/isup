<?php
declare(strict_types=1);

namespace App\Service;


class VariableFetcher
{
    const VARIABLE_PATTERN = '/\$\[[a-zA-z0-9]+\]/u';
    /** @var string */
    private $text;
    /** @var string[] */
    private $variables;


    public function setText(string $text): void
    {
        $this->text = $text;
    }
    public function getVariables(): array
    {
        $matches = [];
        preg_match_all(self::VARIABLE_PATTERN, $this->text, $matches);
        foreach ($matches as $num => $match) {
            $matches[$num] = preg_replace('/\$\[|\]/', '', $match);
        }
        $this->variables = array_flip($matches[0]);
        return $this->variables;
    }

    public function replaceVariables(array $variables): void
    {
        $this->variables = $variables;
        foreach ($variables as $name => $value) {
            $this->text = preg_replace('/\$\['.$name.'\]/', $value, $this->text);
        }
    }

    public function getText(): string
    {
        return $this->text;
    }
}