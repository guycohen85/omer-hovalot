<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
define( 'YOURBASEPATH', dirname(__FILE__) );
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
<base href="<?php echo JURI::base(false) ?>">
<jdoc:include type="head" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/normalize.min.css" type="text/css" />
<link rel="shortcut icon" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicon.ico">
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/bootstrap.min.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/bootstrap-rtl.min.css" type="text/css" />
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/font-awesome-animation.css" type="text/css" />

<?php /* DELETE FOR ANIMATE.CSS <<<
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/animate.css" type="text/css" />
>>> DELETE FOR ANIMATE.CSS  */ ?>
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/components.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/styles.css" type="text/css" />
<script type="text/javascript">
	if ((navigator.userAgent.match(/Trident/) || navigator.userAgent.match(/MSIE/))){ 
			document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ie.css">');
			console.log('ie.css loaded');

	} else {
		document.write('<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/fonts/fonts.css" />');
	}
</script>

<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/bootstrap-responsive.min.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/responsive.css" type="text/css" />
</head>
<body>
<section id="helperBG">
	<nav id="helper" class="wrapper">
		<jdoc:include type="modules" name="helper" style="html5plus" />
		<div class="clearbox"></div>
	</nav> <!-- /helper -->
</section> <!-- /helperBG -->
<section id="headerBG">
	<header id="header" class="wrapper">
		<div id="logo" class="left">
			<jdoc:include type="modules" name="logo" style="html5plus" />
		</div> <!-- /logo -->
		<nav id="menu" class="right">
			<jdoc:include type="modules" name="menu" style="html5plus" />
			<div class="clearbox"></div>
		</nav> <!-- /menu -->
		<div class="clearbox"></div>
	</header> <!-- /header -->
</section> <!-- /headerBG -->

<?php if($this->countModules('slider')) : ?>
<div id="slider">	
	<jdoc:include type="modules" name="slider" style="html5plus" />
</div> <!-- /slider -->
<?php endif; ?>
<?php
	$sidebar1 = $this->countModules('sidebar1') ? 'sidebar1' : '';
	$sidebar2 = $this->countModules('sidebar2') ? 'sidebar2' : '';
?>
<section id="mainBG">
	<section id="main" class="wrapper">
		<?php if($sidebar1) : ?>
			<aside id="sidebar1">
				<jdoc:include type="modules" name="sidebar1" style="html5plus" />
				<div class="clearbox"></div>
			</aside> <!-- /sidebar1 -->
		<?php endif; ?>
		<section id="center" class="<?php echo $sidebar1.' '.$sidebar2; ?>">
			<?php if($this->countModules('center-top')) : ?>
				<section id="center-top">
					<jdoc:include type="modules" name="center-top" style="html5plus" />
					<div class="clearbox"></div>
				</section> <!-- center-top -->
			<?php endif; ?>
			<article id="content">
				<jdoc:include type="message" />
				<jdoc:include type="component" />
				<div class="clearbox"></div>
			</article> <!-- /content -->
			<?php if($this->countModules('center-bottom')) : ?>
				<section id="center-bottom">
					<jdoc:include type="modules" name="center-bottom" style="html5plus" />
					<div class="clearbox"></div>
				</section> <!-- center-bottom -->
			<?php endif; ?>
		</section> <!-- /center-->
		<?php if($sidebar2) : ?>
			<aside id="sidebar2">
				<jdoc:include type="modules" name="sidebar2" style="html5plus" />
				<div class="clearbox"></div>
			</aside> <!-- /sidebar2 -->
		<?php endif; ?>
		<div class="clearbox"></div>
	</section> <!-- /main -->
</section> <!-- /mainBG -->
<?php if($this->countModules('bottom')) : ?>
	<section id="bottomBG">
		<section id="bottom" class="wrapper">
			<jdoc:include type="modules" name="bottom" style="html5plus" />
			<div class="clearbox"></div>
		</section> <!-- bottom -->
	</section> <!-- bottomBG -->
<?php endif; ?>
<section id="footerBG">
	<footer id="footer" class="wrapper">
		<?php if($this->countModules('social')) : ?>
			<section id="social">	
				<jdoc:include type="modules" name="social" style="html5plus" />
			</section> <!-- /social -->
		<?php endif; ?>
		<jdoc:include type="modules" name="footer" style="html5plus" />
		<jdoc:include type="modules" name="rights" style="html5plus" />
		<div class="clearbox"></div>
	</footer> <!-- /footer -->
</section> <!-- /footerBG -->

<script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/scripts/scripts.js"></script>
</body>
</html>