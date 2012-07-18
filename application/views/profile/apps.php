<div class="container">
    <div class="hero-unit">
        <h2>Your authorized applications</h2>
            <?php foreach ($results as $result) { ?>
                <h2><?php echo $result->name ?></h2>
                <a class="btn" href="<?php echo site_url('profile/apps/revoke/'.$result->client_id) ?>">Revoke</a>
                <hr />
            <?php } ?>
    </div>
</div>