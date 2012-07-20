<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">

        <title>PlugID</title>

        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <!-- <link rel="stylesheet" href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css">  -->
        <link rel="stylesheet" href="<?php echo base_url('css/bootstrap.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/bootstrap-responsive.min.css'); ?>">
        <link rel="stylesheet" href="<?php echo base_url('css/style.css'); ?>">

    </head>
    <body>

        <div data-dropdown="dropdown" class="navbar navbar-fixed-top navbar-wrapper">
            <!-- Source: http://stackoverflow.com/a/9351158/939349 -->
            <!-- More inspiration: http://jsfiddle.net/ekjxu/ -->
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="brand" href="<?php echo site_url(''); ?>">PlugID</a>
                    <ul id="user" class="nav">
                        <li class="pull-right dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="icon-user"></i> Username
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Profile</a></li>
                                <li class="divider"></li>
                                <li><a href="<?php echo site_url('logout'); ?>">Sign Out</a></li>
                            </ul>
                        </li>
                    </ul>
                        <ul class="nav">
                            <li><a href="<?php echo site_url(''); ?>">Home</a></li>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="<?php echo site_url('docs/documentation'); ?>">
                                    Developers<b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo site_url('developer/apps'); ?>">My apps</a></li>
                                    <li><a href="<?php echo site_url('developer/register'); ?>">Register new app</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    Profile<b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo site_url('profile/plugs'); ?>">Plugs</a></li>
                                    <li><a href="<?php echo site_url('profile/apps'); ?>">Connected apps</a></li>
                                </ul>
                            </li>
                            <li><a href="<?php echo site_url('docs/userdoc'); ?>">About</a></li>
                        </ul>
                </div>
            </div>
        </div>