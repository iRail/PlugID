<style type="text/css">
    .logo {
        float: left;
        margin-right: 15px;
    }

    .span8 {
        margin-bottom: 25px;
    }

    .span8:after {
        clear:both;
    }

    body {
        padding-bottom: 40px;
        padding-top: 60px;
    }

    .sidebar-nav {
        padding: 9px 0;
    }

    .sidebar-nav-fixed {
        position:fixed;
        top:60px;
        width:21.97%;
    }

    h1 {
        word-wrap: break-word;
    }

    @media (max-width: 767px) {
        .sidebar-nav-fixed {
            position:static;
            width:auto;
        }
    }

    @media (max-width: 979px) {
        .sidebar-nav-fixed {
            top:70px;
        }
    }
</style>
<div class="container-fluid">

    <div class="row-fluid">
        <div class="span3">
            <div class="well sidebar-nav sidebar-nav-fixed">
                <ul class="nav nav-list">
                    <li class="nav-header">Services</li>
                    <li><a href="#vs">Vikingspots</a></li>
                    <li><a href="#fs">Foursquare</a></li>
                    <li><a href="#tw">Twitter</a></li>
                    <li><a href="#fb">Facebook</a></li>
                </ul>
            </div><!--/.well -->
        </div><!--/span-->
        <div class="span9">
            <div class="hero-unit">
                <h1>Services you can connect to</h1>
                <p>The PlugID platform offers connection with various social media. Allowing you to post about the things you're doing, look up places to eat or drink and find deals by local businesses. By connecting, you let PlugID post on the accounts you've connected.</p>
                <p><a href="#vs" class="btn btn-primary btn-large">Start reading &raquo;</a></p>
                <p><a href="<?php echo site_url('authenticate'); ?>" class="btn btn-primary btn-large">Get connected &raquo;</a></p>
            </div>
            <div id="vs" class="span8">
                <figure>
                    <img class="logo" width="116" height="116" alt="Vikingspots logo" src="<?php echo base_url('img/viking-logo.jpg'); ?>" />
                    <figcaption>Vikingspots will inform you of the best deals in town and places to be. Find out what friends are doing or simply check in somewhere, which will earn you points and shields. Wherever you go, Vikingspots will tell you what's happening near you.</figcaption>
                </figure>
            </div><!--/span-->
            <div id="fs" class="span8">
                <figure>
                    <img class="logo" width="116" height="116" alt="Foursquare logo" src="<?php echo base_url('img/foursquare-logo.jpg'); ?>" />
                    <figcaption>Don't know what to write here</figcaption>
                </figure>
            </div><!--/span-->
            <div id="tw" class="span8">
                <figure>
                <img class="logo" width="116" height="116" alt="Twitter logo" src="<?php echo base_url('img/twitter-logo.png'); ?>" />
                    <figcaption>PlugID will be able to post on your twitter account and by connecting it, you agree to this. It is mentioned again when you first connect.</figcaption>
                </figure>
            </div><!--/span-->
            <div id="fb" class="span8">
                <figure>
                    <img class="logo" width="116" height="116" alt="Facebook logo" src="<?php echo base_url('img/facebook-logo.png'); ?>" />
                    <figcaption>PlugID can post to your facebook account.</figcaption>
                </figure>
            </div><!--/span-->
        </div>
    </div><!--/row-->

    <hr>
    <footer>
        <p>&copy; iRail 2012</p>
    </footer>

</div><!--/.fluid-container-->