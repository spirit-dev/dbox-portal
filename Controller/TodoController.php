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
 * File           TodoController.php
 * Updated the    15/05/16 11:47
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TodoController
 * @package SpiritDev\Bundle\DBoxPortalBundle\Controller
 */
class TodoController extends Controller {

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/todos", name="spirit_dev_dbox_portal_bundle_todos")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getEntityManager();

        $nbTodos = $em->getRepository('SpiritDevDBoxPortalBundle:Todo')->getNbTodos();

        if ($nbTodos > 0) {

            $todos = $em->getRepository('SpiritDevDBoxPortalBundle:Todo')->findAll();

            return array(
                'todos' => $todos
            );
        } else {
            return new RedirectResponse($this->container->get('router')->generate('spirit_dev_dbox_portal_bundle_homepage'));
        }
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/todos/menu", name="spirit_dev_dbox_portal_bundle_todos_menu")
     */
    public function menuAction() {
        $em = $this->getDoctrine()->getEntityManager();

        $nbTodos = $em->getRepository('SpiritDevDBoxPortalBundle:Todo')->getNbTodos();

        return $this->render('SpiritDevDBoxPortalBundle:Common:menu_todos.html.twig', array('nb_todos' => $nbTodos));

    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/todos/remove", name="spirit_dev_dbox_portal_bundle_todos_remove")
     */
    public function deleteTodos(Request $request) {
        $todosList = json_decode($request->request->get('todos'));

        for ($i = 0; $i < count($todosList); $i++) {
            $this->get('spirit_dev_dbox_portal_bundle.todos.manager')->deleteTodo($todosList[$i]);
        }

        return new RedirectResponse($this->container->get('router')->generate('spirit_dev_dbox_portal_bundle_todos'));
    }

}