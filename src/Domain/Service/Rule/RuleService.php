<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Service\Rule;

use Obblm\Core\Domain\Contracts\CacheStorageInterface;
use Obblm\Core\Domain\Contracts\HasRuleInterface;
use Obblm\Core\Domain\Contracts\ObblmBusInterface;
use Obblm\Core\Domain\Contracts\RuleHelperInterface;
use Obblm\Core\Domain\Exception\UnexpectedTypeException;
use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Model\Rule;
use Obblm\Core\Domain\Repository\RuleRepositoryInterface;
use Obblm\Core\Domain\Service\MessageBusService;

class RuleService extends MessageBusService
{
    const CACHE_GLUE = '.';

    private RuleRepositoryInterface $repository;
    private CacheStorageInterface $storage;
    private array $helpers;

    public function __construct(CacheStorageInterface $storage, ObblmBusInterface $messageBus, RuleRepositoryInterface $repository)
    {
        parent::__construct($messageBus);
        $this->repository = $repository;
        $this->storage = $storage;
        $this->helpers = [];
    }

    public function addHelper(RuleHelperInterface $helper)
    {
        $this->helpers[$helper->getKey()] = $helper;
    }

    public function get($id): ?Rule
    {
        return $this->repository->get($id);
    }

    public function findAll(): ?array
    {
        return $this->repository->findAll();
    }

    public function findAllowedRules(Coach $coach): ?array
    {
        return $this->repository->findAllowedRules($coach);
    }

    /****************
     * CACHE METHODS
     ***************/

    /**
     * @param Rule|HasRuleInterface $item
     *
     * @throws UnexpectedTypeException
     */
    public function getHelper($item): RuleHelperInterface
    {
        $rule = ($item instanceof HasRuleInterface) ? $item->getRule() : $item;
        if (!($rule instanceof Rule)) {
            throw new UnexpectedTypeException(get_class($rule), Rule::class);
        }

        return $this->getCacheOrCreate($rule);
    }

    /**
     * @param $key
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function getCacheOrCreate(Rule $rule): RuleHelperInterface
    {
        $key = $this->getCacheKey($rule);

        try {
            $item = $this->storage->getOrCreate($key, function () use ($rule) {
                $notCachedHelper = $this->getNotCachedHelper($rule->getRuleKey());
                $notCachedHelper->attachRule($rule);

                return [
                        'class' => get_class($notCachedHelper).'::class',
                        'helper' => $notCachedHelper,
                    ];
            });
            $helper = $item['helper'];
        } catch (\InvalidArgumentException $e) {
            $helper = $this->getNotCachedHelper($rule->getRuleKey());
            $helper->attachRule($rule);
        }

        return $helper;
    }

    /**
     * @param $key
     *
     * @throws \Exception
     */
    private function getNotCachedHelper($key): ?RuleHelperInterface
    {
        if (!isset($this->helpers[$key])) {
            throw new \Exception('No RuleHelperInterface found for '.$key);
        }

        return $this->helpers[$key];
    }

    /**
     * @return string
     */
    protected static function getCacheKey(Rule $rule)
    {
        return join(self::CACHE_GLUE, ['obblm', 'rules', $rule->getRuleKey(), $rule->getId()]);
    }
}
