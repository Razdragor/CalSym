<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 08/02/2017
 * Time: 23:40
 */

namespace AppBundle\Services;


use AppBundle\Entity\UserEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\Container;

class UserEventService
{
    protected $em;
    private $container;

    // We need to inject this variables later.
    public function __construct(EntityManager $entityManager, Container $container)
    {
        $this->em = $entityManager;
        $this->container = $container;
    }

    public function checkUserEventDate(UserEvent $userEvent) {
        $qb = $this->em->createQueryBuilder();

        $qb->select('u')
            ->from('AppBundle:UserEvent', 'u')
            ->where('u.date = :date')
            ->andWhere('u.proId = :proId')
            ->setParameter('date',$userEvent->getDate())
            ->setParameter('proId', $userEvent->getProId());

        $result = $qb->getQuery()->getResult();

        if(count($result)){
            return true;
        }

        return false;
    }
}