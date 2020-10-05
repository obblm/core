<?php

namespace Obblm\Core\Tests\Helper;

use Obblm\Core\Contracts\Rule\RuleApplicativeInterface;
use Obblm\Core\Contracts\Rule\RuleTeamInterface;
use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\Player;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Exception\InvalidArgumentException;
use Obblm\Core\Exception\NotFoundKeyException;
use Obblm\Core\Exception\UnexpectedTypeException;
use Obblm\Core\Form\Player\ActionType;
use Obblm\Core\Form\Player\InjuryType;
use Obblm\Core\Helper\CoreTranslation;
use Obblm\Core\Helper\PlayerHelper;
use Obblm\Core\Helper\Rule\Config\ConfigResolver;
use Obblm\Core\Helper\Rule\Config\RuleConfigResolver;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Helper\TeamHelper;
use Obblm\Core\Tests\Kernel;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class Lrb6RuleHelperTest extends TestCase
{
    /** @var Kernel */
    protected $kernel;
    /** @var Application */
    protected $application;
    /** @var RuleHelperInterface */
    protected $helper;
    /** @var Rule */
    protected $realRule;

    protected function setUp(): void
    {
        $this->kernel = new Kernel('test', false);
        $this->kernel->boot();
        $this->application = new Application($this->kernel);
        $this->loadRealLrb6Rule();
        /** @var RuleHelper $ruleHelper */
        $ruleHelper = $this->kernel->getContainer()->get(RuleHelper::class);
        $this->helper = $ruleHelper->getHelper($this->realRule);
    }

    public function testGetters()
    {
        $this->assertEquals(24, $this->helper->getRosters()->count(), "LRB6 got 24 rosters");
        $this->assertEquals(79, $this->helper->getSkills()->count(), "LRB6 got 79 skills");
        $this->assertEquals(56, $this->helper->getStarPlayers()->count(), "LRB6 got 56 star players");
        $this->assertEquals(7, $this->helper->getInducements()->count(), "LRB6 got 7 inducements (8 - star player line)");
    }

    public function testTeamHelper()
    {
        $team = $this->createFakeDwarfTeam();
        /** @var RuleHelper $ruleHelper */
        $ruleHelper = $this->kernel->getContainer()->get(RuleHelper::class);
        $helper = $ruleHelper->getHelper($team);
        $this->assertEquals($this->helper, $helper);
    }

    public function testRuleApplicativeInterfaceMethods()
    {
        /** Tests for @var RuleApplicativeInterface */
        // LRB6 got ActionType & Injury Type
        $this->assertEquals(ActionType::class, $this->helper->getActionsFormClass());
        $this->assertEquals(InjuryType::class, $this->helper->getInjuriesFormClass());
        $this->assertEquals($this->realRule->getTemplate(), $this->helper->getTemplateKey());
    }

    public function testRuleTeamInterfaceMethods()
    {
        /** Tests for @var RuleTeamInterface */
        $rule = $this->realRule->getRule();
        $this->assertEquals(1000000, $this->helper->getMaxTeamCost(), "Team max cost is 1000k");

        $dwarves = $this->createFakeDwarfTeam();
        $undeads = $this->createFakeUndeadTeam();
        $this->assertEquals(50000, $this->helper->getRerollCost($dwarves), "Dwarf Reroll cost is 50k");
        $this->assertEquals(50000, $this->helper->getApothecaryCost($dwarves), "Apothecary cost is 50k");
        $this->assertEquals(true, $this->helper->couldHaveApothecary($dwarves), "Dwarf could have apothecary");
        $this->assertEquals(false, $this->helper->couldHaveApothecary($undeads), "Undead could not have Apothecary");
        $this->assertEquals(10000, $this->helper->getCheerleadersCost($dwarves), "Cheerleader cost is 10k");
        $this->assertEquals(10000, $this->helper->getAssistantsCost($dwarves), "Assistant cost is 10k");
        $this->assertEquals(10000, $this->helper->getPopularityCost($dwarves), "Popularity cost is 10k");
        $types = ['blocker', 'blitzer', 'runner', 'slayer', 'death_roller'];
        // Dwarf positions are $types
        $version = TeamHelper::getLastVersion($dwarves);
        // Start Team value must be 0
        $this->assertEquals(0, $this->helper->calculateTeamValue($version));
        foreach ($this->helper->getAvailablePlayerTypes($dwarves->getRoster()) as $key => $type) {
            $this->assertContains($key, $types, "Dwarf team has type $key");
            // For team value test
            $dwarves->addPlayer(
                (new Player())
                    ->setType(PlayerHelper::composePlayerKey($dwarves->getRule()->getRuleKey(), $dwarves->getRoster(), $key))
            );
        }
        $this->assertEquals(16, $this->helper->getMaxPlayersByType($dwarves->getRoster(), 'blocker'), "Dwarf have max 16 blocker");
        $this->assertEquals(2, $this->helper->getMaxPlayersByType($dwarves->getRoster(), 'blitzer'), "Dwarf have max 2 blitzer");
        $this->assertEquals(2, $this->helper->getMaxPlayersByType($dwarves->getRoster(), 'runner'), "Dwarf have max 2 runner");
        $this->assertEquals(2, $this->helper->getMaxPlayersByType($dwarves->getRoster(), 'slayer'), "Dwarf have max 2 slayer");
        $this->assertEquals(1, $this->helper->getMaxPlayersByType($dwarves->getRoster(), 'death_roller'), "Dwarf have max 1 death_roller");
        /*
         * 1 blocker + 1 blitzer + 1 runner + 1 slayer + 1 death_roller = 480k
         */
        $this->assertEquals(480000, $this->helper->calculateTeamValue($version), "calculateTeamValue must return 480k");
        $this->assertEquals(48, $this->helper->calculateTeamRate($version), "calculateTeamRate must return 48");

        /*
         * 480k + 2 RR (2x50k) = 580k
         */
        // For team value test
        $version->setRerolls(2);
        $this->assertEquals(580000, $this->helper->calculateTeamValue($version), "calculateTeamValue must return 580k");

        /*
         * 580k + Apo (50k) = 630k
         */
        // For team value test
        $version->setApothecary(true);
        $this->assertEquals(630000, $this->helper->calculateTeamValue($version), "calculateTeamValue must return 630k");
    }

    public function testException()
    {
        try {
            $this->helper->calculateTeamValue((new TeamVersion()));
        } catch (InvalidArgumentException $e) {
            if ($e) {
                $this->assertInstanceOf(InvalidArgumentException::class, $e);
            } else {
                $this->fail('exception not expected : ' . get_class($e));
            }
        }
        try {
            $this->helper->getMaxPlayersByType('dwarf', 'dummyType');
        } catch (NotFoundKeyException $e) {
            if ($e) {
                $this->assertInstanceOf(NotFoundKeyException::class, $e);
            } else {
                $this->fail('exception not expected : ' . get_class($e));
            }
        }
    }

    protected function createFakeDwarfTeam(): Team
    {
        return (new Team())
            ->setRule($this->realRule)
            ->setRoster('dwarf')
            ->setName('Dwarf Test')
            ;
    }

    protected function createFakeUndeadTeam(): Team
    {
        return (new Team())
            ->setRule($this->realRule)
            ->setRoster('undead')
            ->setName('Undead Test')
            ;
    }

    protected function loadRealLrb6Rule(): void
    {
        $finder = new Finder();
        $key = 'lrb6';
        $directory = dirname(__DIR__) . RuleHelperTest::REAL_RULE_PATH . $key;
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
        $this->helper = null;
        $this->realRule = null;
    }
}
