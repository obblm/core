<?php

namespace Obblm\Core\Service;

use Obblm\Core\Entity\Rule;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class RuleService {

    const TRANSLATION_GLUE = '.';

    private $rule_services;
    private $rules;
    private $cache;

    public function __construct(AdapterInterface $cache) {
        $this->rule_services = new ArrayCollection();
        $this->rules = new ArrayCollection();
        $this->cache = $cache;
    }

    public function getRules() {
        return $this->rules;
    }

    public function addRule(RuleInterface $rule) {
        if (!$rule instanceof RuleInterface) {
            throw new UnexpectedTypeException($rule, RuleInterface::class);
        }
        $this->rule_services->offsetSet($rule->getKey(), $rule);
    }
    public function getRuleService($key):?RuleInterface {
        if (!isset($this->rule_services[$key])) {
            throw new Exception('No Service found for ' . $key);
        }
        return $this->rule_services[$key];
    }
    public function getRule(Rule $rule):RuleInterface {
        $key = self::getCacheKey($rule);
        return $this->getCacheOrCreate($key, $rule);
    }

    protected static function getCacheKey(Rule $rule) {
        return join(self::TRANSLATION_GLUE, ['obblm', 'rules', $rule->getRuleKey(), $rule->getId()]);
    }
    public function getCacheOrCreate($key, Rule $rule):RuleInterface {
        $item = $this->cache->getItem($key);
        if (!$item->isHit()) {
            $ruleService = $this->getRuleService($rule->getRuleKey());
            $ruleService->attachRule($rule);
            $this->cache->save($item->set($ruleService));
        } else {
            $ruleService = $item->get();
        }

        return $ruleService;
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
}
