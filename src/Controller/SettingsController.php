<?php

namespace App\Controller;

use App\Controller\AppController;

class SettingsController extends AppController{

	public function edit(){
		$settings = $this->Settings->find('all')
			->where(['Settings.is_active = ' => 1])
			->firstOrFail();
		if($this->request->is(['post', 'put'])){
			$this->Settings->patchEntity($settings, $this->request->getData());
			if($this->Settings->save($settings)){
				$this->Flash->success(__('Settings have been saved'));
				return $this->redirect(['controller' => 'Admin', 'action' => 'index']);
			}
			$this->Flash->error(__('Settings could not be saved. Please try again.'));
		}

		$this->set('settings', $settings);
	}

	public function isAuthorized(){
		if($this->Auth->user('username') == 'admin'){
			return true;
		}
		return false;
	}

}