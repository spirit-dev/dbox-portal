<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

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