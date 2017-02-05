<?php
namespace AppBundle\EventListener;

use ADesigns\CalendarBundle\Event\CalendarEvent;
use ADesigns\CalendarBundle\Entity\EventEntity;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CalendarEventListener
{
    private $entityManager;

    private $token_storage;
    public function __construct(EntityManager $entityManager,TokenStorageInterface $token_storage)
    {
        $this->entityManager = $entityManager;
        $this->token_storage = $token_storage;

    }

    public function loadEvents(CalendarEvent $calendarEvent)
    {
        $startDate = $calendarEvent->getStartDatetime();
        $endDate = $calendarEvent->getEndDatetime();

        // The original request so you can get filters from the calendar
        // Use the filter in your query for example

        $request = $calendarEvent->getRequest();
        $filter = $request->get('filter');



        // load events using your custom logic here,
        // for instance, retrieving events from a repository

        $companyEvents = $this->entityManager->getRepository('AppBundle:UserEvent')
        ->createQueryBuilder('user_event')
        ->where('user_event.date BETWEEN :startDate and :endDate')
        ->setParameter('startDate', $startDate->format('Y-m-d H:i:s'))
        ->setParameter('endDate', $endDate->format('Y-m-d H:i:s'))
        ->getQuery()->getResult();

        // $companyEvents and $companyEvent in this example
        // represent entities from your database, NOT instances of EventEntity
        // within this bundle.
        //
        // Create EventEntity instances and populate it's properties with data
        // from your own entities/database values.

        foreach($companyEvents as $companyEvent) {

            $eventDate = $companyEvent->getDate();
            $now = new \DateTime('now');

            $eventEntity = new EventEntity($eventDate->format("H:i")." ".$companyEvent->getEventTitle(), $eventDate, null, true);

            //optional calendar event settings
            $eventEntity->setAllDay(true); // default is false, set to true if this is an all day event
            if($eventDate < $now){
                $eventEntity->setBgColor('#FF0000'); //set the background color of the event's label
            }else{
                $eventEntity->setBgColor('#17961E'); //set the background color of the event's label
            }

            $eventEntity->setFgColor('#FFFFFF'); //set the foreground color of the event's label
            $eventEntity->setUrl("http://localhost/CalSym/web/app_dev.php/app/event/".$companyEvent->getId());
            $eventEntity->setCssClass('my-custom-class'); // a custom class you may want to apply to event labels

            //finally, add the event to the CalendarEvent for displaying on the calendar
            $calendarEvent->addEvent($eventEntity);
        }

    }
}