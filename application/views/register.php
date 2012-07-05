<?php 
    include('header.tpl'); 
    $this->load->helper('form');
?>

<div class="container">
	<div class="hero-unit">
		    <h1>requests access</h1>
            <p>
                <form method="post">
		        <label>Application name</label>
                <?php echo form_input('name', isset($name)?$name:''); ?>
                <label>Callback url</label>
                <?php echo form_input('callback', isset($callback)?$callback:''); ?>
                <button class="btn btn-primary" type="submit" name="register" >Register</button>
            </p>
            </form>
	</div>
</div>

<?php include ('footer.tpl'); ?>
