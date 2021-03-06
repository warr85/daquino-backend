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
                $email = (isset($params->email) ? $params->email : null);
                $memberships = (isset($params->topics) ? $params->topics : null);

                $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
                $password = substr( str_shuffle( $chars ), 0, 8 );
                
                                
                if($username != null && $email != null ){

                    $em = $this->getDoctrine()->getManager()->getConnection();
                    $sth = $em->prepare("select public.sp_user_create('$username','$password', '$email')");
                    $exec = $sth->execute();
                    

                    if($exec){ 
                        $em = $this->getDoctrine()->getManager()->getConnection();
                        $sth = $em->prepare("select groname from pg_group");
                        $exec = $sth->execute();
                        while($result = $sth->fetch()){
                            $r[] = $result;
                        }                        
                        
                        $message = (new \Swift_Message('UpperData System Registration'))
                            ->setFrom('wilmer.ramones@gmail.com')
                            ->setTo($email)
                            ->setBody(
                                $this->renderView(
                                    // app/Resources/views/Emails/registration.html.twig
                                    'Emails/registration.html.twig',
                                    array(
                                        'name' => $username,
                                        'password' => $password
                                    )
                                ),
                                'text/html'
                            )
                            /*
                            * If you also want to include a plaintext version of the message
                            ->addPart(
                                $this->renderView(
                                    'Emails/registration.txt.twig',
                                    array('name' => $name)
                                ),
                                'text/plain'
                            )
                            */
                        ;

                        $this->get('mailer')->send($message);

                        if($r != null ){                     
                            foreach($r as $roles){                                
                                $rol = $roles['groname'];
                                $sth = $em->prepare("select sp_membership_grant_insert('$rol','$username')");
                                $exec = $sth->execute();
                            }

                            foreach($memberships as $mem){
                                $sth = $em->prepare("select sp_membership_grant('$mem','$username')"); 
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
            $all = $em->getRepository("AppBundle:Uds001")->findAll();

            /*$paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,                             
                $request->query->getInt('page', $page)  
                10                                   
            );*/

           // var_dump($pagination->getTotalItemCount()); die();

            $data = array(
                'status' => "success",
                'code' => 200,
                'users' => $all,
                /*'total_users' => $pagination->getTotalItemCount(),
                'page' => $request->query->getInt('page', 1),
                'total_pages' => ($pagination->getTotalItemCount() / 5),
                'all' => $all*/
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
            $uds006 = array(
                'id' => $user["iduds006"]                              
            );
            $user["iduds006"] = $uds006;            
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
     * @Route("/security/user/searchByDescription/{description}", name="show_user_by_description", methods={"POST"}))     
     */

    public function searchByDescriptionAction($description, Request $request){
        $helper = $this->get(Helpers::class);
        $jwt = $this->get(JwtAuth::class);

        $json = $request->get("json", null);
        $params = json_decode($json);
        $token = $request->get("authorization", null);
        

        if($token && $jwt->checkToken($token)){
            $identity = $jwt->checkToken($token, true); 
            $con = $this->getDoctrine()->getManager()->getConnection();

            $sth = $con->prepare("select * from uds001 where description = '$description'");
            $sth->execute();
            $user = $sth->fetch();
            if($user){
                $uds006 = array(
                    'id' => $user["iduds006"]                              
                );
                $user["iduds006"] = $uds006;            
                $result = [];
                $username = $user["description"];                        
                $data = array(
                    'status' => "success",
                    'code' => 200,
                    'user' => $user                
                );   
            }else{
                $data = array(
                    'status' => "error",
                    'code' => 400,
                    'msg' => "User not found!"
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
     * @Route("/security/user/update/permission", name="update_user_permission", methods={"POST"}))     
     */

    public function UpdatePermissionAction(Request $request){
        $helper = $this->get(Helpers::class);
        $jwt = $this->get(JwtAuth::class);

        $json = $request->get("json", null);
        $params = json_decode($json);
        $token = $request->get("authorization", null);
        
        
        $username = (isset($params->description) ? $params->description : null);
        $password = (isset($params->password) ? $params->password : null);
        $memberships = (isset($params->topics) ? $params->topics : null);        
        
        if($token && $jwt->checkToken($token)){
            $identity = $jwt->checkToken($token, true); 
            $em = $this->getDoctrine()->getManager()->getConnection();

            if(isset($password)){
                $sth2 = $em->prepare("select pass_change('$username','$password')");
                $sth2->execute();    
            }

            if(isset($memberships)){
                
                $sth2 = $em->prepare("select groname as value from pg_group");
                $sth2->execute();            
                while($roles = $sth2->fetch()){
                    $membership = $roles["value"];                    
                    $sth = $em->prepare("select sp_membership_revoke('$membership','$username')"); 
                    $exec = $sth->execute();
                }   

                
                foreach($memberships as $mem){
                    
                    $sth = $em->prepare("select sp_membership_grant('$mem','$username')"); 
                    $exec = $sth->execute();
                    $r[] = $sth->fetch();
                }
                
                $data = array(
                    'status' => "success",
                    'code' => 200,
                    'user' => $exec                
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
     * @Route("/security/user/all/roles", name="user_all_roles", methods={"POST"}))     
     */

    public function getRolesAction(Request $request){
        $helper = $this->get(Helpers::class);
        $jwt = $this->get(JwtAuth::class);

        $token = $request->get("authorization", null);

        if($token && $jwt->checkToken($token)){
            $identity = $jwt->checkToken($token, true); 
            $em = $this->getDoctrine()->getManager()->getConnection();
            $sth = $em->prepare("select groname as value from pg_group");
            $exec = $sth->execute();            
            while($roles = $sth->fetch()){
                $r[]["value"] = $roles["value"]                ;
            }            
            
            $data = array(
                'status' => "success",
                'code' => 200,
                'topics' => $r                
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
     * @Route("/security/user/disable/{id}", name="user_disable", methods={"POST"}))     
     */

    public function disableAction($id, Request $request){
        $helper = $this->get(Helpers::class);
        $jwt = $this->get(JwtAuth::class);

        $token = $request->get("authorization", null);

        if($token && $jwt->checkToken($token)){
            $identity = $jwt->checkToken($token, true); 
            $em = $this->getDoctrine()->getManager()->getConnection();
            $sth = $em->prepare("select sp_disable_user($id)");
            $exec = $sth->execute(); 
            
            $em = $this->getDoctrine()->getManager();
            $dql   = "SELECT u FROM AppBundle:Uds001 u WHERE u.id = $id";
            $query = $em->createQuery($dql); 
            $user = $query->setMaxResults(1)->getOneOrNullResult();
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
     * @Route("/security/user/enable/{id}", name="user_enable", methods={"POST"}))     
     */

    public function enableAction($id, Request $request){
        $helper = $this->get(Helpers::class);
        $jwt = $this->get(JwtAuth::class);

        $token = $request->get("authorization", null);

        if($token && $jwt->checkToken($token)){
            $identity = $jwt->checkToken($token, true); 
            $em = $this->getDoctrine()->getManager()->getConnection();
            $sth = $em->prepare("select sp_enable_user($id)");
            $exec = $sth->execute();                                  
            
            $em = $this->getDoctrine()->getManager();
            $dql   = "SELECT u FROM AppBundle:Uds001 u WHERE u.id = $id";
            $query = $em->createQuery($dql); 
            $user = $query->setMaxResults(1)->getOneOrNullResult();
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
     * @Route("/security/user/reset/{description}", name="reset_user", methods={"POST"}))     
     */

    public function resetAction($description, Request $request){
        $helper = $this->get(Helpers::class);
        $jwt = $this->get(JwtAuth::class);

        $token = $request->get("authorization", null);

        if($token && $jwt->checkToken($token)){
            $identity = $jwt->checkToken($token, true); 
            $em = $this->getDoctrine()->getManager()->getConnection();
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
            $passwd = substr( str_shuffle( $chars ), 0, 8 );
            $sth = $em->prepare("select pass_change('$description', '$passwd')");
            $exec = $sth->execute();   
            
            $em = $this->getDoctrine()->getManager();
            $dql   = "SELECT u FROM AppBundle:Uds001 u WHERE u.description = '$description'";
            $query = $em->createQuery($dql); 
            $user = $query->setMaxResults(1)->getOneOrNullResult();
            
            $emailTo = $user->getEmail();

            $message = (new \Swift_Message('UpperData System Password Update'))
            ->setFrom('wilmer.ramones@gmail.com')
            ->setTo($emailTo)
            ->setBody(
                $this->renderView(
                    // app/Resources/views/Emails/registration.html.twig
                    'Emails/passwordReset.html.twig',
                    array(
                        'name' => $user->getDescription(),
                        'password' => $passwd
                    )
                ),
                'text/html'
            )
            /*
            * If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'Emails/registration.txt.twig',
                    array('name' => $name)
                ),
                'text/plain'
            )
            */
        ;

        $this->get('mailer')->send($message);



            $data = array(
                'status' => "success",
                'code' => 200, 
                'passwd' => $passwd                             
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
