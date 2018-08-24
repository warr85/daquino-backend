<?php
namespace AppBundle\Services;


Class Helpers{
		public $manager;

		public function __construct($manager){
			$this->manager = $manager;
		}

		public function json($data){
			$normalizer = array(new \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer());
			$encoder = array("json" => new \Symfony\Component\Serializer\Encoder\JsonEncoder());

			$serializer = new \Symfony\Component\Serializer\Serializer($normalizer, $encoder);

			$json = $serializer->serialize($data, "json");

			$response = new \Symfony\Component\HttpFoundation\Response();
			$response->setContent($json);
			$response->headers->set("Content-Type", "application/json");
			// Allow all websites
		    $response->headers->set('Access-Control-Allow-Origin', '*');
		   $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');    

			return $response;


		}

}