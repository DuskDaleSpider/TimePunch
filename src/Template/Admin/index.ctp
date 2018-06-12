<?php
	//paths
	$addUserPath = $this->Url->build('/users/add', true);
	$logoutPath = $this->Url->build('/users/logout', true);
?>
<button onclick="redirect('<?= $addUserPath?>');">Add User</button>
<button onclick="redirect('<?= $logoutPath?>');">Logout</button>
<script>
	function redirect(path){
		window.location.href = path;
	}
</script>