<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 08/11/2016
 * Time: 20:14
 */

namespace AppBundle\Subscriber;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class AddFormListener implements EventSubscriberInterface {
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT   => 'onPreSubmit',
        );
    }

    public function onPreSetData(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();
        if (null != $event->getData()) {
            var_dump($user->getRole());
        }
//        var_dump($user->getId());


        // Check whether the user from the initial data has chosen to
        // display his email or not.
//        if (true === $user->isGranted()) {
        if (null != $event->getData()) {
            if($user->getRole() == "PROF")
            {

                $form->add('activity', TextType::class);
            }
        }

//        }
    }

    public function onPreSubmit(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();

        if (!$user) {
            return;
        }

        // Check whether the user has chosen to display his email or not.
        // If the data was submitted previously, the additional value that
        // is included in the request variables needs to be removed.
        if (true === $user['show_email']) {
            $form->add('email', EmailType::class);
        } else {
            unset($user['email']);
            $event->setData($user);
        }
    }
}