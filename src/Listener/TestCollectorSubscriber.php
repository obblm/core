<?php

namespace Obblm\Core\Listener;

use Obblm\Core\Event\RulesCollectorEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TestCollectorSubscriber implements EventSubscriberInterface
{
    protected $context;
    protected $ruleHelper;

    public static function getSubscribedEvents()
    {
        return [
            RulesCollectorEvent::COLLECT => 'onCollect',
        ];
    }

    public function onCollect(RulesCollectorEvent $event) {
        /**
         * Do something like
         * $helper = $event->getHelper();
         * foreach($helper->getRules() as $rule) {
         *     if($rule->getRuleKey() == 'bb16') { $helper->removeRule($rule);}
         * }
         * But it's better to add some other rules :)
         */
    }
}