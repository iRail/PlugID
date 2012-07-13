<?php include('header.tpl'); ?>

<style type="text/css">
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

    .hero-unit h1 {
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
                    <li class="nav-header">Sidebar</li>
                    <li><a href="#reg">Registration</a></li>
                    <li><a href="#access">Access Token</a></li>
                    <li><a href="#">Link</a></li>
                    <li><a href="#">Link</a></li>
                    <li class="nav-header">Sidebar</li>
                    <li><a href="#">Link</a></li>
                    <li><a href="#">Link</a></li>
                    <li><a href="#">Link</a></li>
                    <li><a href="#">Link</a></li>
                    <li><a href="#">Link</a></li>
                    <li><a href="#">Link</a></li>
                </ul>
            </div><!--/.well -->
        </div><!--/span-->
        <div class="span9">
            <div class="hero-unit">
                <h1>Getting started</h1>
                <p>Apps connect with solomidem via OAuth 2.0. This is the standard used by most major API providers.</p>
                <p><a href="#reg" class="btn btn-primary btn-large">Get started &raquo;</a></p>
            </div>
            <div id="reg" class="span8">
                <h2>Registration</h2>
                <p>Make sure you have an account on solomidem. Once you have one, start by <a target="_blank" href="<?php echo site_url('register'); ?>">registering your app</a> to obtain its solomidem API credentials. Be sure to use an account with a secure password to own these credentials. If you're creating an app on the behalf of a full-fledged company, consider creating the key in association with the page account for your company. Since each set of credentials is tied to a particular URL, you may want to create different credentials for your development server and production server. For the purposes of OAuth, your “key” from that registration process is your “client id” here, and your secret from registering is your secret here.</p>
            </div><!--/span-->
            <div id="access" class="span8">
                <h2>Access token</h2>
                <p>Access tokens allow apps to make requests to foursquare on the behalf of a user. Each access token is unique to the user and consumer key. Access tokens do not expire, but they may be revoked by the user.</p>
                <h4 class="inline">Code (Preferred) - Web server applications</h4>
                <div>
                    <ul>
                        <li><b>Redirect</b> users who wish to authenticate to
                            <pre>/oauth2/authorize?redirect_uri=REDIRECT_URI&client_id=CLIENT_ID&response_type=code</pre>
                        </li>
                        <li>Then, you get a redirect to your callback url, in this form
                            <pre>http://YOUR_REGISTERED_REDIRECT_URI/?code=OUR_CODE</pre>
                        </li>
                        <li>
                            Once you have this token, your server can make a POST request to
                            <pre>/oauth2/access_token</pre>
                            With parameters
                            <pre>client_id=YOUR_CLIENT_ID&client_secret=YOUR_CLIENT_SECRET&grant_type=authorization_code&redirect_uri=YOUR_REGISTERED_REDIRECT_URI&code=OUR_CODE</pre>
                        </li>
                    </ul>
                </div>
            </div><!--/span-->
            <div class="span8">
                <h2>Heading</h2>
                <p>Donec  id  elit  non mi porta gravida at eget metus. Fusce dapibus, tellus  ac  cursus  commodo, tortor mauris condimentum nibh, ut fermentum massa  justo  sit  amet risus. Etiam porta sem malesuada magna mollis euismod.  Donec  sed  odio dui. </p>
                <p><a class="btn" href="#">View details &raquo;</a></p>
            </div><!--/span-->
        </div><!--/span-->
    </div><!--/row-->

    <hr>

    <footer>
        <p>&copy; iRail 2012</p>
    </footer>

</div><!--/.fluid-container-->



<?php include ('footer.tpl'); ?>