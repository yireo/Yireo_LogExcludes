<?php
declare(strict_types=1);

namespace Yireo\LogExcludes\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Monolog\Level;

class LogLevel implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $levels = [
            Level::Info,
            Level::Notice,
            Level::Warning,
            Level::Error,
            Level::Critical,
            Level::Alert,
            Level::Emergency,
        ];

        $options = [];
        foreach ($levels as $level) {
            $options[] = ['value' => $level->value, 'label' => $level->getName()];
        }

        return $options;
    }
}
