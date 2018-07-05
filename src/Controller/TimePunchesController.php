<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;
use Cake\I18n\Date;

class TimePunchesController extends AppController{

	public function index(){
		$this->viewBuilder()->setLayout('myLayout');
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
		$time_punch->date = $this->TimePunches->query()->func()->now('date');
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

		//fetch the minimum lunch time setting
		$this->loadModel('Settings');
		$min_lunch = $this->Settings->find()
			->select(['min_lunch_mins'])
			->where(['Settings.is_active =' => 1])
			->firstOrFail();

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

				//check if the minimum time has passed for the users lunch

				$now = Time::now(); //get current time
				//gets difference between current time and when user started lunch
				$timeDiff = ($now->getTimestamp() - $timePunch->lunch_start->getTimestamp()) / 60;

				//checks if time difference is less than the minimun time needed for lunch
				if($timeDiff < $min_lunch->min_lunch_mins){
					$errorMessage = "You need a minimum of " . $min_lunch->min_lunch_mins . " minutes before ending your lunch.";
					$this->Flash->error(__($errorMessage));
					return $this->redirect('/TimePunches/');
				}

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

	public function view($punch_id){
		//find specific punch
		if($punch_id){
			$punch = $this->TimePunches->get($punch_id);
			//check if this punch belongs to current user
			if($punch->user_id != $this->Auth->user('id')){
				$this->Flash->error(__('That is not your punch'));
				return $this->redirect(['action' => 'index']);
			}

			$this->set('timePunch', $punch);
			return;
		}


		//load open punches
		$this->loadModel('OpenPunches');

		//check if there's an open punch
		$openPunch = $this->OpenPunches->find()
			->where(['user_id =' => $this->Auth->user('id')])
			->first();

		if($openPunch){
			$timePunch = $this->TimePunches->get($openPunch->time_punch_id);

			if($timePunch){
				$this->set('timePunch', $timePunch);
				return;
			}
		}

		//try to find a punch from today
		$timePunch = $this->TimePunches->find()
			->where([
				'date' => $this->TimePunches->query()->func()->now('date')
			])->first();
		if($timePunch){
			$this->set('timePunch', $timePunch);
			return;
		}else{
			$this->Flash->error(__('There have been no punches so far for today'));
			return $this->redirect('/TimePunches/');
		}

	}

	public function payPeriod(){
		//get current payperiod start date

		//fetch pay period length and first pay period start date
		$this->loadModel('Settings');
		$settings = $this->Settings->find()
			->select(['pp_start_date', 'pay_period_days'])
			->where(['is_active =' => 1])
			->firstOrFail();

		//calculate number of days passed since start of first pay period
		$daysPassed = (Date::now()->getTimestamp() - $settings->pp_start_date->getTimestamp())/60/60/24;
		$daysPassed = floor($daysPassed);

		//calculate first day of current pay period
		$daysIntoPayPeriod = $daysPassed % $settings->pay_period_days;
		$firstDay = Date::now()->subDays($daysIntoPayPeriod);

		//get users timepunches starting from the first day of the period
		$timePunches = $this->TimePunches->find()
			->where([
				'date >=' => $firstDay,
				'user_id =' => $this->Auth->user('id')
			]);

		$days = [];

		//calculate hours worked for each day
		foreach($timePunches as $punch){
			$hours = 0;
			$date = $punch->date;
			$punchIn = NULL;
			$lunchStart = NULL;
			$lunchEnd = NULL;
			$punchOut = NULL;

			//get timestamps for punches
			$punchIn = $punch->punch_in->getTimestamp();
			if($punch->lunch_start != NULL){
				$lunchStart = $punch->lunch_start->getTimestamp();
			}
			if($punch->lunch_end != NULL){
				$lunchEnd = $punch->lunch_end->getTimestamp();
			}
			if($punch->punch_out != NULL){
				$punchOut = $punch->punch_out->getTimestamp();
			}

			
			//half day
			if($lunchStart == NULL && $punchOut != NULL){
				$hours += ($punchOut - $punchIn)/60/60;
			}
			//full day
			else if($lunchStart != NULL && $punchOut != NULL){
				$hours += ($lunchStart - $punchIn)/60/60;
				$hours += ($punchOut - $lunchEnd)/60/60;
			}
			//currently on lunch
			else if($lunchStart != NULL && $lunchEnd == NULL){
				$hours += ($lunchStart - $punchIn)/60/60;
			}

			$temp = [];
			$temp['date'] = $date;
			$temp['hours'] = $hours;
			$temp['punch_id'] = $punch->id;
			$days[] = $temp;
		}

		$this->set('days', $days);
	}

	public function isAuthorized(){
		if($this->Auth->user('username')){
			return true;
		}else{
			return false;
		}
	}

}