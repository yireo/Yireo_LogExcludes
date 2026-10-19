<?php declare(strict_types=1);

namespace Yireo\LogExcludes\Util;

use Throwable;
use Yireo\LogExcludes\Config\Config;

class MessageMatcher
{
    public function __construct(
        private Config $config
    ) {
    }

    public function match(string $message): bool
    {
        try {
            if (false === $this->config->isEnabled()) {
                return false;
            }

            $patterns = $this->config->getPatterns();
        } catch (Throwable) {
            return false;
        }

        foreach ($patterns as $pattern) {
            if (str_contains($message, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
