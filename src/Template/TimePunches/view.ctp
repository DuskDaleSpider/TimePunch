<h1>Punches for <?= $timePunch->date ?></h1>

<table>
	<tr>
		<th>Type</th>
		<th>Time</th>
	</tr>
	<tr>
		<td>Punch In</td>
		<td><?php if($timePunch->punch_in) echo $timePunch->punch_in; ?></td>
	</tr>
	<tr>
		<td>Lunch Start</td>
		<td><?php if($timePunch->lunch_start) echo $timePunch->lunch_start; ?></td>
	</tr>
	<tr>
		<td>Lunch End</td>
		<td><?php if($timePunch->lunch_end) echo $timePunch->lunch_end; ?></td>
	</tr>
	<tr>
		<td>Punch Out</td>
		<td><?php if($timePunch->punch_out) echo $timePunch->punch_out; ?></td>
	</tr>
</table>