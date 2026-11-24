<?php declare(strict_types=1);

namespace Yireo\LogExcludes\Test\Unit\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Monolog\Level;
use PHPUnit\Framework\TestCase;
use Yireo\LogExcludes\Config\Config;

final class ConfigTest extends TestCase
{
    public function testGetPatternsSkipsEmptyLines(): void
    {
        $config = $this->createConfig([Config::XML_PATTERNS => "Foobar\r\n\n  Barfoo  \n"]);

        $this->assertSame(['Foobar', 'Barfoo'], $config->getPatterns());
    }

    public function testGetMinimumLevelFallsBackToError(): void
    {
        $this->assertSame(Level::Error->value, $this->createConfig([Config::XML_MIN_LEVEL => null])->getMinimumLevel());
        $this->assertSame(Level::Error->value, $this->createConfig([Config::XML_MIN_LEVEL => '999'])->getMinimumLevel());
        $this->assertSame(Level::Critical->value, $this->createConfig([Config::XML_MIN_LEVEL => '500'])->getMinimumLevel());
    }

    private function createConfig(array $values): Config
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturnCallback(fn (string $path) => $values[$path] ?? null);

        return new Config($scopeConfig);
    }
}
