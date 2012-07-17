<div class="container">
    <div class="hero-unit">
        <form method="POST">
            <ul style="list-style-type:none">
                <li>
                    <?php foreach ($multi as $client) { ?>
                        <h3>Revoke <?php echo $client['name'] ?>&#39;s access</h1>
                        <p>Upon completing this action, this app will no longer be connected to your <?php echo $client['name'] ?> account.</p>
                        <button type="submit" class="btn btn-success btn-large" name="revoke" value="<?php echo $client['client_id'] ?>">Revoke</button>
                    <?php } ?>
                </li>
            </ul>
        </form>
    </div>

</div>