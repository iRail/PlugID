<div class="container">
    <div class="hero-unit">
        <?php echo form_open(); ?>
            <h2>Your authorized applications</h2>
            <ul style="list-style-type:none">
                <li>
                    <h3>Revoke <?php echo $name; ?>&#39;s access</h1>
                    <p>Upon completing this action, this app will no longer be connected to your <?php echo $name; ?> account.</p>
                    <button type="submit" class="btn btn-success btn-large" name="client_id" value="<?php echo $client_id ?>">Revoke</button>
                </li>
            </ul>
        </form>
    </div>
</div>