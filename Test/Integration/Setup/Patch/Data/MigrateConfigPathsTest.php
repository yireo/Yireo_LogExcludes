<?php declare(strict_types=1);

namespace Yireo\LogExcludes\Test\Integration\Setup\Patch\Data;

use Magento\Framework\App\ResourceConnection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\LogExcludes\Config\Config;
use Yireo\LogExcludes\Setup\Patch\Data\MigrateConfigPaths;

/**
 * @magentoDbIsolation enabled
 */
final class MigrateConfigPathsTest extends TestCase
{
    public function testOldPathsAreMovedToNewPaths(): void
    {
        $resource = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $connection = $resource->getConnection();
        $table = $resource->getTableName('core_config_data');
        $connection->delete($table, ['path IN (?)' => [Config::XML_ENABLED, Config::XML_PATTERNS]]);
        $connection->insertMultiple($table, [
            ['scope' => 'default', 'scope_id' => 0, 'path' => 'system/yireo_logexcludes/enabled', 'value' => '1'],
            ['scope' => 'default', 'scope_id' => 0, 'path' => 'system/yireo_logexcludes/patterns', 'value' => 'Foobar123'],
        ]);

        Bootstrap::getObjectManager()->create(MigrateConfigPaths::class)->apply();

        $select = $connection->select()->from($table, ['path', 'value'])->where('path IN (?)', [
            'system/yireo_logexcludes/enabled',
            'system/yireo_logexcludes/patterns',
            Config::XML_ENABLED,
            Config::XML_PATTERNS,
        ]);
        $this->assertSame(
            [Config::XML_ENABLED => '1', Config::XML_PATTERNS => 'Foobar123'],
            $connection->fetchPairs($select->order('path'))
        );
    }
}
