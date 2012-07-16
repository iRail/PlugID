<?php include('header.tpl'); ?>

<div class="container">
    <div class="hero-unit">
        <h1>Login</h1>
        <p>
            <!-- Trouble when user comes by for the very first time. Not logged in so no services. Warnings on checking -->
            <?php if(!empty($services)){ ?>
                <?php if (in_array('facebook', $services)) { ?>
                    <span class="btn btn-large">Facebook</span>
                <?php } else { ?>
                    <a href="<?php echo site_url('connect/facebook'); ?>" class="btn btn-large" >Facebook</a>
                <?php } ?>
                <?php if (in_array('twitter', $services)) { ?>
                    <span class="btn btn-large">Twitter</span>
                <?php } else { ?>
                    <a href="<?php echo site_url('connect/twitter'); ?>" class="btn btn-large" >Twitter</a>
                <?php } ?>
                <?php if (in_array('viking', $services)) { ?>
                    <span class="btn btn-large">Vikingspots</span>
                <?php } else { ?>
                    <a href="<?php echo site_url('connect/viking'); ?>" class="btn btn-large" >Vikingspots</a>
                <?php } ?>
                <?php if (in_array('foursquare', $services)) { ?>
                    <span class="btn btn-large">Foursquare</span>
                <?php } else { ?>
                    <a href="<?php echo site_url('connect/foursquare'); ?>" class="btn btn-large" >Foursquare</a>
                <?php } ?>
            <?php } else { ?>
                <a href="<?php echo site_url('connect/facebook'); ?>" class="btn btn-large" >Facebook</a>
                <a href="<?php echo site_url('connect/twitter'); ?>" class="btn btn-large" >Twitter</a>
                <a href="<?php echo site_url('connect/viking'); ?>" class="btn btn-large" >Vikingspots</a>
                <a href="<?php echo site_url('connect/foursquare'); ?>" class="btn btn-large" >Foursquare</a>
            <?php } ?>
        </p>
        </form>
    </div>
</div>

<?php
    include ('footer.tpl');
?>