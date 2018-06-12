<?php

namespace App\Controller;

use App\Controller\AppController;

class TimePunchesController extends AppController{

	public function index(){
		$this->viewBuilder()->setLayout('myLayout');
		$this->Flash->default(__('You are on the TimePunches index'));
	}

	public function punchIn(){
		//load open punches
		$this->loadModel('OpenPunches');
		//check if this user has any open punches and redirect them if they have an open punch
		$openPunch = $this->OpenPunches->find()
			->where(['user_id =' => $this->Auth->user('id')])
			->first();
		if($openPunch){
			$this->Flash->error(__('You are already clocked in'));
			return $this->redirect(['controller' => 'TimePunches', 'action' => 'index']);
		}

		//build timepunch entity
		$user_id = $this->Auth->user('id');
		
		$time_punch = $this->TimePunches->newEntity();

		$time_punch->user_id = $user_id;
		$time_punch->punch_in = $this->TimePunches->query()->func()->now();

		//save timepunch
		if($this->TimePunches->save($time_punch)){
			//build an open punch entity
			$time_punch_id = $time_punch->id;
			$openPunch = $this->OpenPunches->newEntity();
			$openPunch->user_id = $user_id;
			$openPunch->time_punch_id = $time_punch_id;

			//save open punch
			if($this->OpenPunches->save($openPunch)){
				$this->Flash->success(__('You have been clocked in!'));
				return $this->redirect(['controller' => 'TimePunches', 'action' => 'index']);
			}else{
				//if an open punch couldn't be created
				//delete the time punch just created and give an error
				$this->TimePunches->delete($time_punch);
				$this->Flash->error(__('Unable to create an Open Punch. Please try again.'));
				return $this->redirect(['controller' => 'TimePunches', 'action' => 'index']);
			}
		}else{
			//if a time punch couldn't be created, spit out an error message
			$this->Flash->error(__('Unable to create a Time Punch. Please try again.'));
			return $this->redirect(['controller' => 'TimePunches', 'action' => 'index']);
		}
	}

	public function lunchStart(){
		//load open punches
		$this->loadModel('OpenPunches');

		//find an open punch for this user
		$openPunch = $this->OpenPunches->find()
			->where(['user_id =' => $this->Auth->user('id')])
			->first();
		if($openPunch){
			//find the currenttimepunch
			$timePunch = $this->TimePunches->get($openPunch->time_punch_id);

			//check if they have already started lunch
			if($timePunch->lunch_start){
				$this->Flash->error(__('You have already started your lunch'));
				return $this->redirect('/TimePunches/');
			}

			//add lunch_start date
			$timePunch->lunch_start = $this->TimePunches->query()->func()->now();

			//save the punch
			if($this->TimePunches->save($timePunch)){
				$this->Flash->success(__('You have started lunch!'));
				return $this->redirect('/TimePunches/');
			}else{
				$this->Flash->error(__('Unable to update time punch. Please try again.'));
				return $this->redirect('/TimePunches/');
			}

		}else{
			$this->Flash->error(__("You haven't clocked in yet."));
			return $this->redirect('/TimePunches/');
		}
	}

	public function lunchEnd(){
		//load open punches
		$this->loadModel('OpenPunches');

		//find an open punch for this user
		$openPunch = $this->OpenPunches->find()
			->where(['user_id =' => $this->Auth->user('id')])
			->first();
		if($openPunch){
			//find the current timepunch
			$timePunch = $this->TimePunches->get($openPunch->time_punch_id);

			//check if they have clocked out for lunch
			if($timePunch->lunch_start){
				//check if they have already clocked in from lunch
				if($timePunch->lunch_end){
					$this->Flash->error(__('You have already ended your lunch'));
					return $this->redirect('/TimePunches/');
				}

				//add lunch end date
				$timePunch->lunch_end = $this->TimePunches->query()->func()->now();

				//save the timepunch
				if($this->TimePunches->save($timePunch)){
					$this->Flash->success(__('You have ended your lunch!'));
					return $this->redirect('/TimePunches/');
				}else{
					//spit out an error
					$this->Flash->error(__("Couldn't update your punch. Please try again."));
					return $this->redirect('/TimePunches/');
				}
			}else{
				//spit out an error
				$this->Flash->error(__('You have not started lunch yet'));
				return $this->redirect('/TimePunches/');
			}
		}else{
			//spit out an error
			$this->Flash->error(__('You have not clocked in yet'));
			return $this->redirect('/TimePunches/');
		}
	}

	public function punchOut(){
		//load OpenPunches
		$this->loadModel('OpenPunches');

		//find an open punch for the user
		$openPunch = $this->OpenPunches->find()
			->where(['user_id =' => $this->Auth->user('id')])
			->first();

		if($openPunch){
			//find the time punch
			$timePunch = $this->TimePunches->get($openPunch->time_punch_id);

			//check if they had a lunch and if they did, make sure they clocked back in
			if($timePunch->lunch_start){
				if(!$timePunch->lunch_end){
					$this->Flash->error(__("You haven't ended your lunch"));
					return $this->redirect('/TimePunches/');
				}
			}

			//add the punch out
			$timePunch->punch_out = $this->TimePunches->query()->func()->now();

			//save time punch and close the open punch
			if($this->TimePunches->save($timePunch)){
				$this->OpenPunches->delete($openPunch);
				$this->Flash->success(__('You have clocked out'));
				return $this->redirect('/TimePunches/');
			}else{
				$this->Flash->error(__("Unable to update time punch. Please try again."));
				return $this->redirect('/TimePunches/');
			}

		}else{
			$this->Flash->error(__('You have not clocked in yet'));
			return $this->redirect('/TimePunches/');
		}

	}

	public function isAuthorized(){
		if($this->Auth->user('username')){
			return true;
		}else{
			return false;
		}
	}

}