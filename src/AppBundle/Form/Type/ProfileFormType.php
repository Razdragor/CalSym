<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;


class ProfileFormType extends AbstractType
{
    private $authorization;

    public function __construct(AuthorizationChecker $authorizationChecker)
    {
        $this->authorization = $authorizationChecker;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);


        // Ajoutez vos champs ici, revoilà notre champ *location* :
        $builder->add('firstname', TextType::class);
        $builder->add('lastname', TextType::class);

        var_dump($this->authorization);

        if($this->authorization->isGranted('ROLE_ADMIN'))
        {
            $builder->add('bar');
        }
        $builder->add('role', ChoiceType::class, array(
            'choices'   => array(
                'Patient'   => 'PATIENT',
                'Professionnel' => 'PROF'
            )
        ));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }

    public function getBlockPrefix()
    {
        return 'app_user_profile';
    }

// For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}