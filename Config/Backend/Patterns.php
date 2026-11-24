<?php
declare(strict_types=1);

namespace Yireo\LogExcludes\Config\Backend;

use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Framework\App\Area;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Throwable;
use Yireo\LogExcludes\Util\MessageMatcher;

class Patterns extends Value
{
    public const MIN_PATTERN_LENGTH = 6;
    public const MAX_PATTERNS = 50;

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        private MessageMatcher $messageMatcher,
        private AuthSession $authSession,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    public function beforeSave()
    {
        $lines = explode("\n", str_replace("\r", "\n", (string)$this->getValue()));

        $patterns = [];
        foreach ($lines as $line) {
            $line = trim($line, " \f\n\r\t\v\x00");
            if ($line === '') {
                continue;
            }

            if (strlen($line) < self::MIN_PATTERN_LENGTH) {
                throw new LocalizedException(
                    __(
                        'Exclude pattern "%1" is too short: use at least %2 characters.',
                        $line,
                        self::MIN_PATTERN_LENGTH
                    )
                );
            }

            $patterns[] = $line;
        }

        if (count($patterns) > self::MAX_PATTERNS) {
            throw new LocalizedException(
                __('Too many exclude patterns: use at most %1 patterns.', self::MAX_PATTERNS)
            );
        }

        $this->setValue(implode("\n", array_unique($patterns)));

        return parent::beforeSave();
    }

    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $this->messageMatcher->_resetState();
            $patternCount = count(array_filter(explode("\n", (string)$this->getValue())));
            $userName = $this->getAdminUserName();
            $this->messageMatcher->runUnfiltered(function () use ($patternCount, $userName) {
                $this->_logger->notice(sprintf(
                    'Yireo_LogExcludes: exclude patterns changed by "%s" (%d patterns)',
                    $userName,
                    $patternCount
                ));
            });
        }

        return parent::afterSave();
    }

    private function getAdminUserName(): string
    {
        try {
            if ($this->_appState->getAreaCode() !== Area::AREA_ADMINHTML) {
                return 'non-admin context';
            }

            $user = $this->authSession->getUser();
        } catch (Throwable) {
            return 'unknown';
        }

        return $user ? (string)$user->getUserName() : 'unknown';
    }
}
