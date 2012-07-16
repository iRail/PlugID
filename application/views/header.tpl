<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    
    <title>Solomidem</title>
    
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">
    
    <!-- <link rel="stylesheet" href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css">  -->
    <link rel="stylesheet" href="<?php echo base_url('css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('css/bootstrap-responsive.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('css/style.css'); ?>">
    
</head>
<body>

<div class="navbar navbar-fixed-top">
    <!-- Source: http://stackoverflow.com/a/9351158/939349 -->
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="/solomidem">Solomidem</a>
            <div class="btn-group pull-right">
                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="icon-user"></i> Username
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="#">Profile</a></li>
                    <li class="divider"></li>
                    <li><a href="#">Sign Out</a></li>
                </ul>
            </div>
            <div class="nav-collapse">
                <ul class="nav">
                    <li><a href="/solomidem">Home</a></li>
                    <li><a href="/solomidem/authenticate">Authenticate</a></li>
                    <li><a href="/solomidem/register">Register</a></li>
                    <li><a href="/solomidem/userdoc">Users</a></li>
                    <li><a href="/solomidem/documentation">Developers</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>