<div class="container">

    <div class="hero-unit">
        <h1><?php echo $client; ?> requests access</h1>
        <p>Connecting this app will allow it to do certain things like create new check-ins on your behalf and access personal information such as your profile information, check-in history, friends list, tips and to-dos.</p>

        <?php echo form_open('oauth2/authorize?' . http_build_query($_GET)); ?>
        <p>
            <button type="submit" class="btn btn-success btn-large" name="allow" value="yes">Allow</button>
            <button class="btn btn-danger btn-large" onclick="history.go(-1)">Deny</button>
        </p>
        </form>
    </div>

</div>
