<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en-us">
  <head>
    <link href="http://gmpg.org/xfn/11" rel="profile">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">

    <!-- Enable responsiveness on mobile devices-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <?php echo $this->title(); ?>

    <!-- CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $this->router()
      ->generateRaw('aura.asset',
          array(
              'vendor' => 'rtdocs',
              'package' => 'reader',
              'file' => 'css/prism.css'
          )
      ); ?>">
    <link rel="stylesheet" href="<?php echo $this->router()
      ->generateRaw('aura.asset',
          array(
              'vendor' => 'rtdocs',
              'package' => 'reader',
              'file' => 'css/custom.css'
          )
      ); ?>">
    <meta name="description" content="">
    <meta name="author" content="Hari KT">
    <link rel="icon" href="favicon.ico">



    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
      <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">Render github markdown files</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="/">Home</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Releases <span class="caret"></span></a>
              <?php echo $this->releases(); ?>
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="active"><a href="http://harikt.com">Hari KT</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-4" id="sidebar">
                <?php echo $this->navigation(); ?>
            </div>
            <div class="col-md-8">
                <?php echo $this->getContent(); ?>
            </div>
        </div>
    </div> <!-- /container -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <?php /*
    <script src="<?php echo $this->router()
      ->generateRaw('aura.asset',
          array(
              'vendor' => 'rtdocs',
              'package' => 'reader',
              'file' => 'js/docs.js'
          )
      ); ?>"></script>
      */ ?>
    <script src="<?php echo $this->router()
      ->generateRaw('aura.asset',
          array(
              'vendor' => 'rtdocs',
              'package' => 'reader',
              'file' => 'js/prism.js'
          )
      ); ?>"></script>
  </body>
</html>
