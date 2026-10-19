<?php
declare(strict_types=1);

namespace Yireo\LogExcludes\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    private const XML_ENABLED = 'system/yireo_logexcludes/enabled';
    private const XML_PATTERNS = 'system/yireo_logexcludes/patterns';

    public function __construct(
        private ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_ENABLED);
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
