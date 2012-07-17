<style type="text/css">
    input {
        width:250px;
    }
</style>
<div class="container">
    <div class="hero-unit">
        <h2>Your authorized applications</h2>
        <?php if ($version === 'view') { ?>
            <?php foreach ($results as $result) { ?>
                <h2><a href="<?php echo site_url('consumer/'.$result->client_id) ?>"><?php echo $result->name ?></a></h2>
                <h3>Client Id</h3>
                <span><?php echo $result->client_id ?></span>
                <h3>Client Secret</h3>
                <span><?php echo $result->client_secret ?></span><br />
                <h3>Callback</h3>
                <span><?php echo $result->redirect_uri ?></span><br />
                <a href="<?php echo site_url('consumer/'.$result->client_id) ?>">Edit this user</a>
                <hr />
            <?php } ?>
        <?php } ?>
        <?php if ($version === 'edit') { ?>
            <form method='post'>
                <label>Name:</label>
                <?php echo form_input('name', isset($item->name) ? $item->name : ''); ?>
                <label>ClientId:</label>
                <?php echo form_input('client_id', isset($item->client_id) ? $item->client_id : '','readonly=readonly'); ?>
                <label>ClientSecret:</label>
                <?php echo form_input('client_secret', isset($item->client_secret) ? $item->client_secret : '','readonly=readonly'); ?>
                <button class="btn btn-primary" type="submit" value="reset" name="resetSecret">Reset secret</button>
                <label>Callback URL:</label>
                <?php echo form_input('redirect_uri', isset($item->redirect_uri) ? $item->redirect_uri : ''); ?>
                <br />
                <input type="hidden" name="clientid" value="<?php echo $item->client_id ?>">
                <button class="btn btn-primary" type="submit" value="update" name="updateUri">Update client</button>
                <hr />
            </form>
        <?php } ?>
    </div>
</div>