<?php declare(strict_types=1);

namespace Yireo\LogExcludes\Test\Integration\Config;

use Magento\Config\Model\Config\Structure;
use Magento\Config\Model\Config\Structure\Element\Section;
use Magento\Framework\Acl\AclResource\ProviderInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea adminhtml
 */
final class StructureTest extends TestCase
{
    public function testSectionIsProtectedByOwnAclResource(): void
    {
        $section = Bootstrap::getObjectManager()->get(Structure::class)->getElement('yireo_logexcludes');

        $this->assertInstanceOf(Section::class, $section);
        $this->assertSame('Yireo_LogExcludes::config', $section->getAttribute('resource'));
    }

    public function testAclResourceExists(): void
    {
        $resources = Bootstrap::getObjectManager()->get(ProviderInterface::class)->getAclResources();

        $this->assertContains('Yireo_LogExcludes::config', $this->collectIds($resources));
    }

    private function collectIds(array $resources): array
    {
        $ids = [];
        foreach ($resources as $resource) {
            $ids[] = $resource['id'];
            $ids = array_merge($ids, $this->collectIds($resource['children'] ?? []));
        }

        return $ids;
    }
}
