<div class="container">
    <div class="hero-unit">
        <h1>Connected Apps</h1>
        <p>
            <?php echo form_open(); ?>
                <ul>
                    <?php foreach( $clients as $client ){?>
                        <li><?php echo $client->name ; ?>
                            <button name="revoke" value="<?php echo $client->client_id; ?>">Revoke</button>
                        </li>
                    <?php } ?>
                </ul>
            </form>
        </p>
    </div>
</div>