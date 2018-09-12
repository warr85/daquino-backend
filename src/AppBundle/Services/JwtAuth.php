<?php
namespace AppBundle\Services;

use Firebase\JWT\JWT;

Class JwtAuth{
	
	public $manager;
	public $container;
	public $key;

	function __construct($manager, $container){
		$this->manager = $manager;
		$this->container = $container;
		$this->key = "a6sd5f496adf4sa32sd1";
	}

	public function signup($username, $password, $getToken){

		 $conn = array(
            'driver'   => 'pdo_pgsql',
            'user'     => $username,
            'password' => $password,
            'dbname'   => 'daquino',
            'port'	=> null,
            'host' => 'localhost'
        );

		 

        $new = \Doctrine\ORM\EntityManager::create(
            $conn,
            $this->manager->getConfiguration(),
            $this->manager->getEventManager()
        );
           

		$this->manager = $new;
		$data = array();
			
		$em = $this->manager->getConnection();
		$sth = $em->prepare("select * from sp_membershipselect(0,0,'',0,'$username')");
		$sth->execute();
		$r = [];
		$id="";
		while($result = $sth->fetch()){
			$id = $result['column1'];
			$r[] = md5($result['column3']);
		}
		//var_dump($r); die;


		if($r) {
			//$encoderService = $this->container->get('security.password_encoder');
			//if($match = $encoderService->isPasswordValid($userBd, $password)){	

				$token = array(
                    "sub" => $id,
                    "description" => $username,
                    "roles" => $r,
                    "iat" => time(),
                    "exp" => time() + (24 * 60 * 60)
                );

                $jwt = JWT::encode($token, $this->key, "HS256");
                $decoded = JWT::decode($jwt, $this->key, array("HS256"));
                $data = $getToken ? $decoded : $jwt;
                //var_dump($getToken); die();
				
			/*}else{
				$data = array(
					"status" => "error",
					"msg" => "login failed"
				);
			}*/
		}
		
		return $data;

	}

	public function checkToken($jwt, $getIdentity = false){
		$auth = false;
		try{
			$decoded = JWT::decode($jwt, $this->key, array("HS256"));
		}catch(\UnexpectedValueException $e){
			return false;
		}catch(\DomainException $e){
			return false;
		}

		if(is_object($decoded) && isset($decoded->sub)){
			$auth = true;
		}else{
			$auth = false;
		}

		if($getIdentity) return $decoded;

		return $auth;
	}
}