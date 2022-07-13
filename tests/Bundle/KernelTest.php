<?php

namespace Obblm\Core\Tests\Bundle;

use Obblm\Core\ObblmCoreBundle;
use Obblm\Core\Tests\Kernel;
use PHPUnit\Framework\TestCase;

class KernelTest extends TestCase
{
    protected $kernel;
    protected $bundle;

    protected function setUp(): void
    {
        $this->kernel = new Kernel('test', false);
        $this->kernel->boot();
        $this->bundle = $this->kernel->getBundle((new ObblmCoreBundle())->getName());
    }

    public function testBundle()
    {
        // The bundle is correctly registred
        $this->assertInstanceOf(ObblmCoreBundle::class, $this->bundle);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->kernel->shutdown();
        // doing this is recommended to avoid memory leaks
        $this->kernel = null;
    }
}
