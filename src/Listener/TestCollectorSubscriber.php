<?php

namespace Obblm\Core\Listener;

use Obblm\Core\Event\RulesCollectorEvent;
use Obblm\Core\Event\TwigCollectorEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TestCollectorSubscriber implements EventSubscriberInterface
{
    protected $context;
    protected $ruleHelper;

    public static function getSubscribedEvents()
    {
        return [
            RulesCollectorEvent::COLLECT => 'onCollect',
            TwigCollectorEvent::COLLECT_NAV_BAR => 'onCollectNavBar',
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

    public function onCollectNavBar(TwigCollectorEvent $event) {
        /**
         * Add some routes to the namigation something like
         * $collection = $event->getCollection();
         * $collection->addToCollection(new NavigationLink());
         * $sub = new NavigationCollection();
         * $sub->addToCollection(new NavigationLink("obblm_championship", "Link to Championships", [], "trophy"));
         * $collection->addToCollection($sub);
        */
    }
}