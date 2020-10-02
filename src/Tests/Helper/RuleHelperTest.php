<?php

namespace Obblm\Core\Tests\Helper;

use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Form\Player\ActionType;
use Obblm\Core\Form\Player\InjuryType;
use Obblm\Core\Helper\CoreTranslation;
use Obblm\Core\Helper\Rule\Config\ConfigResolver;
use Obblm\Core\Helper\Rule\Config\RuleConfigResolver;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\ObblmCoreBundle;
use Obblm\Core\Service\Rule\Lrb6Rule;
use Obblm\Core\Tests\Kernel;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class RuleHelperTest extends TestCase
{
    const REAL_RULE_PATH = '/../Resources/datas/rules/';

    /** @var Kernel */
    protected $kernel;
    /** @var Application */
    protected $application;
    /** @var RuleHelper */
    protected $ruleHelper;
    /** @var Rule */
    protected $realRule;

    protected function setUp(): void
    {
        $this->kernel = new Kernel('test', false);
        $this->kernel->boot();
        $this->application = new Application($this->kernel);
        $this->ruleHelper = $this->kernel->getContainer()->get(RuleHelper::class);
        $this->loadRealLrb6Rule();
    }

    public function testCachedAndNonCachedHelpersHasSameResults():void
    {
        // Not cached helper
        $notCached = $this->ruleHelper->getHelper($this->realRule);
        // Call it twice to get cached one!
        $cached = $this->ruleHelper->getHelper($this->realRule);

        // Both are RuleHelperInterface
        $this->assertInstanceOf(RuleHelperInterface::class, $notCached);
        $this->assertInstanceOf(RuleHelperInterface::class, $cached);

        // Both have same rule
        $this->assertSame($notCached->getAttachedRule()->getRuleKey(), $cached->getAttachedRule()->getRuleKey());
        $this->assertSame($notCached->getAttachedRule()->getRule(), $cached->getAttachedRule()->getRule());

        // Both got 24 rosters
        $this->assertEquals($notCached->getRosters()->count(), $cached->getRosters()->count());
        // Both got 79 skills
        $this->assertEquals($notCached->getSkills()->count(), $cached->getSkills()->count());
        // Both got 56 star players
        $this->assertEquals($notCached->getStarPlayers()->count(), $cached->getStarPlayers()->count());
        // Both got 7 inducements (8 - star player line)
        $this->assertEquals($notCached->getInducements()->count(), $cached->getInducements()->count());
    }

    public function testAvailableRules()
    {
        $this->ruleHelper->addRule($this->realRule);
        // Is the rule has been added to the Collection
        $collection = $this->ruleHelper->getRules();
        $this->assertEquals(true, $collection->contains($this->realRule));

        $this->ruleHelper->removeRule($this->realRule);
        // Is the rule has been removed from the Collection
        $collection = $this->ruleHelper->getRules();
        $this->assertEquals(false, $collection->contains($this->realRule));

        // LRB6 got 24 rosters
        $this->assertEquals(24, count($this->ruleHelper->getAvailableRosters($this->realRule)));

        // LRB6 got ActionType & Injury Type
        $this->assertEquals(ActionType::class, $this->ruleHelper->getActionFormType($this->realRule));
        $this->assertEquals(InjuryType::class, $this->ruleHelper->getInjuryFormType($this->realRule));
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
        $this->kernel->shutdown();
        $this->kernel = null;
        $this->application = null;
        $this->ruleHelper = null;
        $this->realRule = null;
    }
}
