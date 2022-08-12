<?php

namespace Obblm\Core\Infrastructure\Shared\Cache;

use Obblm\Core\Domain\Contracts\CacheStorageInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class CacheStorage implements CacheStorageInterface
{
    private AdapterInterface $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function getOrCreate(string $key, callable $fallback)
    {
        $item = $this->adapter->getItem($key);
        if (!$item->isHit()) {
            $value = $fallback();
            $item->expiresAfter(864000);
            $item->set($value);
            $this->adapter->save($item);
        }

        return $item->get();
    }
}
