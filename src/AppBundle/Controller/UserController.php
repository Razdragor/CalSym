<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\UserEvent;
use AppBundle\Form\Type\UserEventType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserController extends Controller {


    /**
     * @Route("/app", name="calendar_app_dashboard")
     */
    public function dashboardAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('user/dashboard.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..')
        ]);
    }

    /**
     * @Route("/event/add", name="calendar_app_addEvent")
     *
     */
    public function addEventAction(Request $request) {

        $user = $this->getUser();

        // create an event and give it some initial data
        $event = new UserEvent();
        $event->setDate(new \DateTime("today"));
        $event->setIsactive(false);

        $event->setUserId($user);


        //Create form using UserEventType class

        $form = $this->createFormBuilder($event)
            ->add('eventTitle', TextType::class)
            ->add('eventContent', TextType::class)
            ->add('date', DateTimeType ::class,array('minutes' => array(0,30)))
            ->add('save', SubmitType::class, array('label' => 'Save Event'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userEventService = $this->get('user_event.service');
            if($userEventService->checkUserEventDate($event)){
                $this->addFlash(
                    'danger', 'An appointment already exists at this time!'
                );

                return $this->render('user/addEvent.html.twig', array(
                    'form' => $form->createView(),
                ));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            $this->addFlash(
                'success', 'You\'ve created new event!'
            );

            return $this->redirectToRoute('calendar_app_dashboard');
        }

        return $this->render('user/addEvent.html.twig', array(
            'form' => $form->createView(),
        ));
        //if form is valid the event will be created

    }

    /**
     * @Route("/event/add/{id}", name="calendar_app_addEventbyID", requirements={"id" = "\d+"})
     * @Security("has_role('PATIENT')")
     */
    public function addEventIdAction(Request $request,$id) {

        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $userpro = $em->getRepository("AppBundle:User")->findOneById($id);


        // create an event and give it some initial data
        $event = new UserEvent();
        $event->setDate(new \DateTime("today"));
        $event->setIsactive(false);

        $event->setUserId($user);
        $event->setProId($userpro);




        //Create form using UserEventType class

        $form = $this->createFormBuilder($event)
            ->add('eventTitle', TextType::class)
            ->add('eventContent', TextType::class)
            ->add('date', DateTimeType ::class)
            ->add('save', SubmitType::class, array('label' => 'Save Event'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userEventService = $this->get('user_event.service');
            if($userEventService->checkUserEventDate($event)){
                $this->addFlash(
                    'danger', 'An appointment already exists at this time!'
                );

                return $this->render('user/addEvent.html.twig', array(
                    'form' => $form->createView(),
                ));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            $this->addFlash(
                'success', 'You\'ve created new event!'
            );


            return $this->redirectToRoute('calendar_app_dashboard');
        }

        return $this->render('user/addEvent.html.twig', array(
            'form' => $form->createView(),
        ));
        //if form is valid the event will be created

    }

    /**
     * @Route("/event/{id}", name="calendar_app_showEvent", requirements={"id" = "\d+"})
     *
     * @Template
     */
    public function showEventAction($id) {

        $em = $this->getDoctrine()->getManager();

        $event = $em->getRepository("AppBundle:UserEvent")->findOneById($id);

        $user = $this->getUser();

//        var_dump($event->getProId());
        //Check whether event belongs to user

        if($event->getProId())
        {
            if ($user != $event->getProId()) {
                $this->addFlash(
                    'danger', 'This event is not yours!'
                );
                return $this->redirectToRoute('calendar_app_dashboard');
            }
        }
        else{
            if ($user != $event->getUserId()) {
                $this->addFlash(
                    'danger', 'This event is not yours!'
                );
                return $this->redirectToRoute('calendar_app_dashboard');
            }
        }


        return $this->render('user/showEvent.html.twig', array(
            "id" => $event->getId(),
            "title" => $event->getEventTitle(),
            "content" => $event->getEventContent(),
            "date" => $event->getDate()
        ));
    }

    /**
     * @Route("/event/remove/{id}", name="calendar_app_removeEvent", requirements={"id" = "\d+"})
     *
     * @Template
     */
    public function removeEventAction($id) {

        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository("AppBundle:UserEvent")->findOneById($id);

        //Check whether event belongs to user
        if($event->getProId())
        {
            if ($user != $event->getProId()) {
                $this->addFlash(
                    'danger', 'This event is not yours!'
                );
                return $this->redirectToRoute('calendar_app_dashboard');
            }
        }
        else{
            if ($user != $event->getUserId()) {
                $this->addFlash(
                    'danger', 'This event is not yours!'
                );
                return $this->redirectToRoute('calendar_app_dashboard');
            }
        }


        return $this->redirectToRoute('calendar_app_dashboard');
    }

    /**
     * @Route("/event/edit/{id}", name="calendar_app_editEvent", requirements={"id" = "\d+"})
     *
     * @Template
     */
    public function editEventAction($id, Request $request) {

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $event = $em->getRepository("AppBundle:UserEvent")->findOneById($id);

        //Check whether event belongs to user
        if($event->getProId())
        {
            if ($user != $event->getProId()) {
                $this->addFlash(
                    'danger', 'This event is not yours!'
                );
                return $this->redirectToRoute('calendar_app_dashboard');
            }
        }
        else{
            if ($user != $event->getUserId()) {
                $this->addFlash(
                    'danger', 'This event is not yours!'
                );
                return $this->redirectToRoute('calendar_app_dashboard');
            }
        }


        $form = $this->createFormBuilder($event)
            ->add('eventTitle', TextType::class)
            ->add('eventContent', TextType::class)
            ->add('date', DateTimeType ::class)
            ->add('isactive', ChoiceType::class, array('choices' => array('Validé' => true, 'En attente' => false)))
            ->add('save', SubmitType::class, array('label' => 'Save Event'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            $this->addFlash(
                'success', 'You\'ve successfully edited event!'
            );

            return $this->redirectToRoute('calendar_app_dashboard');
        }

        return $this->render('user/editEvent.html.twig', array(
            'id' => $event->getId(),
            'form' => $form->createView(),
        ));

    }

    /**
     * @Route("/event/list", name="calendar_app_listEvent")
     * @Template
     */
    public function listEventAction(Request $request) {

        if($user = $this->getUser())
        {
            $user = $this->getUser();


            $em = $this->getDoctrine()->getManager();

            $repository = $this->getDoctrine()->getRepository('AppBundle:UserEvent');


            $events = $repository->findBy(
                array('userId' => $user->getId(),
                )
            );

        }
        else{
            $events = null;
        }


         return $this->render('user/listEvent.html.twig', array(
            'events' => $events
        ));

    }


    /**
     * @Route("/listp", name="calendar_app_listPro")
     */
    public function listProAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $repository = $this->getDoctrine()->getRepository('AppBundle:User');

        $activities = $em->getRepository("AppBundle:Activity")->findAll();

        $users= $repository->findBy(
            array('role' => "PROF")
        );

        return $this->render('user/listpro.html.twig', array(
            'users' => $users,
            'activities' => $activities
        ));

    }

    /**
     * @Route("/search", name="calendar_app_search")
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        $repository = $this->getDoctrine()->getRepository('AppBundle:User');

        $repositoryActivity = $this->getDoctrine()->getRepository('AppBundle:Activity');
        $activities = $em->getRepository("AppBundle:Activity")->findAll();



        $address = $request->request->get('address');

        if($request->request->get('activity') != 0 && $request->request->get('address') != "")
        {
            $activity = $repositoryActivity->findOneById($request->request->get('activity'));

            $userpro = $repository->createQueryBuilder('user')
                ->where('user.address LIKE :address')
                ->andWhere('user.activity = :activity')
                ->setParameter("address", '%'.$address.'%')
                ->setParameter("activity", $activity)
                ->getQuery()->getResult();

        }
        elseif($request->request->get('activity') == 0 && $request->request->get('address') != "")
        {
            $userpro = $repository->createQueryBuilder('user')
                ->where('user.address LIKE :address')
                ->setParameter("address", '%'.$address.'%')
                ->getQuery()->getResult();
        }
        elseif($request->request->get('activity') != 0 && $request->request->get('address') == "")
        {
            $activity = $repositoryActivity->findOneById($request->request->get('activity'));

            $userpro = $repository->createQueryBuilder('user')
                ->where('user.activity = :activity')
                ->setParameter("activity", $activity)
                ->getQuery()->getResult();
        }
        else{
            $activities = $em->getRepository("AppBundle:Activity")->findAll();

            $userpro = $repository->findBy(
                array('role' => "PROF")
            );

        }


        return $this->render('user/listpro.html.twig', array(
            'users' => $userpro,
            'activities' => $activities
        ));

    }

    /**
     * @Route("/listp/show/{id}", name="calendar_app_showPro", requirements={"id" = "\d+"})
     */
    public function showProAction($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository("AppBundle:User")->findOneById($id);

        return $this->render('user/showPro.html.twig', array(
            'pro' => $user
        ));


    }


}
