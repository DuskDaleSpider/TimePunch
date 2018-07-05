<div class="row text-center">
	<div class="col">
		<h3>Hours from pay period</h3>
	</div>
</div>
<div class="row text-center">
	<div class="col">
		<table>
			<tr>
				<th>Date</th>
				<th>Hours</th>
			</tr>
			<?php foreach($days as $day){
				$url = $this->Url->build('/TimePunches/view/'.$day['punch_id'], true);
			 ?>
				<tr>
					<td>
						<a href="<?=$url;?>">
							<?= $day['date']->format('Y-m-d'); ?>
						</a>
						</td>
					<td><?= $day['hours']; ?></td>
				</tr>
			<?php } //end foreach ?>
		</table>
	</div>
</div>