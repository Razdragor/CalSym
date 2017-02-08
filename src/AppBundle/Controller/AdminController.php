<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\User;
use AppBundle\Entity\UserEvent;
use AppBundle\Form\Type\UserEventType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityManager;

class AdminController extends Controller
{

    /**
     * @Route("/admin/user/list", name="admin_listUser")
     *
     * @Template
     */
    public function adminListUsersAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $repository = $this->getDoctrine()->getRepository('AppBundle:User');

        $users = $repository->findAll();

        return $this->render('admin/listUser.html.twig', array(
            'users' => $users
        ));
    }

    /**
     * @Route("/admin/user/edit/{id}", name="admin_editPatientUser", requirements={"id" = "\d+"})
     *
     * @Template
     */
    public function adminEditPatientUserAction($id, Request $request) {

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository("AppBundle:User")->findOneById($id);
        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class)
            ->add('email', TextType::class)
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('isActivate', ChoiceType::class, array('choices' => array('Oui' => 1, 'Non' => Null)))
            //->add('role', ChoiceType::class, array('choices' => array('Admin' => "ROLE_ADMIN", 'Professionel' => "PROF", "Patient"=>"PATIENT")))
            ->add('save', SubmitType::class, array('label' => 'Save User'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success', 'You\'ve successfully edited event!'
            );

            return $this->redirectToRoute('admin_listUser');
        }

        return $this->render('admin/editPatientUser.html.twig', array(
            'id' => $user->getId(),
            'form' => $form->createView(),
        ));

    }

    /**
     * @Route("/admin/event/list", name="admin_calendar_app_listEvent")
     *
     * @Template
     */
    public function adminListEventAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $repository = $this->getDoctrine()->getRepository('AppBundle:UserEvent');

        $events = $repository->findAll();

        return $this->render('admin/listEvent.html.twig', array(
            'events' => $events
        ));
    }

    /**
     * @Route("/admin/event/edit/{id}", name="admin_calendar_app_editEvent", requirements={"id" = "\d+"})
     *
     * @Template
     */
    public function adminEditEventAction($id, Request $request) {

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $event = $em->getRepository("AppBundle:UserEvent")->findOneById($id);
        /*
        $users = $em->getRepository("AppBundle:User")->findBy(
            array('role' => array("PROF","ROLE_ADMIN"))
        );
        $user_tab = array();
        $user_tab['choices'] = array();
        foreach($users as $key=> $user)
        {
            $user_tab['choices'][$user->getUsername()] = $key;
        }*/
        $form = $this->createFormBuilder($event)
            //->add('userId', ChoiceType::class, $user_tab)
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

            return $this->redirectToRoute('admin_calendar_app_listEvent');
        }

        return $this->render('admin/editEvent.html.twig', array(
            'id' => $event->getId(),
            'form' => $form->createView(),
            'userId' => $event->getUserId(),
        ));

    }

    /**
     * @Route("/admin/event/remove/{id}", name="admin_calendar_app_removeEvent", requirements={"id" = "\d+"})
     *
     * @Template
     */
    public function removeEventAction($id) {

        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository("AppBundle:UserEvent")->findOneById($id);


            $em->remove($event);
            $em->flush();

            $this->addFlash(
                'danger', 'You\'ve successfully removed event!'
            );

        return $this->redirectToRoute('admin_calendar_app_listEvent');
    }



    /**
     * @Route("/admin", name="admin_default")
     *
     * @Template
     */
    public function AdminAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        return $this->render('admin/index.html.twig');
    }

}
