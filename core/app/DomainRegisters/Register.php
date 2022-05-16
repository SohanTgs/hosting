<?php

namespace App\DomainRegisters;

class Register{

	public $alias;
	public $domain;
	public $request;
	public $command;

	public function __construct($alias){		
		$this->alias = $alias;
	}

	public function run(){
		$getFile =  $this->getApiMethods($this->alias);

		$object = new $getFile($this->domain);
		$command = $this->command;

		$object->request = $this->request;
		$object->domain = $this->domain;
		return $object->$command();
	}

	protected function getApiMethods($alias){

		$methods = [
			'Namecheap'=>Namecheap::class
		];

		return $methods[$alias];
	}


}

