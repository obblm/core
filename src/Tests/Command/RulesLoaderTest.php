<?php

namespace Obblm\Core\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class RulesLoaderTest extends KernelTestCase
{
    protected $application;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->application = new Application(self::$kernel);
        $command = $this->application->find('doctrine:database:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $command = $this->application->find('doctrine:schema:update');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            '--force' => true
        ]);
    }

    public function testExecute()
    {
        $command = $this->application->find('obblm:rules:load');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        // the output of the command in the console
        $this->assertStringContainsString('[OK] All rules have been created or updated.', $output);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->application = null;
    }
}
