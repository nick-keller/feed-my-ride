<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Forms;

class DefaultController extends Controller
{
    private $em;

    /**
     * Get all the posts where a User is tagged in
     *
     * @Route("/api/users/{userId}", name="find_by_user")
     * @Method({"GET"})
     *
     * @ApiDoc()
     * @param $userId
     * @return Post[]
     */
    public function findByUserAction($userId)
    {
        return $this->em()->getRepository('AppBundle:User')->findById($userId)->getPosts();
    }

    /**
     * Get all the posts of a feed
     *
     * @Route("/api/trips/{tripId}", name="find_by_trip")
     * @Method({"GET"})
     *
     * @ApiDoc()
     * @param $tripId
     * @return Post[]
     */
    public function findByTripAction($tripId)
    {
        return $this->em()->getRepository('AppBundle:Post')->findByTripId($tripId);
    }


    /**
     * Add a Post to the trip feed
     *
     * @Route("/api/post", name="post_action")
     * @Method({"POST"})
     *
     * @ApiDoc(
     *  parameters={
     *      {"name"="trip_id", "dataType"="string", "required"=true},
     *      {"name"="author_id", "dataType"="string", "required"=true},
     *      {"name"="img", "dataType"="file", "required"=false},
     *      {"name"="text", "dataType"="string", "required"=false},
     *      {"name"="users", "dataType"="string", "required"=false, "format"="comma separated IDs", "description"="Ids of people tagged in the photo"},
     *  }
     * )
     * @param Request $request
     * @return Post
     */
    public function postAction(Request $request)
    {
        $post = new Post();
        $file = $request->files->get('img');

        if ($file) {
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(__DIR__.'/../../../web/uploads', $fileName);
            $post->setImageUrl('/uploads/'.$fileName);
        }

        $author = $this->em()->getRepository('AppBundle:User')->findById($request->request->get('author_id'));

        $post->setAuthor($author);
        $post->setText($request->request->get('text'));
        $post->setTripId($request->request->get('trip_id'));

        foreach ($this->em()->getRepository('AppBundle:User')->findByIds(explode(',', $request->request->get('users'))) as $user) {
            $post->addUser($user);
        }

        $this->em()->persist($post);
        $this->em()->flush();

        return $post;
    }

    /**
     * Create a user in the DB
     *
     * @Route("/api/users", name="create_user")
     * @Method({"POST"})
     *
     * @ApiDoc(
     *  parameters={
     *      {"name"="id", "dataType"="string", "required"=true},
     *      {"name"="display_name", "dataType"="string", "required"=true},
     *      {"name"="picture", "dataType"="string", "required"=true},
     *  }
     * )
     * @param Request $request
     * @return User
     */
    public function createUser(Request $request)
    {
        $user = $this->em()->getRepository('AppBundle:User')->findById($request->request->get('id'));

        if (is_null($user)) {
            $user = new User($request->request->get('id'), $request->request->get('display_name'), $request->request->get('picture'));
            $this->em()->persist($user);
            $this->em()->flush();
        }

        return $user;
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
