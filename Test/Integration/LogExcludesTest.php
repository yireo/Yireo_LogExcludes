<?php declare(strict_types=1);

namespace Yireo\LogExcludes\Test\Integration;

use Magento\Framework\Logger\Monolog;
use Magento\TestFramework\Helper\Bootstrap;
use Monolog\Handler\TestHandler;
use PHPUnit\Framework\TestCase;
use Yireo\LogExcludes\Util\MessageMatcher;

final class LogExcludesTest extends TestCase
{
    private TestHandler $handler;
    private Monolog $logger;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(MessageMatcher::class)->_resetState();
        $this->handler = new TestHandler();
        $this->logger = $objectManager->create(Monolog::class, ['name' => 'test', 'handlers' => [$this->handler]]);
    }

    protected function tearDown(): void
    {
        Bootstrap::getObjectManager()->get(MessageMatcher::class)->_resetState();
    }

    /**
     * @magentoConfigFixture default/yireo_logexcludes/general/enabled 0
     * @magentoConfigFixture default/yireo_logexcludes/general/patterns LogExcludesTestPattern
     */
    public function testNothingIsExcludedWhenDisabled(): void
    {
        $this->logger->info('Message with LogExcludesTestPattern');

        $this->assertTrue($this->handler->hasInfoThatContains('LogExcludesTestPattern'));
    }

    /**
     * @magentoConfigFixture default/yireo_logexcludes/general/enabled 1
     * @magentoConfigFixture default/yireo_logexcludes/general/min_level 400
     * @magentoConfigFixture default/yireo_logexcludes/general/patterns LogExcludesTestPattern
     */
    public function testMatchingRecordBelowMinimumLevelIsExcluded(): void
    {
        $this->logger->info('Message with LogExcludesTestPattern');
        $this->logger->warning('Message with LogExcludesTestPattern');
        $this->logger->info('Other message');

        $this->assertFalse($this->handler->hasInfoThatContains('LogExcludesTestPattern'));
        $this->assertFalse($this->handler->hasWarningThatContains('LogExcludesTestPattern'));
        $this->assertTrue($this->handler->hasInfoThatContains('Other message'));
    }

    /**
     * @magentoConfigFixture default/yireo_logexcludes/general/enabled 1
     * @magentoConfigFixture default/yireo_logexcludes/general/min_level 400
     * @magentoConfigFixture default/yireo_logexcludes/general/patterns LogExcludesTestPattern
     */
    public function testMatchingCriticalRecordIsWrittenByDefault(): void
    {
        $this->logger->critical('Message with LogExcludesTestPattern');

        $this->assertTrue($this->handler->hasCriticalThatContains('LogExcludesTestPattern'));
    }

    /**
     * @magentoConfigFixture default/yireo_logexcludes/general/enabled 1
     * @magentoConfigFixture default/yireo_logexcludes/general/min_level 600
     * @magentoConfigFixture default/yireo_logexcludes/general/patterns LogExcludesTestPattern
     */
    public function testEmergencyRecordIsNeverExcluded(): void
    {
        $this->logger->critical('Message with LogExcludesTestPattern');
        $this->logger->emergency('Message with LogExcludesTestPattern');

        $this->assertFalse($this->handler->hasCriticalThatContains('LogExcludesTestPattern'));
        $this->assertTrue($this->handler->hasEmergencyThatContains('LogExcludesTestPattern'));
    }
}
