<div class="container">
    <div class="hero-unit">
        <h2>Your authorized applications</h2>
            <?php foreach ($results as $result) { ?>
                <h2><?php echo $result->name ?></h2>
                <h3>Client Id</h3>
                <span><?php echo $result->client_id ?></span>
                <h3>Client Secret</h3>
                <span><?php echo $result->client_secret ?></span><br />
                <h3>Callback</h3>
                <span><?php echo $result->redirect_uri ?></span><br />
                <hr />
            <?php } ?>
    </div>
</div>