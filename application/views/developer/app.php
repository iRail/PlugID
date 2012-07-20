<div class="container">
    <div class="hero-unit">
        <div id="form">
            <h2>Your authorized applications</h2>
            <?php echo form_open(); ?>
            <label>Name:</label>
            <?php echo form_input('name', isset($item->name) ? $item->name : '', 'class="input-xlarge"'); ?>
            <label>Client Id:</label>
            <?php echo form_input('client_id', isset($item->client_id) ? $item->client_id : '', 'readonly class="input-xlarge"'); ?>
            <label>Client Secret:</label>
            <?php echo form_input('client_secret', isset($item->client_secret) ? $item->client_secret : '', 'readonly class="input-xlarge"'); ?>
            <button class="btn btn-primary" type="submit" value="reset" name="resetSecret">Reset secret</button>
            <label>Callback URL:</label>
            <?php echo form_input('redirect_uri', isset($item->redirect_uri) ? $item->redirect_uri : '', 'class="input-xlarge"'); ?>
            <br />
            <input type="hidden" name="clientid" value="<?php echo $item->client_id ?>">
            <button class="btn btn-primary" type="submit" value="update" name="updateUri">Update client</button><br />
            <span style="display:block">&nbsp;</span>
            <button class="btn btn-primary" type="submit" value="delete" name="deleteClient">Delete client</button>
            </form>
        </div>
        <div id="tokens">
            <b>Access token URL</b><br />
            <span><?php echo site_url() ?>oauth2/access_token</span><br />
            <b>Authorize URL</b><br />
            <span><?php echo site_url() ?>oauth2/authorize</span>
        </div>
</div>