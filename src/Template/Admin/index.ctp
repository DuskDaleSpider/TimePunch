<?php
	//paths
	$addUserPath = $this->Url->build('/users/add', true);

?>
<button onclick="redirect('<?= $addUserPath?>');">Add User</button>
<script>
	function redirect(path){
		window.location.href = path;
	}
</script>