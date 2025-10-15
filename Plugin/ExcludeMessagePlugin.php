<?php declare(strict_types=1);

namespace Yireo\LogExcludes\Plugin;

use Monolog\JsonSerializableDateTimeImmutable;
use Monolog\Level;
use Monolog\Logger;
use Yireo\LogExcludes\Util\MessageMatcher;

class ExcludeMessagePlugin
{
    public function __construct(
        private MessageMatcher $messageMatcher,
    ) {
    }

    public function aroundAddRecord(Logger $subject,
        callable $proceed,
        int|Level $level, string $message, array $context = [],
        JsonSerializableDateTimeImmutable|null $datetime = null
    ): bool {
        if ($this->messageMatcher->match($message)) {
            return true;
        }

        return $proceed($level, $message, $context, $datetime);
    }
}
