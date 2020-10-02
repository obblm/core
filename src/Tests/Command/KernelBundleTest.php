<?php

namespace Obblm\Core\Tests\Command;

use Obblm\Core\ObblmCoreBundle;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;

class KernelBundleTest extends KernelTestCase
{
    protected $application;

    protected function setUp(): void
    {

    }

    public function testExecute()
    {
        self::bootKernel();
        $this->application = new Application(self::$kernel);
        $container = self::$kernel->getContainer();
        $this->assertInstanceOf(Container::class, $container);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->application = null;
    }
}
