<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    private $em;

    /**
     * @Route("/api/users/{userId}", name="find_by_user")
     * @param $userId
     * @return Post[]
     */
    public function findByUserAction($userId)
    {
        return $this->em()->getRepository('AppBundle:User')->findById($userId)->getPosts();
    }

    /**
     * @Route("/api/trips/{tripId}", name="find_by_trip")
     * @param $tripId
     * @return Post[]
     */
    public function findByTripAction($tripId)
    {
        return $this->em()->getRepository('AppBundle:Post')->findByTripId($tripId);
    }


    /**
     * @Route("/api/post", name="post_action")
     * @Method({"POST"})
     */
    public function postAction(Request $request)
    {
        $post = new Post();
        $post->setText("description");
        $post->setTripId(1);
        $post->setUserId(2);
        $post->setUsers(array(1,2,3));
        $post->setImageUrl("/tmp/to/img.jpeg");

        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        return array();
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    private function em()
    {
        if (is_null($this->em)) {
            $this->em = $this->getDoctrine()->getManager();
        }

        return $this->em;
    }
}
