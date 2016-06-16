<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
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
        // var_dump($request->files->all());
        // var_dump($request->request->all());exit;
        $form = $this->createFormBuilder(null, array('csrf_protection' => false))
            ->add('description', TextType::class)
            ->add('trip_id',IntegerType::class)
            ->add('user_id', IntegerType::class)
            ->add('img', FileType::class)
            // ->add('users', CollectionType::class, array('entry_type' => IntegerType::class))
            ->getForm();

        $form->submit(array_merge($request->request->all(),$request->files->all()));
        if ($form->isValid()) {
            $data = $form->getData();

            $file = $data['img'];
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move(
                "/tmp",
                $fileName
            );

            $post = new Post();
            $post->setDescription($data['description']);
            $post->setTripId($data['trip_id']);
            $post->setUserId($data['user_id']);
            $post->setUsers(array(1,2,3));
            $post->setUrl($fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

        }

        foreach ($form->getErrors(true, false) as $error) {
            echo 'la';
            var_dump($error->getMessage());
        }
        echo 'la';exit;
        exit;

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
