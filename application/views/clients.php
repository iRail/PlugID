<div class="container">
    <div class="hero-unit">
        <h2>Your authorized applications</h2>
            <?php foreach ($results as $result) { ?>
                <h2><a href="<?php echo site_url('clients/'.$result->client_id) ?>"><?php echo $result->name ?></a></h2>
                <h3>Client Id</h3>
                <span><?php echo $result->client_id ?></span>
                <h3>Client Secret</h3>
                <span><?php echo $result->client_secret ?></span><br />
                <h3>Callback</h3>
                <span><?php echo $result->redirect_uri ?></span><br />
                <a href="<?php echo site_url('clients/edit/'.$result->client_id) ?>">Edit this user</a>
                <hr />
            <?php } ?>
    </div>
</div>