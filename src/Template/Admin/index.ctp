<?php
	//paths
	$addUserPath = $this->Url->build('/users/add', true);
	$logoutPath = $this->Url->build('/users/logout', true);
	$settingsPath = $this->Url->build('/settings/edit', true);
?>
<button onclick="redirect('<?= $addUserPath?>');">Add User</button>
<button onclick="redirect('<?= $logoutPath?>');">Logout</button>
<button onclick="redirect('<?= $settingsPath ?>');">Settings</button>
<script>
	function redirect(path){
		window.location.href = path;
	}
</script>