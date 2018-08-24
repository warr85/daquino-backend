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

class UserController extends Controller
{
    /**
     * @Route("/admin/users", name="user_homepage")
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


    /**
     * @Route("/login", name="login", methods={"POST"}))     
     */

    public function loginAction(Request $request){
        $helper = $this->get(Helpers::class);
        //lo que viene
        $json = $request->get("json", null);

       

        if($json != null){
            $params = json_decode($json);
            $username = (isset($params->username) ? $params->username : null);
            $plainPassword = (isset($params->password) ? $params->password : null);
            $hashed = (isset($params->hashed) ? $params->hashed : false);
            

            if($username != null && $plainPassword != null){
                $jwt = $this->get(JwtAuth::class);
                
                if($signup = $jwt->signup($username, $plainPassword, $hashed)){


                    $data = array(
                        'status' => "success",
                        'data' => "success",
                        'token' => $signup
                    );
                    return $this->json($signup) ;
                    //var_dump($signup); die();
                }else{
                    $data = array(
                        'status' => "error",
                        'data' => "Password error"
                    );
                }


            }else{
                $data = array(
                    'status' => "error",
                    'data' => "username or password invalid"
                );
            }
            

        }else{
            $data = array(
                'status' => "error",
                'data' => "send json via post"
            );
        }

        return $helper->json($data);
    }


    /**
     * @Route("/login_check", name="login_check", methods={"POST"}))     
     */

    public function loginCheckAction(Request $request){
        $helper = $this->get(Helpers::class);
        $jwt = $this->get(JwtAuth::class);

        $token = $request->get("authorization", null);

        if($token && $jwt->checkToken($token)){
            $data = array(
                'status' => "success",
                'data' => "send json via post"
            );
        }else{
            $data = array(
                'status' => "error",
                'data' => "send json via post"
            );
        }

        return $helper->json($data);

    }


    /**
     * @Route("/security/user/new", name="new_user", methods={"POST"}))     
     */

    public function newUserAction(Request $request){
        $helper = $this->get(Helpers::class);
        $jwt = $this->get(JwtAuth::class);

        $json = $request->get("json", null);
        $params = json_decode($json);
        
        

        if($json != null){
            $createdAt = new  \Datetime("now");
            $username = (isset($params->username) ? $params->username : null);
            $password = (isset($params->password) ? $params->password : null);
            $group = (isset($params->group) ? $params->group : null);
            $membership = (isset($params->membership) ? $params->membership : null);

            if($username != null && $password != null && $group != null && $membership != null){
                $em = $this->getDoctrine()->getManager()->getConnection();
                $sth = $em->prepare("select * from sp_membershipselect(0,0,0,'wilmer','',1)");
                $sth->execute();
                $result = $sth->fetch();
                $data = array(
                    'status'    => "success",
                    'code'      => "200",
                    'msg'       => $result
                );
            }else{
                $data = array(
                    'status'    => "error",
                    'code'      => "400",
                    'msg'       => "user not created",
                );
            }
        }

        return $helper->json($data);

    }


    /**
     * @Route("/security/user/search/{search}", name="search_user", methods={"POST"}))     
     */

    public function searchUserAction(Request $request){
        $helper = $this->get(Helpers::class);
        $jwt = $this->get(JwtAuth::class);

        $token = $request->get("authorization", null);

        if($token && $jwt->checkToken($token)){
            $identity = $jwt->checkToken($token, true);           
           

            if ($search != null){
                $em = $this->getDoctrine()->getManager()->getConnection();
                $sth = $em->prepare("select * from sp_membershipselect(0,0,0,'$filter','',1)");
                $sth->execute();
                $result = $sth->fetch();
                $data = array(
                    'status'    => "success",
                    'code'      => "200",
                    'msg'       => $result
                );
            }else{
                $data = array(
                    'status' => "error",
                    'code' => 400,
                    'msg' => "You are not auth"
                );  
            }
        }else{
            $data = array(
                'status' => "error",
                'code' => 400,
                'msg' => "You are not auth"
            );            
        }

        return $helper->json($data);
    }
}
