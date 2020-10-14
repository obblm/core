<?php

namespace Obblm\Core\Helper;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Event\RulesCollectorEvent;
use Obblm\Core\Exception\UnexpectedTypeException;
use Obblm\Core\Form\Player\ActionBb2020Type;
use Obblm\Core\Form\Player\ActionType;
use Obblm\Core\Form\Player\InjuryBb2020Type;
use Obblm\Core\Form\Player\InjuryType;
use Obblm\Core\Helper\Rule\CanHaveRuleInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RuleHelper
{
    const CACHE_GLUE = '.';

    private $helpers;
    private $em;
    private $dispatcher;
    private $rules;
    private $cacheAdapter;

    public function __construct(AdapterInterface $adapter, EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->helpers = new ArrayCollection();
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->cacheAdapter = $adapter;
        $this->rules = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getRules():ArrayCollection
    {
        return $this->rules;
    }

    /**
     * @param Rule|CanHaveRuleInterface $item
     * @return $this
     */
    public function addRule($item): self
    {
        if (!$this->rules->contains($item)) {
            $this->rules[] = $item;
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
     * @param RuleHelperInterface $helper
     */
    public function addHelper(RuleHelperInterface $helper)
    {
        $this->helpers->offsetSet($helper->getKey(), $helper);
    }

    /**
     * @return ArrayCollection|Rule[]|object[]
     */
    public function getRuleChoices()
    {
        $rules = $this->em->getRepository(Rule::class)->findAll();

        $this->rules = (!$rules instanceof ArrayCollection) ? new ArrayCollection($rules) : $rules;

        $collector = new RulesCollectorEvent($this);
        $this->dispatcher->dispatch($collector, RulesCollectorEvent::COLLECT);
        return $this->rules;
    }

    /**
     * @return Rule[]|object[]
     */
    public function getRulesAvailableForTeamCreation()
    {
        return $this->em->getRepository(Rule::class)->findAll();
    }

    /**
     * @param Rule $object
     * @return array
     */
    public static function getAvailableRosters(Rule $object):array
    {
        $rule = $object->getRule();
        return array_keys($rule['rosters']);
    }

    /**
     * @param Rule $rule
     * @return string
     */
    public static function getActionFormType(Rule $rule):string
    {
        return ($rule->isPostBb2020()) ? ActionBb2020Type::class : ActionType::class;
    }

    /**
     * @param Rule $rule
     * @return string
     */
    public static function getInjuryFormType(Rule $rule):string
    {
        return ($rule->isPostBb2020()) ? InjuryBb2020Type::class : InjuryType::class;
    }

    /****************
     * CACHE METHODS
     ***************/

    /**
     * @param Rule|CanHaveRuleInterface $item
     * @return RuleHelperInterface
     * @throws UnexpectedTypeException
     */
    public function getHelper($item):RuleHelperInterface
    {
        $rule = ($item instanceof CanHaveRuleInterface) ? $item->getRule() : $item;

        if (!($rule instanceof Rule)) {
            throw new UnexpectedTypeException($rule, Rule::class);
        }

        $key = $this->getCacheKey($rule);
        return $this->getCacheOrCreate($key, $rule);
    }

    /**
     * @param $key
     * @param Rule $rule
     * @return RuleHelperInterface
     * @throws
     */
    public function getCacheOrCreate($key, Rule $rule):RuleHelperInterface
    {
        try {
            $item = $this->cacheAdapter->getItem($key);
            if (!$item->isHit()) {
                $helper = $this->getNotCachedHelper($rule->getRuleKey());
                $helper->attachRule($rule);
                $this->cacheAdapter->save($item->set([
                    'class' => get_class($helper) . '::class',
                    'helper' => $helper
                ]));
            } else {
                $normalizedRule = $item->get();
                $helper = $normalizedRule['helper'];
            }
        } catch (InvalidArgumentException $e) {
            $helper = $this->getNotCachedHelper($rule->getRuleKey());
            $helper->attachRule($rule);
        }

        return $helper;
    }

    /**
     * @param $key
     * @return RuleHelperInterface|null
     * @throws Exception
     */
    private function getNotCachedHelper($key):?RuleHelperInterface
    {
        if (!isset($this->helpers[$key])) {
            throw new Exception('No RuleHelperInterface found for ' . $key);
        }
        return $this->helpers[$key];
    }

    /**
     * @param Rule $rule
     * @return string
     */
    protected static function getCacheKey(Rule $rule)
    {
        return join(self::CACHE_GLUE, ['obblm', 'rules', $rule->getRuleKey(), $rule->getId()]);
    }
}
