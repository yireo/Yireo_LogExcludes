<?php declare(strict_types=1);

namespace Yireo\LogExcludes\Test\Unit\Util;

use Monolog\Level;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yireo\LogExcludes\Config\Config;
use Yireo\LogExcludes\Util\MessageMatcher;

final class MessageMatcherTest extends TestCase
{
    public function testDisabledNeverMatches(): void
    {
        $matcher = new MessageMatcher($this->createConfig(false, Level::Error->value, ['Foobar']));

        $this->assertFalse($matcher->match(Level::Info->value, 'Foobar happened'));
    }

    public function testMatchesSubstringBelowMinimumLevel(): void
    {
        $matcher = new MessageMatcher($this->createConfig(true, Level::Error->value, ['Foobar']));

        $this->assertTrue($matcher->match(Level::Info->value, 'Some Foobar happened'));
        $this->assertTrue($matcher->match(Level::Warning->value, 'Foobar'));
        $this->assertFalse($matcher->match(Level::Info->value, 'Something else'));
    }

    public function testNeverMatchesAtOrAboveMinimumLevel(): void
    {
        $matcher = new MessageMatcher($this->createConfig(true, Level::Error->value, ['Foobar']));

        $this->assertFalse($matcher->match(Level::Error->value, 'Foobar'));
        $this->assertFalse($matcher->match(Level::Critical->value, 'Foobar'));
    }

    public function testCriticalMatchesWhenMinimumLevelIsEmergency(): void
    {
        $matcher = new MessageMatcher($this->createConfig(true, Level::Emergency->value, ['Foobar']));

        $this->assertTrue($matcher->match(Level::Critical->value, 'Foobar'));
        $this->assertTrue($matcher->match(Level::Alert->value, 'Foobar'));
        $this->assertFalse($matcher->match(Level::Emergency->value, 'Foobar'));
    }

    public function testConfigIsLoadedOncePerRequest(): void
    {
        $config = $this->createConfig(true, Level::Error->value, ['Foobar']);
        $config->expects($this->once())->method('getPatterns');

        $matcher = new MessageMatcher($config);
        $matcher->match(Level::Info->value, 'Foobar');
        $matcher->match(Level::Info->value, 'Foobar');
    }

    public function testResetStateReloadsConfig(): void
    {
        $config = $this->createConfig(true, Level::Error->value, ['Foobar']);
        $config->expects($this->exactly(2))->method('getPatterns');

        $matcher = new MessageMatcher($config);
        $matcher->match(Level::Info->value, 'Foobar');
        $matcher->_resetState();
        $matcher->match(Level::Info->value, 'Foobar');
    }

    public function testNestedCallWhileLoadingConfigDoesNotMatch(): void
    {
        $config = $this->createMock(Config::class);
        $matcher = new MessageMatcher($config);
        $nestedResult = null;
        $config->method('isEnabled')->willReturnCallback(function () use ($matcher, &$nestedResult) {
            $nestedResult = $matcher->match(Level::Info->value, 'Foobar');
            return true;
        });
        $config->method('getMinimumLevel')->willReturn(Level::Error->value);
        $config->method('getPatterns')->willReturn(['Foobar']);

        $this->assertTrue($matcher->match(Level::Info->value, 'Foobar'));
        $this->assertFalse($nestedResult);
    }

    public function testFailingConfigDoesNotMatch(): void
    {
        $config = $this->createMock(Config::class);
        $config->method('isEnabled')->willThrowException(new RuntimeException('Broken'));

        $matcher = new MessageMatcher($config);

        $this->assertFalse($matcher->match(Level::Info->value, 'Foobar'));
    }

    public function testRunUnfilteredDoesNotMatch(): void
    {
        $matcher = new MessageMatcher($this->createConfig(true, Level::Error->value, ['Foobar']));
        $result = null;

        $matcher->runUnfiltered(function () use ($matcher, &$result) {
            $result = $matcher->match(Level::Info->value, 'Foobar');
        });

        $this->assertFalse($result);
        $this->assertTrue($matcher->match(Level::Info->value, 'Foobar'));
    }

    private function createConfig(bool $enabled, int $minimumLevel, array $patterns): Config&MockObject
    {
        $config = $this->createMock(Config::class);
        $config->method('isEnabled')->willReturn($enabled);
        $config->method('getMinimumLevel')->willReturn($minimumLevel);
        $config->method('getPatterns')->willReturn($patterns);

        return $config;
    }
}
