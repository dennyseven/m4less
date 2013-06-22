<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php if ($icon) { ?>
<link href="<?php echo $icon; ?>" rel="icon" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/geccoshop/css/stylesheet.css" />
<!--SLIDER STYLE-->
<link rel="stylesheet" href="http://www.marineelectronics4less.co/main/catalog/view/theme/geccoshop/slider/css/default.css" type="text/css" media="screen" />
<link rel="stylesheet" href="http://www.marineelectronics4less.co/main/catalog/view/theme/geccoshop/slider/css/nivo-slider.css" type="text/css" media="screen" />
<link rel="stylesheet" href="http://www.marineelectronics4less.co/main/catalog/view/theme/geccoshop/slider/css/style.css" type="text/css" media="screen" />

<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<script src="catalog/view/theme/geccoshop/js/jquery.js" type="text/javascript"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="catalog/view/theme/geccoshop/js/jquery.jcarousel.min.js"></script>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/geccoshop/css/carousel-skin.css" />
<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/carousel.css" media="screen" />
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/external/jquery.cookie.js"></script>
<script type="text/javascript" src="catalog/view/javascript/common.js"></script>
<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>

<script type="text/javascript" src="catalog/view/theme/geccoshop/js/cufon.js"></script>
<script type="text/javascript" src="catalog/view/theme/geccoshop/js/Geosans_basic.font.js"></script>

<script src="catalog/view/theme/geccoshop/js/select-styling.js" type="text/javascript"></script>


<!-- COLORBOX -->
<link rel="stylesheet" type="text/css" href="catalog/view/theme/geccoshop/css/colorbox.css" />
<script src="catalog/view/theme/geccoshop/js/jquery.colorbox-min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
	//Examples of how to assign the ColorBox event to elements
	$("a.colorbox").colorbox({rel:'colorbox'});
	$(".ajax").colorbox();
	$(".callbacks").colorbox({
		onOpen:function(){ alert('onOpen: colorbox is about to open'); },
		onLoad:function(){ alert('onLoad: colorbox has started to load the targeted content'); },
		onComplete:function(){ alert('onComplete: colorbox has displayed the loaded content'); },
		onCleanup:function(){ alert('onCleanup: colorbox has begun the close process'); },
		onClosed:function(){ alert('onClosed: colorbox has completely closed'); }
	});
				
	//Example of preserving a JavaScript event for inline calls.
	$("#click").click(function(){ 
		$('#click').css({"background-color":"#f00", "color":"#fff", "cursor":"inherit"}).text("Open this window again and this message will still be here.");
		return false;
	});
	});
</script>

<!-- TOOGLE -->
<script type="text/javascript">
$(document).ready(function(){
	$(".toggle_container").hide(); 
	$(".trigger-title").click(function(){
		$(this).toggleClass("active").next().slideToggle("slow");
		return false; //Prevent the browser jump to the link anchor
	});
});
</script>


<!-- CAROUSEL -->
<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('#mycarousel').jcarousel({
    	wrap: 'circular'
    });
});
</script>

<!-- SLIDING CAPTIONS -->
<script type="text/javascript">
$(document).ready(function(){
	$('.boxgrid.captionfull').hover(function(){
		$(".cover", this).stop().animate({top:'0px'},{queue:false,duration:160});
	}, function() {
	$(".cover", this).stop().animate({top:'260px'},{queue:false,duration:160});
	});
});
</script>

<!-- CUFON -->
<script type="text/javascript" charset="utf-8">
Cufon.replace('.page-title, .welcome-left, .cart-total, .price-amount', { fontFamily: 'Geosans' } );
</script>

<!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/geccoshop/css/ie7.css" />
<![endif]-->

<?php echo $google_analytics; ?>
</head>

<body id="<?php echo empty($this->request->get['route']) ? 'common-home' : str_replace('/', '-', $this->request->get['route']); ?>">

<div id="notification"></div>

<div id="general-bg"></div>

<div id="main">

<div id="header-background">
<div id="header-container">
<div id="header-center">

<div id="header-level-1">

<div id="logo"><a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" width="250" height="100" /></a></div><!--logo ends-->


<div id="header-level-1-right">

<div id="header-level-1-right-top">

<div id="cart-widget">
<div class="toolbar-cart-count alignright"><?php echo $cart; ?></div>
</div><!--cart-widget ends-->

<?php // echo $language; ?>
<?php // echo $currency; ?>

