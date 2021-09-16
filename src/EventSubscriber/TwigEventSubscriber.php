<?php

namespace App\EventSubscriber;

use App\Repository\ConferenceRepository;
use Twig\Environment;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private Environment $twig;
    private ConferenceRepository $conferenceRepository;

    public function __construct(
        Environment $twig, 
        ConferenceRepository $conferenceRepository
    ) {
        $this->twig = $twig;
        $this->conferenceRepository = $conferenceRepository;
    }

    public function onControllerEvent(ControllerEvent $event)
    {
        // Now, you can add as many controllers as you want: the conferences variable,
        // will always be available in Twig
        $this->twig->addGlobal(
            'conferences', 
            $this->conferenceRepository->findAllOrderByYearAndCityASC());
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
