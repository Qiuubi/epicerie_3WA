<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailController extends AbstractController
{
    #[Route('/mail', name: 'app_mail')]
    public function index(MailerInterface $mailer): Response
    {

        $message = (new Email())
            ->from('qiuubi.devtest@gmail.com')
            ->to('quang.nguyen@3wa.io')
            ->subject('test email')
            ->text(
                'Sender : qiuubi.devtest@gmail.com'
            );
        $mailer->send($message);

        return $this->render('mail/index.html.twig', [
            'controller_name' => 'MailController',
        ]);
    }
}
