<div class="container">
    <div class="hero-unit">
        <h1>Your profile is plugged into</h1>
        <p>
            <form method="post">
                <ul>
                    <?php foreach( $plugs as $service_name => $token ){?>
                        <li>
                            <?php if( $token ){ ?>
                                <?php echo $service_name; ?> is plugged in &#8594;
                            <?php }else{ ?>
                                <a href="<?php echo site_url('connect/' .$service_name); ?>">Plug-in <?php echo site_url($service_name); ?> &#8594;</a>
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            </form>
        </p>
    </div>
</div>