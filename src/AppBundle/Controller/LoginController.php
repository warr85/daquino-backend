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

class LoginController extends Controller
{
    
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
                        'data' => "Username or Password are incorrect"
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
     * @Route("/reset_instructions/{description}", name="login_reset_instructions", methods={"POST"}))     
     */

    public function resetInstructionAction($description, Request $request){
        $helper = $this->get(Helpers::class);
        $jwt = $this->get(JwtAuth::class);

        $json = $request->get("json", null);
        

        if($json != null){
            

            $em = $this->getDoctrine()->getManager();
            $dql   = "SELECT u FROM AppBundle:Uds001 u WHERE u.description = '$description'";
            $query = $em->createQuery($dql); 
            $user = $query->setMaxResults(1)->getOneOrNullResult();
            if($user){
                $emailTo = $user->getEmail();
                $username = $user->getDescription();                
                $userEncripted = md5($username);
                $date = new \DateTime();            
                $date = md5($date->format("Y-m-d"));
                $link = "http://gage.vps.co.ve/backend/web/redirected?un=" . $userEncripted . "&d=" . $date ; 
                $message = (new \Swift_Message('UpperData Password Reset Instructions'))
                ->setFrom('wilmer.ramones@gmail.com')
                ->setTo($emailTo)
                ->setBody(
                    $this->renderView(
                        // app/Resources/views/Emails/loginReset.html.twig
                        'Emails/loginReset.html.twig',
                        array(
                            'username' => $username,
                            'link' => $link
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
                    'email' => $emailTo
                );
            }else{
                $data = array(
                    'status' => "error",
                    'msg' => "User not Found"
                );
            }
        }else{
            $data = array(
                'status' => "error",
                'msg' => "send json via post"
            );
        }

        return $helper->json($data);

    }


     /**
     * @Route("/redirected", name="login_reset", methods={"GET"}))     
     */

    public function resetPasswordAction(Request $request){
        $username = $request->query->get('un', null);
        $dateGet = $request->query->get('d', null);
        $date = new \DateTime();            
        $date = md5($date->format("Y-m-d"));
        if ($date == $dateGet){
            $em = $this->getDoctrine()->getManager();
            $dql   = "SELECT u FROM AppBundle:Uds001 u";
            $query = $em->createQuery($dql); 
            $users = $query->getResult();
            $userFound = false;
            foreach($users as $user){
                //var_dump($user->getDescription());
                if(md5($user->getDescription()) == $username){
                    $userFound = true;
                    $emailTo = $user->getEmail();
                    $name = $user->getDescription();
                    break;
                }
            }

            if($userFound){

                //var_dump($name); die();
                $em = $this->getDoctrine()->getManager()->getConnection();
                $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
                $passwd = substr( str_shuffle( $chars ), 0, 8 );
                $sth = $em->prepare("select pass_change('$name', '$passwd')");
                $exec = $sth->execute(); 

                $message = (new \Swift_Message('UpperData Password Reseted'))
                ->setFrom('wilmer.ramones@gmail.com')
                ->setTo($emailTo)
                ->setBody(
                    $this->renderView(
                        // app/Resources/views/Emails/loginReseted.html.twig
                        'Emails/loginReseted.html.twig',
                        array(
                            'username' => $name,
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
            return $this->redirect('http://gage.vps.co.ve/daquino-prod/login');
        }




        }
        

    }


    
}
