<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Linked Emails Project</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width">

  <link href="<?php echo base_url(); ?>assets/css/admin/global.css" rel="stylesheet" type="text/css">
  
   <!--<link rel="stylesheet" href="<?php /*echo base_url();*/?>/assets/css/bootstrap-responsive.min.css">-->

  <script src="<?php echo base_url();?>assets/js/modernizr-2.6.2-respond-1.1.0.min.js"></script>
  <!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script> -->
 
  
  <script src="<?php echo base_url();?>/assets/js/jquery-1.7.1.min.js"></script>)
  <script src="<?php echo base_url();?>/assets/js/bootstrap.min.js"></script>
  
  <!--<script src="<?php echo base_url();?>/node_modules/socket.io/node_modules/socket.io-client/socket.io.js"></script>-->
  
  <!--<script src="<?php echo base_url();?>/assets/js/socket.js"></script> -->
  <!--<script src="<?php echo base_url();?>/assets/js/main.js"></script> -->
  
    <style>
    body {
      padding-top: 60px;
      padding-bottom: 40px;
    }
    </style>
</head>
<body>
	<div class="navbar navbar-fixed-top">
	  <div class="navbar-inner">
	    <div class="container">
	      <a class="brand">Linked Emails Project</a>
	      <ul class="nav">
	        <li <?php if($this->uri->segment(2) == 'admin'){echo 'class="active"';}?>>
	          <a href="<?php echo base_url(); ?>index.php/admin/main">DashBoard</a>
	        </li>
                <li <?php if($this->uri->segment(2) == 'products'){echo 'class="active"';}?>>
	          <a href="<?php echo base_url(); ?>index.php/admin/products">Products</a>
	        </li>
	        <li <?php if($this->uri->segment(2) == 'manufacturers'){echo 'class="active"';}?>>
	          <a href="<?php echo base_url(); ?>index.php/admin/manufacturers/">Manufacturers</a>
	        </li>
                <li class="dropdown">
	          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Emails <b class="caret"></b></a>
	          <ul class="dropdown-menu">
	            <li>
	              <a href="<?php echo base_url(); ?>index.php/admin/Emails">Email Add,Edit,Delete</a>
	            </li>
                    <li>
	              <a href="<?php echo base_url(); ?>index.php/admin/Owner">Owner Add,Edit,Delete</a>
	            </li>
                    <li>
	              <a href="<?php echo base_url(); ?>index.php/admin/Source">Source Add,Edit,Delete</a>
	            </li>
	          </ul>
	        </li>
                <li class="dropdown">
	          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Companies <b class="caret"></b></a>
	          <ul class="dropdown-menu">
	            <li>
	              <a href="<?php echo base_url(); ?>index.php/admin/Companies">Company Add,Edit,Delete</a>
	            </li>
                    <li>
	              <a href="<?php echo base_url(); ?>index.php/admin/Category">Category Add,Edit,Delete</a>
	            </li>
                    <li>
	              <a href="<?php echo base_url(); ?>index.php/admin/Source">... Add,Edit,Delete</a>
	            </li>
	          </ul>
	        </li>
	        <li class="dropdown">
	          <a href="#" class="dropdown-toggle" data-toggle="dropdown">System <b class="caret"></b></a>
	          <ul class="dropdown-menu">
	            <li>
	              <a href="<?php echo base_url(); ?>admin/logout">Logout</a>
	            </li>
	          </ul>
	        </li>
	      </ul>
	    </div>
	  </div>
	</div>	
    
    
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li class="active"><a href="<?php echo base_url(); ?>index.php/admin/main">Overview <span class="sr-only">(current)</span></a></li>
            <li><a href="#">Reports</a></li>
            <li><a href="<?php echo base_url(); ?>index.php/admin/Mailer/show">Mailer</a></li>
            <li><a href="<?php echo base_url(); ?>index.php/admin/Mailer/mailfixedjob">Scheduled Mail Jobs</a></li>
            <li><a href="<?php echo base_url(); ?>index.php/admin/Mailer/cronm/1/mailer/8/gmail/UI">Cron Scheduler</a></li>
            <li><a href="<?php echo base_url(); ?>index.php/admin/Emails/cron_backup/false">Cron Backup</a></li>
            <li><a href="<?php echo base_url(); ?>index.php/admin/export/show">Exports</a></li>
          </ul>
          <ul class="nav nav-sidebar">
            <li><a href="">Nav item</a></li>
            <li><a href="">Nav item again</a></li>
            <li><a href="">One more nav</a></li>
            <li><a href="<?php echo base_url(); ?>index.php/admin/Statistics/">Statistics</a></li>
            <li><a href="<?php echo base_url(); ?>index.php/admin/Owner/">Owners</a></li>
          </ul>
          <ul class="nav nav-sidebar">
            <li><a href="<?php echo base_url(); ?>index.php/admin/Emails/">Emails</a></li>
            <li><a href="<?php echo base_url(); ?>index.php/admin/upload/show">Uploads</a></li>
            <li><a href="<?php echo base_url(); ?>index.php/admin/upload/">Upload Files</a></li>
          </ul>
        </div>
       
        </div>  
       </div>
