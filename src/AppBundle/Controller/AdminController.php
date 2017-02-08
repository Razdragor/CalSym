<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityManager;

class AdminController extends Controller {


    /**
     * @Route("/admin/test", name="admin_app_dashboard")
     */
    public function dashboardAction(Request $request)
    {

        // replace this example code with whatever you need
        return $this->render('admin/dashboard.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..')
        ]);
    }

}
