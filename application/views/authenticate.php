<?php include('header.tpl'); ?>

<div class="container">

	<div class="hero-unit">
		<h1><?php echo $client; ?> requests access</h1>
		<p>Connecting this app will allow it to do certain things like create new check-ins on your behalf and access personal information such as your profile information, check-in history, friends list, tips and to-dos.</p>
		<p><a href="<?php echo $callback; ?>" class="btn btn-primary btn-large">Allow</a></p>
	</div>

</div>

<?php
include ('footer.tpl');
?>