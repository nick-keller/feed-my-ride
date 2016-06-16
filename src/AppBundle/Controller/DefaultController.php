<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Post;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/api/toto", name="test")
     */
    public function testAction(Request $request)
    {
        return array("foo" => "bar");
    }


    /**
     * @Route("/api/post", name="post_action")
     * @Method({"POST"})
     */
    public function postAction(Request $request)
    {
        $post = new Post();
        $post->setDescription("description");
        $post->setTripId(1);
        $post->setUserId(2);
        $post->setUsers(array(1,2,3));
        $post->setUrl("/tmp/to/img.jpeg");

        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        return array();
    }
}
