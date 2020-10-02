<?php

namespace Obblm\Core\Tests\Helper;

use Obblm\Core\Entity\Rule;
use Obblm\Core\Helper\RuleHelper;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RuleHelperExceptionTest extends KernelTestCase
{
    const REAL_RULE_PATH = '/../Resources/datas/rules/';
    protected $application;
    protected $ruleHelper;
    protected $realRule;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->application = new Application(self::$kernel);
        $this->ruleHelper = self::$kernel->getContainer()->get(RuleHelper::class);
    }

    public function testExceptionGetter(): void
    {
        $dummyRule = (new Rule())
            ->setRuleKey('testRule')
            ->setTemplate('testRule')
        ;
        $this->expectException(\Exception::class);
        $this->ruleHelper->addRule($dummyRule);
        $this->ruleHelper->getHelper($dummyRule);
        $this->expectException('');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->application = null;
        $this->ruleHelper = null;
    }
}
