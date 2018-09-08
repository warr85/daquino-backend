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

        

       
    }



    /**
     * @Route("/security/user/new", name="new_user", methods={"POST"}))     
     */

    public function newAction(Request $request, UserPasswordEncoderInterface $encoder){
        $helper = $this->get(Helpers::class);
        $jwt = $this->get(JwtAuth::class);

        $json = $request->get("json", null);
        $params = json_decode($json);
        
        

        if($json != null){
            $createdAt = new  \Datetime("now");
            $username = (isset($params->username) ? $params->username : null);
            $password = (isset($params->password) ? $params->password : null);
            $token = $request->get("authorization", null);
        
            if($token && $jwt->checkToken($token)){                        
                if($username != null && $password != null ){

                    $em = $this->getDoctrine()->getManager()->getConnection();
                    $sth = $em->prepare("select public.sp_user_create('$username', '$password');");
                    $exec = $sth->execute();
                    

                    if($exec){                        
                        $data = array(
                            'status'    => "success",
                            'code'      => "200",
                            'msg'       => $exec
                        );
                    }else{
                        $data = array(
                            'status'    => "error",
                            'code'      => "400",
                            'msg'       => "Username Already Exists!!"
                        );
                    }

                    
                }else{
                    $data = array(
                        'status'    => "error",
                        'code'      => "400",
                        'msg'       => "user not created, Empty fields!!",
                    );
                }
            }else{
                $data = array(
                    'status'    => "error",
                    'code'      => "400",
                    'msg'       => "Not auth",
                );
            }
        }

        return $helper->json($data);

    }



    /**
     * @Route("/security/user/list", name="list_user", methods={"POST"}))     
     */

    public function ListAction(Request $request){
        $helper = $this->get(Helpers::class);
        $jwt = $this->get(JwtAuth::class);

        $json = $request->get("json", null);
        $params = json_decode($json);
        $token = $request->get("authorization", null);
        
        if($token && $jwt->checkToken($token)){
            $identity = $jwt->checkToken($token, true); 
            $em = $this->getDoctrine()->getManager();
            $dql   = "SELECT u FROM AppBundle:User u";
            $query = $em->createQuery($dql);

            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1)/*page number*/,
                10/*limit per page*/
            );

            $data = array(
                'status' => "success",
                'code' => 200,
                'users' => $pagination,
                'total_users' => $pagination->getTotalItemCount(),
                'page' => $request->query->getInt('page', 1),
                'total_pages' => ($pagination->getTotalItemCount() / 10)
            );          
        }else{
            $data = array(
                'status' => "error",
                'code' => 400,
                'msg' => "You are not auth"
            );   
        }
        

        

        return $helper->json($data);

    }


    /**
     * @Route("/security/user/show/{id}", name="show_user", methods={"POST"}))     
     */

    public function showAction($id, Request $request){
        $helper = $this->get(Helpers::class);
        $jwt = $this->get(JwtAuth::class);

        $json = $request->get("json", null);
        $params = json_decode($json);
        $token = $request->get("authorization", null);
        

        if($token && $jwt->checkToken($token)){
            $identity = $jwt->checkToken($token, true); 
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository("AppBundle:User")->findOneById($id);            

            $data = array(
                'status' => "success",
                'code' => 200,
                'user' => $user,               
            );          
        }else{
            $data = array(
                'status' => "error",
                'code' => 400,
                'msg' => "You are not auth"
            );   
        }
        

        

        return $helper->json($data);

    }



    /**
     * @Route("/security/user/checkusername/{username}", name="checkusername", methods={"POST"}))     
     */

    public function usernameTakenAction($username, Request $request){
        $helper = $this->get(Helpers::class);
        $jwt = $this->get(JwtAuth::class);

        $json = $request->get("json", null);
        $params = json_decode($json);
        $token = $request->get("authorization", null);
        

        if($token && $jwt->checkToken($token)){
            $identity = $jwt->checkToken($token, true); 
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository("AppBundle:User")->findOneByUsername($username);            
            if($user){
                $data = array(
                    'status' => "success",
                    'code' => 200,                                
                );  
            }else{
                $data = array(
                    'status' => "error",
                    'code' => 400,                                 
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


    /**
     * @Route("/security/user/search/{search}", name="search_user", methods={"POST"}))     
     */

    public function searchAction(Request $request){
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
