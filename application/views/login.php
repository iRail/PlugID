<?php include('header.tpl'); ?>

<div class="container">
    <div class="hero-unit">
        <h1>Login</h1>
        <p>
            <a href="<?php echo base_url('connect/facebook'); ?>" class="btn btn-large" >Facebook</a>
            <a href="<?php echo base_url('connect/twitter'); ?>" class="btn btn-large" >Twitter</a>
            <a href="<?php echo base_url('connect/vikingspots'); ?>" class="btn btn-large" >Vikingspots</a>
            <a href="<?php echo base_url('connect/foursquare'); ?>" class="btn btn-large" >Foursquare</a>
        </p>
        </form>
    </div>
</div>

<?php
    include ('footer.tpl');
?>