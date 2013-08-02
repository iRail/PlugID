<div class="container">
    <div class="hero-unit">
        <h2>Your applications</h2>
            <?php foreach ($results as $result) { ?>
                <h2><a href="<?php echo site_url('developer/apps/edit/'.$result->client_id) ?>"><?php echo $result->name ?></a></h2>
                <h3>Client ID</h3>
                <span><?php echo $result->client_id ?></span>
                <h3>Client secret</h3>
                <span><?php echo $result->client_secret ?></span><br />
                <h3>Push secret</h3>
                <span><?php echo $result->notify_secret ?></span><br />
                <a href="<?php echo site_url('developer/apps/edit/'.$result->client_id) ?>">Edit this user</a>
                <hr />
            <?php } ?>
    </div>
</div>