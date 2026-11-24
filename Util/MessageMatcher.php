<?php declare(strict_types=1);

namespace Yireo\LogExcludes\Util;

use Magento\Framework\ObjectManager\ResetAfterRequestInterface;
use Throwable;
use Yireo\LogExcludes\Config\Config;

class MessageMatcher implements ResetAfterRequestInterface
{
    private bool $bypass = false;
    private ?array $patterns = null;
    private int $minimumLevel = 0;

    public function __construct(
        private Config $config
    ) {
    }

    public function match(int $level, string $message): bool
    {
        if ($this->bypass) {
            return false;
        }

        if (!$this->load()) {
            return false;
        }

        if ($level >= $this->minimumLevel) {
            return false;
        }

        foreach ($this->patterns as $pattern) {
            if (str_contains($message, $pattern)) {
                return true;
            }
        }

        return false;
    }

    public function runUnfiltered(callable $callback): void
    {
        $previous = $this->bypass;
        $this->bypass = true;

        try {
            $callback();
        } finally {
            $this->bypass = $previous;
        }
    }

    public function _resetState(): void
    {
        $this->patterns = null;
        $this->minimumLevel = 0;
    }

    private function load(): bool
    {
        if ($this->patterns !== null) {
            return true;
        }

        $this->bypass = true;

        try {
            if (false === $this->config->isEnabled()) {
                $this->minimumLevel = 0;
                $this->patterns = [];
                return true;
            }

            $this->minimumLevel = $this->config->getMinimumLevel();
            $this->patterns = $this->config->getPatterns();
            return true;
        } catch (Throwable) {
            $this->patterns = null;
            return false;
        } finally {
            $this->bypass = false;
        }
    }
}
