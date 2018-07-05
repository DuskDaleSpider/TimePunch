<div class="form large-9 medium-8 columns content">
	<?= $this->Form->create($settings); ?>
	<fieldset>
		<legend><?=__('Edit Settings'); ?></legend>
		<?php
			echo $this->Form->control('pay_period_days');
			echo $this->Form->control('min_lunch_mins');
			echo $this->Form->control('pp_start_date');
		?>
	</fieldset>
	<?= $this->Form->button(__('Apply')); ?>
	<?= $this->Form->end(); ?>
</div>