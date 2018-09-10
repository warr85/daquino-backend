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
        $token = $request->get("authorization", null);
        $params = json_decode($json);
        
        
        
        if($json != null){
            if($token && $jwt->checkToken($token)){
                
                $createdAt = new  \Datetime("now");
                $username = (isset($params->description) ? $params->description : null);
                $password = (isset($params->password) ? $params->password : null);
                $memberships = (isset($params->topics) ? $params->topics : null);
                
                                
                if($username != null && $password != null ){

                    $em = $this->getDoctrine()->getManager()->getConnection();
                    $sth = $em->prepare("select public.sp_user_create('$username','$password.')");
                    $exec = $sth->execute();
                    

                    if($exec){  
                        if($memberships != null ){                     
                            foreach($memberships as $membership){
                                $sth = $em->prepare("select sp_membership_grant_insert('$membership','$username')");
                                $exec = $sth->execute();
                            }

                            if($exec){
                                $em = $this->getDoctrine()->getManager();
                                $dql   = "SELECT u FROM AppBundle:Uds001 u WHERE u.description = '$username'";
                                $query = $em->createQuery($dql);

                                $user = $query->setMaxResults(1)->getOneOrNullResult();
                                $data = array(
                                    'status'    => "success",
                                    'code'      => "200",
                                    'msg'       => $exec,
                                    'user'      => $user
                                );
                            }else{
                                $data = array(
                                    'status'    => "error",
                                    'code'      => "400",
                                    'msg'       => "Could not Create Permision!!"
                                ); 
                            }   
                        }                
                                             
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
                    'msg'       => "not Auth",
                );
            }
        }else{
            $data = array(
                'status'    => "error",
                'code'      => "400",
                'msg'       => "Bad headers & Params",
            );
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
        $page = $request->get("page", 1);
        

        
        if($token && $jwt->checkToken($token)){
            $identity = $jwt->checkToken($token, true); 
            $em = $this->getDoctrine()->getManager();
            $dql   = "SELECT u FROM AppBundle:Uds001 u";
            $query = $em->createQuery($dql);

            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,                             /* query NOT result */
                $request->query->getInt('page', $page)  /*page number*/,
                5                                   /*limit per page*/
            );

           // var_dump($pagination->getTotalItemCount()); die();

            $data = array(
                'status' => "success",
                'code' => 200,
                'users' => $pagination,
                'total_users' => $pagination->getTotalItemCount(),
                'page' => $request->query->getInt('page', 1),
                'total_pages' => ($pagination->getTotalItemCount() / 5)
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
            $con = $this->getDoctrine()->getManager()->getConnection();

            $sth = $con->prepare("select * from uds001 where id = $id");
            $sth->execute();
            $user = $sth->fetch(); 
            
            $result = [];
            $username = $user["description"];
            $user['membership'] = [];
            $sth = $con->prepare("select column3 as name from sp_membershipselect(0,0,'',0, '$username')");
            $sth->execute();
            while($r = $sth->fetch()){ 
               //$result[] = $r;
               //var_dump($r);
               array_push($user['membership'], $r['name']);
            }            
            //die();
            //if($result) $user['membership'] = $result;
            //if(!$result) $result = $user;
            $data = array(
                'status' => "success",
                'code' => 200,
                'user' => $user                
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
     * @Route("/security/user/permission/{username}/{role}", name="get_user_permission", methods={"POST"}))     
     */

    public function permissionAction($username, $role, Request $request){
        $helper = $this->get(Helpers::class);
        $jwt = $this->get(JwtAuth::class);

        $json = $request->get("json", null);
        $params = json_decode($json);
        $token = $request->get("authorization", null);
                
        if($token && $jwt->checkToken($token)){
            $identity = $jwt->checkToken($token, true); 
            $con = $this->getDoctrine()->getManager()->getConnection();

            $sth = $con->prepare("select id from uds001 where description = '$username'");
            $sth->execute();
            $userid = $sth->fetch(); 
            $userid = $userid['id'];
            
                        
            
            $sth = $con->prepare("select id from uds0201 where iduds001 = '$userid' and iduds002 = '$role'");
            $sth->execute();
            $roleid = $sth->fetch(); 
            //var_dump($roleid); die();

                 
            
            if($roleid){
                $data = array(
                    'status' => "success",
                    'code' => 200,
                    'role' => true                
                );
            }else{
                $data = array(
                    'status' => "success",
                    'code' => 200,
                    'role' => false                
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
            $user = $em->getRepository("AppBundle:Uds001")->findOneByDescription($username);            
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
