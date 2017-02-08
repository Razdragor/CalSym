<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 08/11/2016
 * Time: 20:14
 */

namespace AppBundle\Subscriber;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class AddFormListener implements EventSubscriberInterface {
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'onPreSetData',
        );
    }

    public function onPreSetData(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();

        if (null != $event->getData()) {
            if($user->getRole() == "PROF")
            {
                $form->add('activity', EntityType::class, array(
                    'class' => 'AppBundle:Activity',
                    'choice_label' => 'name',
                ));
                $form->add('address', TextType::class);
                $form->add('description', TextType::class);

            }
        }
    }


}