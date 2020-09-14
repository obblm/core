<?php

namespace Obblm\Core\Helper;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Event\RulesCollectorEvent;
use Obblm\Core\Form\Player\ActionBb2020Type;
use Obblm\Core\Form\Player\ActionType;
use Obblm\Core\Form\Player\InjuryBb2020Type;
use Obblm\Core\Form\Player\InjuryType;
use Obblm\Core\Helper\Rule\CanHaveRuleInterface;
use Obblm\Core\Helper\Rule\RuleHelperInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class RuleHelper {

    const TRANSLATION_GLUE = '.';

    private $helpers;
    private $em;
    private $dispatcher;
    private $rules;
    private $cache;

    public function __construct(AdapterInterface $cache, EntityManagerInterface $em, EventDispatcherInterface $dispatcher) {
        $this->helpers = new ArrayCollection();
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->cache = $cache;
        $this->rules = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getRules():ArrayCollection {
        return $this->rules;
    }

    /**
     * @param Rule|CanHaveRuleInterface $item
     * @return $this
     */
    public function addRule($item): self
    {
        if (!$this->rules->contains($item)) {
            if($item instanceof Rule) {
                $this->rules[] = $item;
            }
            else if($item instanceof CanHaveRuleInterface) {
                $this->rules[] = $item;
            }
        }

        return $this;
    }
    /**
     * @param Rule|CanHaveRuleInterface $item
     * @return $this
     */
    public function removeRule($item): self
    {
        if ($this->rules->contains($item)) {
            $this->rules->removeElement($item);
        }
        return $this;
    }

    /**
     * @param RuleHelperInterface $rule
     */
    public function addHelper(RuleHelperInterface $rule) {
        if (!$rule instanceof RuleHelperInterface) {
            throw new UnexpectedTypeException($rule, RuleHelperInterface::class);
        }
        $this->helpers->offsetSet($rule->getKey(), $rule);
    }

    /**
     * @return ArrayCollection|Rule[]|object[]
     */
    public function getRuleChoices() {
        $rules = $this->em->getRepository(Rule::class)->findAll();

        $this->rules = (!$rules instanceof ArrayCollection) ? new ArrayCollection($rules) : $rules;

        $collector = new RulesCollectorEvent($this);
        $this->dispatcher->dispatch($collector, RulesCollectorEvent::COLLECT);
        return $this->rules;
    }

    /**
     * @return Rule[]|object[]
     */
    public function getRulesAvailableForTeamCreation() {
        return $this->em->getRepository(Rule::class)->findAll();
    }

    /**
     * @param $rule_key
     * @param $roster
     * @return string
     */
    public static function composeTranslationRosterKey($rule_key, $roster):string {
        return join(self::TRANSLATION_GLUE, ['obblm', $rule_key, 'rosters', $roster, 'title']);
    }

    /**
     * @param $rule_key
     * @param $roster
     * @return string
     */
    public static function composeTranslationRosterDescription($rule_key, $roster):string {
        return join(self::TRANSLATION_GLUE, ['obblm', $rule_key, 'rosters', $roster, 'description']);
    }

    /**
     * @param $rule_key
     * @param $injury_key
     * @return string
     */
    public static function composeTranslationInjuryKey($rule_key, $injury_key):string {
        return join(self::TRANSLATION_GLUE, ['obblm', $rule_key, 'injuries', $injury_key, 'name']);
    }

    /**
     * @param $rule_key
     * @param $injury_key
     * @return string
     */
    public static function composeTranslationInjuryEffect($rule_key, $injury_key):string {
        return join(self::TRANSLATION_GLUE, ['obblm', $rule_key, 'injuries', $injury_key, 'effect']);
    }

    /**
     * @param Rule $object
     * @return array
     */
    public static function getAvailableRosters(Rule $object):array {
        $rule = $object->getRule();
        return array_keys($rule['rosters']);
    }

    /**
     * @param Rule $rule
     * @return string
     */
    public static function getActionFormType(Rule $rule):string {
        return ($rule->isPostBb2020()) ? ActionBb2020Type::class : ActionType::class;
    }

    /**
     * @param Rule $rule
     * @return string
     */
    public static function getInjuryFormType(Rule $rule):string {
        return ($rule->isPostBb2020()) ? InjuryBb2020Type::class : InjuryType::class;
    }

    /**
     * @param Team $team
     * @return TeamVersion
     * @throws \Psr\Cache\InvalidArgumentException
     */
    //TODO: move to team helper
    public function createNewTeamVersion(Team $team):TeamVersion {
        $version = (TeamHelper::getLastVersion($team))
            ->setTreasure($this->getHelper($team->getRule())->getMaxTeamCost());
        return $version;
    }
    //TODO: move to team helper
    public function destructTeamVersion(Team $team):TeamVersion {

    }

    /****************
     * CACHE METHODS
     ***************/

    /**
     * @param $item
     * @return RuleHelperInterface
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getHelper($item):RuleHelperInterface {
        if($item instanceof Rule) {
            $rule = $item;
        }
        else if($item instanceof CanHaveRuleInterface) {
            $rule = $item->getRule();
        }
        $key = $this->getCacheKey($rule);
        return $this->getCacheOrCreate($key, $rule);
    }

    /**
     * @param $key
     * @param Rule $rule
     * @return RuleHelperInterface
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getCacheOrCreate($key, Rule $rule):RuleHelperInterface {
        $item = $this->cache->getItem($key);
        if (!$item->isHit()) {
            $helper = $this->getNotCachedHelper($rule->getRuleKey());
            $helper->attachRule($rule);
            $this->cache->save($item->set($helper));
        } else {
            $helper = $item->get();
        }

        return $helper;
    }

    /**
     * @param $key
     * @return RuleHelperInterface|null
     * @throws Exception
     */
    private function getNotCachedHelper($key):?RuleHelperInterface {
        if (!isset($this->helpers[$key])) {
            throw new Exception('No RuleHelperInterface found for ' . $key);
        }
        return $this->helpers[$key];
    }

    /**
     * @param Rule $rule
     * @return string
     */
    protected static function getCacheKey(Rule $rule) {
        return join(self::TRANSLATION_GLUE, ['obblm', 'rules', $rule->getRuleKey(), $rule->getId()]);
    }
}
