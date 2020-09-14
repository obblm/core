<?php

namespace Obblm\Core\Helper;

use Doctrine\ORM\EntityManagerInterface;
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
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Obblm\Core\Service\TeamService;
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
        $this->rule = new ArrayCollection();
    }

    /**
     * @return Rule[]
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

    public function addHelper(RuleHelperInterface $rule) {
        if (!$rule instanceof RuleHelperInterface) {
            throw new UnexpectedTypeException($rule, RuleHelperInterface::class);
        }
        $this->helpers->offsetSet($rule->getKey(), $rule);
    }

    /**
     * @param Rule|CanHaveRuleInterface $item
     * @return RuleHelperInterface
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

    protected static function getCacheKey(Rule $rule) {
        return join(self::TRANSLATION_GLUE, ['obblm', 'rules', $rule->getRuleKey(), $rule->getId()]);
    }

    public function getCacheOrCreate($key, Rule $rule):RuleHelperInterface {
        $item = $this->cache->getItem($key);
        if (!$item->isHit()) {
            $helper = $this->getUnchachedHelper($rule->getRuleKey());
            $helper->attachRule($rule);
            $this->cache->save($item->set($helper));
        } else {
            $helper = $item->get();
        }

        return $helper;
    }

    private function getUnchachedHelper($key):?RuleHelperInterface {
        if (!isset($this->helpers[$key])) {
            throw new Exception('No RuleHelperInterface found for ' . $key);
        }
        return $this->helpers[$key];
    }

    public function getRuleChoices() {
        $rules = $this->em->getRepository(Rule::class)->findAll();

        $this->rules = (!$rules instanceof ArrayCollection) ? new ArrayCollection($rules) : $rules;

        $collector = new RulesCollectorEvent($this);
        $this->dispatcher->dispatch($collector, RulesCollectorEvent::COLLECT);
        return $this->rules;
    }

    public function getRulesAvailableForTeamCreation() {
        return $this->em->getRepository(Rule::class)->findAll();
    }

    public static function composeTranslationRosterKey($rule_key, $roster):string {
        return join(self::TRANSLATION_GLUE, ['obblm', $rule_key, 'rosters', $roster, 'title']);
    }

    public static function composeTranslationRosterDescription($rule_key, $roster):string {
        return join(self::TRANSLATION_GLUE, ['obblm', $rule_key, 'rosters', $roster, 'description']);
    }

    public static function composeTranslationInjuryKey($rule_key, $injury_key):string {
        return join(self::TRANSLATION_GLUE, ['obblm', $rule_key, 'injuries', $injury_key, 'name']);
    }

    public static function composeTranslationInjuryEffect($rule_key, $injury_key):string {
        return join(self::TRANSLATION_GLUE, ['obblm', $rule_key, 'injuries', $injury_key, 'effect']);
    }

    public static function getAvailableRosters(Rule $object):array {
        $rule = $object->getRule();
        return array_keys($rule['rosters']);
    }

    public static function getActionFormType(Rule $rule):string {
        return ($rule->isPostBb2020()) ? ActionBb2020Type::class : ActionType::class;
    }

    public static function getInjuryFormType(Rule $rule):string {
        return ($rule->isPostBb2020()) ? InjuryBb2020Type::class : InjuryType::class;
    }
    public function createNewTeamVersion(Team $team):TeamVersion {
        $version = (TeamService::getLastVersion($team))
            ->setTreasure($this->getHelper($team->getRule())->getMaxTeamCost());
        return $version;
    }
    public function destructTeamVersion(Team $team):TeamVersion {

    }
}
