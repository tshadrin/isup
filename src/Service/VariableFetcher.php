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

    public function __construct()
    {
        $this->text = "";
        $this->variables = [];
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function replaceVariables(array $variables): void
    {
        if (!$this->hasVariables()) {
            return;
        }

        if (count(array_intersect_key($this->variables, $variables)) !== count($this->variables)) {
            throw new \InvalidArgumentException("Some variables not set");
        }

        $this->variables = array_merge($this->variables, array_intersect_key($variables, $this->variables));
        foreach ($this->variables as $name => $value) {
            $this->text = preg_replace('/\$\['.$name.'\]/', $value, $this->text);
        }
    }

    public function hasVariables(): bool
    {
        return !empty($this->getVariables());
    }

    public function getVariables(): array
    {
        if (!is_null($this->variables)) {
            $matches = [];
            preg_match_all(self::VARIABLE_PATTERN, $this->text, $matches);
            foreach ($matches as $num => $match) {
                $matches[$num] = preg_replace('/\$\[|\]/', '', $match);
            }
            $this->variables = array_flip($matches[0]);
        }
        return $this->variables;
    }

    public function hasVariable(string $name): bool
    {
        return array_key_exists($name, $this->variables);
    }
    public function getVariable(string $name): string
    {
        if (!$this->hasVariable($name)) {
            throw new \DomainException("Variable not found");
        }
        return $this->variables[$name];
    }
}