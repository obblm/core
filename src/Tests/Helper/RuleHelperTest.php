<?php

namespace Obblm\Core\Tests\Helper;

use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Helper\CoreTranslation;
use Obblm\Core\Helper\Rule\Config\ConfigResolver;
use Obblm\Core\Helper\Rule\Config\RuleConfigResolver;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Service\Rule\Lrb6Rule;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class RuleHelperTest extends KernelTestCase
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

    public function testRealLrb6Rule():void
    {
        $this->loadRealLrb6Rule();
        $this->ruleHelper->addHelper(new Lrb6Rule());
        $this->ruleHelper->addRule($this->realRule);
        // Not cached helper
        $helper = $this->ruleHelper->getHelper($this->realRule);
        $this->assertInstanceOf(RuleHelperInterface::class, $helper);
        // Same helper Cached
        $helper = $this->ruleHelper->getHelper($this->realRule);
        $this->assertInstanceOf(RuleHelperInterface::class, $helper);
        /** @var RuleHelperInterface $helper */
        // LRB6 got 24 rosters
        $this->assertEquals(24, $helper->getRosters()->count());
        // LRB6 got 79 skills
        $this->assertEquals(79, $helper->getSkills()->count());
        // LRB6 got 56 star players
        $this->assertEquals(56, $helper->getStarPlayers()->count());
        // LRB6 got 7 inducements (8 - star player line)
        $this->assertEquals(7, $helper->getInducements()->count());
    }

    protected function loadRealLrb6Rule(): void
    {
        $finder = new Finder();
        $key = 'lrb6';
        $directory = dirname(__DIR__) . self::REAL_RULE_PATH . $key;
        $rules = [];
        $finder->files()->ignoreDotFiles(true)->name(['*.yaml', '*.yml'])->in($directory);
        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $content = Yaml::parseFile($file->getPathname());
                $rules = array_merge_recursive($rules, $content);
            }
            $ruleArray = $rules['rules'][$key];

            $treeResolver = new ConfigResolver(new RuleConfigResolver());
            $ruleArray = $treeResolver->resolve($ruleArray);

            /** @var Rule|null $rule */
            $this->realRule = (new Rule())
                ->setRuleKey($key)
                ->setReadOnly(true)
                ->setName(CoreTranslation::getRuleTitle($key))
                ->setRuleDirectory($directory)
                ->setPostBb2020($ruleArray['post_bb_2020'] ?? false)
                ->setTemplate($ruleArray['template'] ?? 'base')
                ->setRule($ruleArray);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->application = null;
        $this->ruleHelper = null;
        $this->realRule = null;
    }
}
