# Navigation bar

An event is dispatch to add links to the navigation bar.

You can listen to the event and add links and collections.

Subcribe to the collector :

```php
<?php

namespace App\Listener;

use Obblm\Core\Event\TwigCollectorEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TestCollectorSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            TwigCollectorEvent::COLLECT_NAV_BAR => 'onCollectNavBar',
            TwigCollectorEvent::COLLECT_ADMIN_BAR => 'onCollectAdminNavBar',
        ];
    }

    public function onCollectNavBar(TwigCollectorEvent $event) {
        /**
         * Add some routes to the navigation
         */
        $collection = $event->getCollection();
        $collection->addToCollection(new NavigationLink());
        $sub = new NavigationCollection("My collection");
        $sub->addToCollection(new NavigationLink("my_link_route", "Link to my page", ['parameter' => "value"], "icon"));
        $collection->addToCollection($sub);
    }

    public function onCollectAdminNavBar(TwigCollectorEvent $event) {
        /**
         * Add some routes to the admin part of the navigation
         */
        $collection = $event->getCollection();
        $collection->addToCollection(new NavigationLink());
        $sub = new NavigationCollection("My admin collection");
        $sub->addToCollection(new NavigationLink("my_admin_link_route", "Link to my admin page", ['parameter' => "value"], "icon"));
        $collection->addToCollection($sub);
    }
}
```

You can see all options in the [NavigationCollection](../../Twig/Parts/NavigationCollection.php) class and the [NavigationLink](../../Twig/Parts/NavigationLink.php) class.
