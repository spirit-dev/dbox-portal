<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Mailer;

use SpiritDev\Bundle\DBoxPortalBundle\Entity\Demand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class Mailer
 * @package SpiritDev\Bundle\DBoxPortalBundle\Mailer
 */
class MailerCore {

    protected $mailer;
    protected $router;
    protected $templating;

    protected $adminMail;
    protected $subjectPrepend;
    protected $fromMail;

    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param RouterInterface $router
     * @param EngineInterface $templating
     * @param ContainerInterface $container
     */
    public function __construct(\Swift_Mailer $mailer, RouterInterface $router, EngineInterface $templating, ContainerInterface $container) {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;

        $this->adminMail = $container->getParameter('app')['admin_mail'];
        $this->subjectPrepend = $container->getParameter('app')['subject_prepend'];
        $this->fromMail = $container->getParameter('app')['from_mail'];
    }

    /**
     * Function dedicated to sending admin mail for new demands
     * @param Demand $demand
     * @param $subject
     */
    protected function sendNewDemandAdminMail(Demand $demand, $subject) {
        // Define necessary vars
        $template = 'SpiritDevDBoxPortalBundle:Mailer/Demand:adminRegistration.html.twig'; // Template
        $subject = $this->getSubject($subject); // Subject
        $demandUrl = $this->router->generate('spirit_dev_dbox_portal_bundle_demand', array('id' => $demand->getId()), true);
        $rendered = $this->templating->render($template, array(
            'demand' => $demand,
            'demand_url' => $demandUrl
        )); // Template rendering
        // Send Mail
        $this->sendEmailMessage($rendered, $subject, $this->adminMail);
    }

    /**
     * Define mail message subject
     * @param $subject
     * @return string
     */
    protected function getSubject($subject) {
        return $this->subjectPrepend . ' ' . $subject;
    }

    /**
     * Send mail message effectively
     * @param $renderedTemplate
     * @param $subjectEmail
     * @param $toEmail
     */
    protected function sendEmailMessage($renderedTemplate, $subjectEmail, $toEmail) {

        $mail = \Swift_Message::newInstance();

        // Attach variable content
        $mail
            ->setFrom($this->fromMail)
            ->setTo($toEmail)
            ->setSubject($subjectEmail)
            ->setBody($renderedTemplate);

        // Attach necessary contents
        $mail->setContentType('text/html');

        // Send mail
        $this->mailer->send($mail);

    }

}