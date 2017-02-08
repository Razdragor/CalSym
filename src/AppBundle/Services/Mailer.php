<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 08/02/2017
 * Time: 17:17
 */

namespace AppBundle\Services;

use AppBundle\Entity\User;
use Symfony\Component\Templating\EngineInterface;

class Mailer
{
    protected $mailer;
    protected $templating;
    private $from = "no-reply@example.fr";
    private $reply = "contact@example.fr";
    private $name = "Equipe Acme Blog";

    public function __construct($mailer,EngineInterface  $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    protected function sendMessage($to, $subject, $body)
    {
        $mail = \Swift_Message::newInstance();

        $mail
            ->setFrom($this->from,$this->name)
            ->setTo($to)
            ->setSubject($subject)
            ->setBody($body)
            ->setReplyTo($this->reply,$this->name)
            ->setContentType('text/html');

        $this->mailer->send($mail);
    }

    protected function sendMessageRequest(User $user, $array = array()){
        $to = $user->getEmail();
        $subject = "Demande de rendez vous";
        $body = $this->templating->render('default/top_articles.html.twig', $array);

        $this->sendMessage($to,$subject,$body);
    }
}
