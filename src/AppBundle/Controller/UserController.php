<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\UserEvent;
use AppBundle\Form\Type\UserEventType;

class UserController extends Controller {

    /**
     * @Route("/app", name="calendar_app_dashboard")
     *
     * @Template
     */
    public function dashboardAction() {

        return [];
    }

    /**
     * @Route("/app/event/add", name="calendar_app_addEvent")
     *
     * @Template
     */
    public function addEventAction(Request $request) {

        $user = $this->get('security.context')->getToken()->getUser();

        // create an event and give it some initial data
        $event = new UserEvent();
        $event->setDate(new \DateTime("today"));
        $event->setUserId($user->getId());

        //Create form using UserEventType class
        $form = $this->createForm(new UserEventType(), $event);
        $form->handleRequest($request);

        //if form is valid the event will be created
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            $this->addFlash(
                'success', 'You\'ve created new event!'
            );

            return $this->redirectToRoute('calendar_app_dashboard');
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/app/event/{id}", name="calendar_app_showEvent", requirements={"id" = "\d+"})
     *
     * @Template
     */
    public function showEventAction($id) {

        $em = $this->getDoctrine()->getManager();

        $event = $em->getRepository("CalendarEngineBundle:UserEvent")->findOneById($id);

        $user = $this->get('security.context')->getToken()->getUser();

        //Check whether event belongs to user
        if ($user->getId() !== $event->getUserId()) {
            $this->addFlash(
                'danger', 'This event is not yours!'
            );
            return $this->redirectToRoute('calendar_app_dashboard');
        }

        return [
            "id" => $event->getId(),
            "title" => $event->getEventTitle(),
            "content" => $event->getEventContent(),
            "date" => $event->getDate()
        ];
    }

    /**
     * @Route("/app/event/remove/{id}", name="calendar_app_removeEvent", requirements={"id" = "\d+"})
     *
     * @Template
     */
    public function removeEventAction($id) {
        $user = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository("CalendarEngineBundle:UserEvent")->findOneById($id);

        //Check whether event belongs to user
        if ($user->getId() === $event->getUserId()) {
            $em->remove($event);
            $em->flush();

            $this->addFlash(
                'danger', 'You\'ve successfully removed event!'
            );
        }else{
            $this->addFlash(
                'danger', 'This event is not yours!'
            );
        }

        return $this->redirectToRoute('calendar_app_dashboard');
    }

    /**
     * @Route("/app/event/edit/{id}", name="calendar_app_editEvent", requirements={"id" = "\d+"})
     *
     * @Template
     */
    public function editEventAction($id, Request $request) {
        $user = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository("CalendarEngineBundle:UserEvent")->findOneById($id);

        //Check whether event belongs to user
        if ($user->getId() !== $event->getUserId()) {
            $this->addFlash(
                'danger', 'This event is not yours!'
            );
            return $this->redirectToRoute('calendar_app_dashboard');
        }

        //Create form using UserEventType class
        $form = $this->createForm(new UserEventType(), $event);
        $form->handleRequest($request);

        //if form is valid the event will be created
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            $this->addFlash(
                'success', 'You\'ve successfully edited event!'
            );

            return $this->redirectToRoute('calendar_app_dashboard');
        }

        return array(
            'id' => $event->getId(),
            'form' => $form->createView(),
        );
    }

}
