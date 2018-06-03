<style>
h1{
	color: <?php echo $color; ?>
}
</style>
<h1>login</h1>
<?php
	echo $this->Form->create();
	echo $this->Form->control('username');
	echo $this->Form->control('password');
	echo $this->Form->Button('Login');
	echo $this->Form->end();
?>