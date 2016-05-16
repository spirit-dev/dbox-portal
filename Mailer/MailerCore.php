<?php
/**
 * Copyright (c) 2016. Spirit-Dev
 * Licensed under GPLv3 GNU License - http://www.gnu.org/licenses/gpl-3.0.html
 *    _             _
 *   /_`_  ._._/___/ | _
 * . _//_//// /   /_.'/_'|/
 *    /
 *    
 * Since 2K10 until today
 *  
 * Hex            53 70 69 72 69 74 2d 44 65 76
 *  
 * By             Jean Bordat
 * Twitter        @Ji_Bay_
 * Mail           <bordat.jean@gmail.com>
 *  
 * File           MailerCore.php
 * Updated the    16/05/16 14:54
 */

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

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;
    /**
     * @var RouterInterface
     */
    protected $router;
    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var
     */
    protected $adminMail;
    /**
     * @var
     */
    protected $subjectPrepend;
    /**
     * @var
     */
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

        $this->adminMail = $container->getParameter('spirit_dev_d_box_portal.app.admin_mail');
        $this->subjectPrepend = $container->getParameter('spirit_dev_d_box_portal.app.subject_prepend');
        $this->fromMail = $container->getParameter('spirit_dev_d_box_portal.app.from_mail');
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