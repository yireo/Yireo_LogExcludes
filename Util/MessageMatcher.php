<?php declare(strict_types=1);

namespace Yireo\LogExcludes\Util;

use Yireo\LogExcludes\Config\Config;

class MessageMatcher
{
    public function __construct(
        private Config $config
    ) {
    }

    public function match(string $message): bool
    {
        $patterns = $this->config->getPatterns();
        foreach ($patterns as $pattern) {
            if (str_contains($message, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
