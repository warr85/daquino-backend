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


    
}
