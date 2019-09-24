<?php
declare(strict_types=1);

namespace App\Tests\Unit\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VariableFetcherTest extends KernelTestCase
{
    public function testHasVariables(): void
    {
        self::bootKernel();
        $container = self::$container;
        $variableFetcher = $container->get("App\Service\VariableFetcher");
        self::assertFalse($variableFetcher->hasVariables());
        $variableFetcher->setText("Some text without variables $[]");
        self::assertFalse($variableFetcher->hasVariables());
        $variableFetcher->setText("Some text with variables $[test]");
        self::assertTrue($variableFetcher->hasVariables());
    }

    public function testReplaceVariables(): void
    {
        self::bootKernel();
        $container = self::$container;
        $variableFetcher = $container->get("App\Service\VariableFetcher");
        $variableFetcher->setText("Some text with variables $[test] $[test2]");
        $variableFetcher->replaceVariables(["test" => "test value", "test2" => "blog"]);
        self::assertEquals($variableFetcher->getText(), "Some text with variables test value blog");
    }

    public function testReplaceVariablesFail(): void
    {
        self::bootKernel();
        $container = self::$container;
        $variableFetcher = $container->get("App\Service\VariableFetcher");
        $variableFetcher->setText("Some text with variables $[test] $[test2]");
        self::expectExceptionMessage("Some variables not set");
        $variableFetcher->replaceVariables(["test" => "test value"]);
    }
}