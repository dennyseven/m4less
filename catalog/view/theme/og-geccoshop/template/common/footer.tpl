
</div><!--main ends-->

<div id="footer">

<div class="footer-spacer1"></div>

<div id="footer-center">

<div id="footer-column1">

<h3>What we sell</h3>

Nulla dui nibh, aliquam quis mollis vel, consectetur in velit. Vestibulum placerat ultrices quam, et fermentum nisl vehicula a. Mauris ac enim quis mollis vel, consectetur in velit. Mollis vel, consectetur in velit.


<h3>Social Networks</h3>
<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
    <a class="addthis_button_facebook"></a>
    <a class="addthis_button_twitter"></a>
    <a class="addthis_button_email"></a>
    <a class="addthis_button_delicious"></a>
    <a class="addthis_button_digg"></a>
<a class="addthis_button_compact"></a>
</div>
<script type="text/javascript" src="//s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e3030310ffbcd20"></script>
<!-- AddThis Button END -->

</div><!--footer-column1 ends-->

<div id="footer-column2">

<h3>Informations</h3>
 <ul>
      <?php foreach ($informations as $information) { ?>
      <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
      <?php } ?>
      <li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
      <li><a href="<?php echo $affiliate; ?>"><?php echo $text_affiliate; ?></a></li>
      <li><a href="<?php echo $voucher; ?>"><?php echo $text_voucher; ?></a></li>
 </ul>

</div><!--footer-column2 ends-->

<div id="footer-column3">

<h3>Contact Informations</h3>

<div id="map-image"></div>
<div id="contact-details">			
<ul>
<li>Gecco Shop</li>
<li>446 N. Rexford Drive, CA 90518</li>
<li>Phone Number: (0678) 878-577-866</li>
<li>E-Mail Address: sales(...)geccoshop.com</li>
<li>Office working hours: 9.00AM - 4.00PM</li>
</ul>
</div>

</div><!--footer-column3 ends-->

</div><!--footer-center ends-->
<div class="clear"></div>

<div class="footer-spacer2"></div>
<div id="footer-bottom">
&copy; <?php echo $name; ?> / Flags by <a href="http://www.icondrawer.com">IconDrawer</a> 
</div><!--footer-bottom ends-->
<div class="footer-spacer3"></div>
</div><!--footer ends-->


<script type="text/javascript"> Cufon.now(); </script>

</body>
</html>
