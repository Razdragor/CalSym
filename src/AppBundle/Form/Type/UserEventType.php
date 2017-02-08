<?php

namespace Calendar\EngineBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserEventType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder -> add('eventTitle', 'text')
            ->add('eventContent', 'textarea')
            ->add('date', 'collot_datetime', array('pickerOptions' =>
                array(
                    'format' => 'yy-mm-dd hh:ii', //24h date format
                    'minuteStep' => 15,
                )
            ))
            ->add('save', 'submit', array('label' => 'Save Event'))
        ;
    }

    public function getName() {
        return 'userEvent';
    }

}
