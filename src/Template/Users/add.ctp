<div class="users form large-9 medium-8 columns content">
	<?= $this->Form->create($user); ?>
	<fieldset>
		<legend><?= __('Add User'); ?></legend>
		<?php
			echo $this->Form->control('username');
			echo $this->Form->control('password');
			echo $this->Form->control('first_name');
			echo $this->Form->control('last_name');
		?>
	</fieldset>
	<?= $this->Form->button(__('Submit')); ?>
	<?= $this->Form->end(); ?>
</div>