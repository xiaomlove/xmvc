<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8">
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TinyHD | <?php echo $this->getPageTitle()?></title>

  <link rel="stylesheet" href="<?php echo App::ins()->request->getBaseUrl()?>application/public/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="<?php echo App::ins()->request->getBaseUrl()?>application/public/css/style.css">
  <?php echo $this->getScript('application/public/js/config.js', FALSE)?>
  <!--[if lt IE 9]>
  <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="<?php echo App::ins()->request->getBaseUrl()?>application/public/js/respond.js"></script>
  <![endif]-->
</head>
<body>