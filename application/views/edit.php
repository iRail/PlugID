<div class="container">
    <div class="hero-unit">
        <h2>Your registered applications</h2>
        <?php if ($version === 'view') { ?>
            <?php foreach ($results as $result) { ?>
                <h2><a href="<?php echo site_url('edit/'.$result->client_id) ?>"><?php echo $result->name ?></a></h2>
                <h3>Client Id</h3>
                <span><?php echo $result->client_id ?></span>
                <h3>Client Secret</h3>
                <span><?php echo $result->client_secret ?></span><br />
                <a href="<?php echo site_url('edit/'.$result->client_id) ?>">Edit this user</a>
                <hr />
            <?php } ?>
        <?php } ?>
        <?php if ($version === 'edit') { ?>
            <form method='post'>
                <label>Name:</label>
                <?php echo form_input('name', isset($item->name) ? $item->name : ''); ?>
                <button class="btn btn-primary" type="submit" name="updateName">Update name</button>
                <label>ClientId:</label>
                <?php echo form_input('client_id', isset($item->client_id) ? $item->client_id : ''); ?>
                <label>ClientSecret:</label>
                <?php echo form_input('client_secret', isset($item->client_secret) ? $item->client_secret : ''); ?>
                <button class="btn btn-primary" type="submit" name="reset">Reset secret</button>
                <label>Callback URL:</label>
                <?php echo form_input('redirect_uri', isset($item->redirect_uri) ? $item->redirect_uri : ''); ?>
                <button class="btn btn-primary" type="submit" name="updateUri">Update callback url</button> 
                <hr />
            </form>
        <?php } ?>
    </div>
</div>