<?php

namespace App\Controller;

use App\Controller\AppController;

class AdminController extends AppController{

	public function index(){
			
	}

	public function isAuthorized(){
		if($this->Auth->user('username') == 'admin'){
			return true;
		}
		return false;
	}

}