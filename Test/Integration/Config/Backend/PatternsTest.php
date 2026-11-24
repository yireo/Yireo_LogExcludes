<?php declare(strict_types=1);

namespace Yireo\LogExcludes\Test\Integration\Config\Backend;

use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\LogExcludes\Config\Backend\Patterns;
use Yireo\LogExcludes\Config\Config;

final class PatternsTest extends TestCase
{
    public function testShortPatternIsRejected(): void
    {
        $this->expectException(LocalizedException::class);
        $this->createBackendModel("ValidPattern\nabc")->beforeSave();
    }

    public function testTooManyPatternsAreRejected(): void
    {
        $patterns = [];
        for ($i = 0; $i <= Patterns::MAX_PATTERNS; $i++) {
            $patterns[] = 'Pattern' . $i;
        }

        $this->expectException(LocalizedException::class);
        $this->createBackendModel(implode("\n", $patterns))->beforeSave();
    }

    public function testValidPatternsAreNormalized(): void
    {
        $backendModel = $this->createBackendModel("  Foobar123  \r\n\r\nBarfoo123\nFoobar123\n");
        $backendModel->beforeSave();

        $this->assertSame("Foobar123\nBarfoo123", $backendModel->getValue());
    }

    public function testEmptyValueIsAllowed(): void
    {
        $backendModel = $this->createBackendModel('');
        $backendModel->beforeSave();

        $this->assertSame('', $backendModel->getValue());
    }

    private function createBackendModel(string $value): Patterns
    {
        $backendModel = Bootstrap::getObjectManager()->create(Patterns::class);
        $backendModel->setPath(Config::XML_PATTERNS);
        $backendModel->setScope('default');
        $backendModel->setScopeId(0);
        $backendModel->setValue($value);

        return $backendModel;
    }
}
