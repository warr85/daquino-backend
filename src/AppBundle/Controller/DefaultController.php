<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use AppBundle\Entity\User;
use AppBundle\Services\Helpers;
use AppBundle\Services\JwtAuth;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request, UserPasswordEncoderInterface $encoder)
    {

        

        // whatever *your* User object is
        /*$user = new User();
        $plainPassword = 'wilmer';
        $encoded = $encoder->encodePassword($user, $plainPassword);
        
        $user->setUsername("wramones");
        $user->setPlainPassword($plainPassword);
        $user->setEmail("wilmer.ramones@gmail.com");
        $user->setPassword($encoded);    

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();*/

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->findAll();

        $helper = $this->get(Helpers::class);
        return $helper->json(array(
            "Status" => "200",
            "usuarios" => $user
        ));

        /*die();

        return new JsonResponse(array(
            "Status" => "200",
            "usuarios" => $user
        ));

        die();*/


        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }


   


    
}
