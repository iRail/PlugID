<div class="container">
    <div class="hero-unit">
        <div id="form">
            <h2>Your authorized applications</h2>
            <?php echo form_open(); ?>
            <label>Name:</label>
            <?php echo form_input('name', isset($item->name) ? $item->name : '', 'class="input-xlarge"'); ?>
            <label>Client ID:</label>
            <?php echo form_input('client_id', isset($item->client_id) ? $item->client_id : '', 'readonly class="input-xlarge"'); ?>
            <label>Client secret:</label>
            <?php echo form_input('client_secret', isset($item->client_secret) ? $item->client_secret : '', 'readonly class="input-xlarge"'); ?>
            <button class="btn btn-primary" type="submit" value="reset" name="resetSecret">Reset</button>
            <label>Callback URL:</label>
            <?php echo form_input('redirect_uri', isset($item->redirect_uri) ? $item->redirect_uri : '', 'class="input-xlarge"'); ?>
            <label>Push URL (receive train-delay updates for your users):</label>
            <?php echo form_input('notify_uri', isset($item->notify_uri) ? $item->notify_uri : '', 'class="input-xlarge"'); ?>
            <label>Push secret:</label>
            <?php echo form_input('notify_secret', isset($item->notify_secret) ? $item->notify_secret : '', 'readonly class="input-xlarge"'); ?>
            <button class="btn btn-primary" type="submit" value="reset" name="resetPushSecret">Reset</button>
            <br />
            <br />
            <input type="hidden" name="clientid" value="<?php echo $item->client_id ?>">
            <button class="btn btn-primary" type="submit" value="update" name="update">Update client</button><br />
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
</div>