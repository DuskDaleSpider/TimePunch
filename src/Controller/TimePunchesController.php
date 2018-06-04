<?php

namespace App\Controller;

use App\Controller\AppController;

class TimePunchesController extends AppController{

	public function index(){
		$this->viewBuilder()->setLayout('myLayout');
		$this->Flash->default(__('You are on the TimePunches index'));
	}

	public function isAuthorized(){
		if($this->Auth->user('username')){
			return true;
		}else{
			return false;
		}
	}

}