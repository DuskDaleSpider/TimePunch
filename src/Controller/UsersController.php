<?php

namespace App\Controller;

use App\Controller\AppController;

class UsersController extends AppController{

	public function initialize(){
		parent::initialize();
	}

	public function login(){
		$user = null;
		if($this->request->is('post')){
			$user = $this->Auth->identify();
			if($user){
				$this->Auth->setUser($user);
				$this->Flash->success(__('You have been logged in'));
				if($user['username'] == "admin"){
					return $this->redirect(['controller' => 'Admin', 'action' => 'index']);
				}else{
					return $this->redirect(['controller' => 'TimePunches', 'action' => 'index']);
				}
			}else{
			$this->Flash->error(__('Your username or password is incorrect'));
			}
		}
		$this->set('color', 'red');
	}

	public function logout(){
		$this->Flash->success(__('You are now logged out'));
		return $this->redirect($this->Auth->logout());
	}

	public function add(){
		$user = $this->Users->newEntity();
		if($this->request->is('post')){
			$user = $this->Users->patchEntity($user, $this->request->getData());
			if($this->Users->save($user)){
				$this->Flash->success(__('The user has been saved.'));

				return $this->redirect(['action' => 'login']);
			}
			$this->Flash->error(__('The user could not bew saved. Please try again.'));
		}
		$this->set(compact('user'));
	}
}