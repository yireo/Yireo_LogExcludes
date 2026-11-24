<?php
declare(strict_types=1);

namespace Yireo\LogExcludes\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Monolog\Level;

class Config
{
    public const XML_ENABLED = 'yireo_logexcludes/general/enabled';
    public const XML_MIN_LEVEL = 'yireo_logexcludes/general/min_level';
    public const XML_PATTERNS = 'yireo_logexcludes/general/patterns';

    public function __construct(
        private ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_ENABLED);
    }

    public function getMinimumLevel(): int
    {
        $level = (int)$this->scopeConfig->getValue(self::XML_MIN_LEVEL);

        return in_array($level, Level::VALUES, true) ? $level : Level::Error->value;
    }

    public function getPatterns(): array
    {
        $lines = explode("\n", (string)$this->scopeConfig->getValue(self::XML_PATTERNS));

        $patterns = [];
        foreach ($lines as $line) {
            $line = trim($line, " \f\n\r\t\v\x00");
            if ($line === '') {
                continue;
            }

            $patterns[] = $line;
        }

        return $patterns;
    }
}
