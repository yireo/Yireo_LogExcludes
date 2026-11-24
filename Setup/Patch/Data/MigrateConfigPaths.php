<?php declare(strict_types=1);

namespace Yireo\LogExcludes\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Yireo\LogExcludes\Config\Config;

class MigrateConfigPaths implements DataPatchInterface
{
    private const PATHS = [
        'system/yireo_logexcludes/enabled' => Config::XML_ENABLED,
        'system/yireo_logexcludes/patterns' => Config::XML_PATTERNS,
    ];

    public function __construct(
        private ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): self
    {
        $connection = $this->moduleDataSetup->getConnection();
        $table = $this->moduleDataSetup->getTable('core_config_data');

        foreach (self::PATHS as $oldPath => $newPath) {
            $select = $connection->select()
                ->from($table, ['scope', 'scope_id', 'value'])
                ->where('path = ?', $oldPath);

            foreach ($connection->fetchAll($select) as $row) {
                $connection->insertOnDuplicate(
                    $table,
                    [
                        'scope' => $row['scope'],
                        'scope_id' => $row['scope_id'],
                        'path' => $newPath,
                        'value' => $row['value'],
                    ],
                    ['value']
                );
            }

            $connection->delete($table, ['path = ?' => $oldPath]);
        }

        return $this;
    }
}