<div style="width:685px;height:143px;position:relative;top:28px;left:115px;">
	<a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=59">
		<img src="/catalog/view/theme/geccoshop/image/save-banner.png" height="70" width="340" />
	</a>
</div>

</div><!--header-level-1-right-top ends-->

<div class="clear"></div>
<!--
<div id="mini-menu">
<ul>
<li><a href="<?php echo $home; ?>"><?php echo $text_home; ?></a></li>
<li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
<li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
<li><a href="<?php echo $special; ?>"><?php echo $text_special; ?></a></li>
<li><a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>
<li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
</ul>
</div>
-->
</div><!--header-level-1-right ends-->
</div><!--header-level-1 ends-->

<div class="clear"></div>

<div id="categories-container">
<div id="categories">
<ul class="nav">
    <li><a href="http://www.marineelectronics4less.co/main/">Home</a></li><!--DONE-->
      <li><a href="http://www.marineelectronics4less.co/main/index.php?route=information/information&information_id=4">About</a></li><!--DONE-->
      <li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=59">Shop</a><!--DONE-->
	  	<div>
			<ul><li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=59">Categories</a>
				<div>
					<ul><li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=93">Accessories</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=67">AIS</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=68">Antenna Mounts</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=69">Autopilots</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=70">Battery Chargers</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=71">Cameras</a></li>
	                <li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=94">Chartplotters</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=72">EPIRB Units</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=73">Fishfinders/Chartplotters</a></li>
	                <li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=95">GPS Antennas</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=74">GPS/Chart Bracket Mount</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=75">GPS Handheld</a></li>
	                <li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=96">Hailers</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=76">Instruments</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=77">Inverters</a></li>
            	    <li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=78">VHF Radios</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=79">VHF Handheld</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=80">SSB Radios</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=81">Marine Antennas</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=82">Marine Stereos</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=83">Marine Monitors</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=84">Radar/Chartplotters</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=85">Satellite TV</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=86">Satellite Communications</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=87">Sonar</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=88">Transducers</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=89">TV Antennas</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=90">Underwater Lights</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=91">Waterproof Speakers</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=92">Watermakers</a></li>
					</ul>
				</div>
			</li><!--DONE-->
			<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=59">Manufacturer's</a>
				<div>
					<ul><li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=100">AccuSteer</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=101">ACR</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=102">Airmar</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=103">Analytic</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=104">B&amp;G</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=105">Bose</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=106">Comnav</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=107">Digital Antenna</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=108">FCI</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=109">Flir</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=62">Furuno</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=110">GlobalStar</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=97">Glomex</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=111">Hatteland</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=63">Icom</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=65">Intellian</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=112">KVH</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=113">Lowrance</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=114">Maretron</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=115">McMurdo Kannad</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=116">Morad</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=117">Nauticomp</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=99">Ocean LED</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=118">OutBack Power</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=119">Polyplaner</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=66">Simrad</a></li>
					<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=98">Standard Horizon</a></li>
					</ul>
				</div>
			</li><!--DONE-->
			<li><a href="http://www.marineelectronics4less.co/main/index.php?route=product/category&path=59">Shop All Products</a></li><!--DONE-->
			</ul>
		</div>
	  </li>
	  <li><a href="http://www.marineelectronics4less.co/main/index.php?route=information/contact">Contact</a></li><!--DONE-->
	  <li><a href="https://www.facebook.com/pages/marineelectronics4lesscom/428671127219394?ref=ts&fref=ts">Social</a><!--DONE-->
	  	<div>
			<ul><li><a href="https://www.facebook.com/pages/marineelectronics4lesscom/428671127219394?ref=ts&fref=ts">Facebook</a></li></ul><!--DONE-->
		</div>
	  </li>
  </ul><!--nav ends-->
</div><!--categories ends-->


  <div id="search-area">
    <?php if ($filter_name) { ?>
    <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" />
    <?php } else { ?>
    <input type="text" name="filter_name" value="<?php echo $text_search; ?>" onclick="this.value = '';" onkeydown="this.style.color = '#000000';" />
    <?php } ?>
  <div class="button-search"></div>
  </div>



</div><!--categories-container ends-->



</div><!--header-center ends-->

<div style="background-color:#182460; width:100%; height:385px; position: relative; z-index:-3;">
<!--INTENTIONALLY BLANK-->

<img src="http://www.marineelectronics4less.co/main/catalog/view/theme/geccoshop/slider/images/slide1.png" width="100%" height="auto" />

</div>



<div class="clear"></div>

</div><!--header-container ends-->
