<?php
	//build redirect urls
	$logoutPath = $this->Url->build('/users/logout', true);
	$punchInPath = $this->Url->build('/TimePunches/punchIn', true);
	$lunchStartPath = $this->Url->build('/TimePunches/lunchStart', true);
	$lunchEndPath = $this->Url->build('/TimePunches/lunchEnd', true);
	$punchOutPath = $this->Url->build('/TimePunches/punchOut', true);
	$viewPunchPath = $this->Url->build('/TimePunches/view', true);
?>

<!-- TODO: Move style to own stylesheet -->
<style>
	.section-container{
		padding: 20px;
		margin-bottom: 10px;
	}
	body{
		margin-top: 30px;
	}
	.section-title{
		padding-bottom: 10px;
	}
</style>

<div class = "container border rounded section-container">
	<div class="row">
		<h3 class="section-title">Punch Options:</h3>
	</div>
	<div class="row text-center">
		<div class="col">
			<a class="btn btn-primary" href="<?= $punchInPath ?>">Punch In</a>
		</div>
		<div class="col">
			<a class="btn btn-primary" href="<?= $lunchStartPath ?>">Lunch Start</a>
		</div>
		<div class="col">
			<a class="btn btn-primary" href="<?= $lunchEndPath ?>">Lunch End</a>
		</div>
		<div class="col">
			<a class="btn btn-primary" href="<?= $punchOutPath ?>">Punch Out</a>
		</div>
	</div>
</div>

<div class="container border rounded section-container">
	<div class="row">
		<h3 class="section-title">User Options</h3>
	</div>
	<div class="row text-center">
		<div class="col">
			<a class="btn btn-primary" href="<?=$viewPunchPath?>">View Punches</a>
		</div>
		<div class="col">
			<a class="btn btn-primary" href="<?=$logoutPath?>">Log out</a>
		</div>
	</div>
</div>