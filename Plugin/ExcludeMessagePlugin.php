<?php declare(strict_types=1);

namespace Yireo\LogExcludes\Plugin;

use DateTimeImmutable;
use Monolog\Level;
use Monolog\Logger;
use Yireo\LogExcludes\Util\MessageMatcher;

class ExcludeMessagePlugin
{
    public function __construct(
        private MessageMatcher $messageMatcher,
    ) {
    }

    public function aroundAddRecord(
        Logger $subject,
        callable $proceed,
        int|Level $level,
        string $message,
        array $context = [],
        ?DateTimeImmutable $datetime = null
    ): bool {
        $levelValue = $level instanceof Level ? $level->value : $level;
        if ($this->messageMatcher->match($levelValue, $message)) {
            return true;
        }

        return $proceed($level, $message, $context, $datetime);
    }
}
