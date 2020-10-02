<?php

namespace Obblm\Core\Tests\Helper;

use Obblm\Core\Entity\Rule;
use Obblm\Core\Exception\UnexpectedTypeException;
use Obblm\Core\Helper\RuleHelper;
use phpDocumentor\Reflection\Types\Object_;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RuleHelperExceptionTest extends KernelTestCase
{
    const REAL_RULE_PATH = '/../Resources/datas/rules/';
    protected $application;
    /** @var RuleHelper */
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
        try {
            $this->ruleHelper->addRule($dummyRule);
            $this->ruleHelper->getHelper($dummyRule);
        } catch (UnexpectedTypeException $e) {
            if ($e) {
                $this->assertInstanceOf(UnexpectedTypeException::class, $e);
            } else {
                $this->fail('exception not expected : ' . get_class($e));
            }
        } catch (\Exception $e) {
            if ($e) {
                $this->assertInstanceOf(\Exception::class, $e);
            } else {
                $this->fail('exception not expected : ' . get_class($e));
            }
        }

        try {
            $dummyClass = new \stdClass;
            $dummyClass->rule = $dummyRule;
            $this->ruleHelper->getHelper($dummyClass);
        } catch (UnexpectedTypeException $e) {
            if ($e) {
                $this->assertInstanceOf(UnexpectedTypeException::class, $e);
            } else {
                $this->fail('exception not expected : ' . get_class($e));
            }
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->application = null;
        $this->ruleHelper = null;
    }
}
