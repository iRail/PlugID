<?php include('header.tpl'); ?>

<div class="container">
    
	<div class="hero-unit">
        <?php if( !$error ){ ?>
		    <h1><?php echo $client; ?> requests access</h1>
		    <p>Connecting this app will allow it to do certain things like create new check-ins on your behalf and access personal information such as your profile information, check-in history, friends list, tips and to-dos.</p>
            <form>
		        <p><button class="btn btn-primary btn-large" value="Allow" /></p>
            </form>
        <?php }else{ ?>
            <label class="error-msg"><?php echo $error_msg; ?></label>
        <?php } ?>
	</div>

</div>

<?php
include ('footer.tpl');
?>
