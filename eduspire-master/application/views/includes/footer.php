<?php 
/**
@Page/Module Name/Class: 		footer.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display common footer 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>

</div>
<!--main container ends-->
</div><!--.wrapper-->
<div class="footer">
    <footer class="group"><!--footer start here-->
		<div class="popup-div">
		<a class="right close-btn"  href="javascript::void(0)">close</a>
		<div class="popup-content">
		<p class="popup-msg">This is the popup message</p>
		<input type="button" id="ok-btn" value="ok" class="editButton" />
		<input type="button" id="cancel-btn" value="Cancel" class="editButton" />
		
		</div>
	</div>
	<div class="maincontent">

	<ul>
	<li>  <a href="<?php echo base_url(); ?>" > <?php echo SITE_NAME; ?></a>  </li>
	<li>  <a href="<?php echo base_url(); ?>courses/" > Courses</a>  </li>
	<li>  <a href="<?php echo base_url() ?>about-us">About Us</a>  </li>
	<li>  <a href="<?php echo base_url()?>events/">News</a>  </li>
	<li>  <a href="<?php echo base_url(); ?>subscribe/" >Stay Informed</a>  </li>
	<li>  <a href="<?php echo base_url(); ?>faq">FAQ</a>  </li>
	<li>  <a href="<?php echo base_url(); ?>contact/" >Contact</a>  </li>
	
  	</li>
	  <li class="icons">
	  <span> <a href="https://www.facebook.com/eduspirellc" target="_blank"><img src="<?php echo base_url(); ?>images/icon_facebook.png"  alt=""/></a></span> 
	  <span><a href="https://twitter.com/eduspirePD" target="_blank"><img src="<?php echo base_url(); ?>images/icon_twitter.png"  alt=""/></a></span> 
	  <span><a href="https://plus.google.com/communities/104477902194640480727" target="_blank"><img src="<?php echo base_url(); ?>images/icon_google.png"  alt=""/></a></span> </li>

	</ul>
	</div>
	</footer>
    <footer class="subFooter"><!--footer start here-->
		<div class="maincontent">&copy;<?php echo date('Y'); ?> Eduspire. <a href="http://ithands.com" target='_blank'>Web Design</a> & <a href="http://ithands.com" target='_blank'>Development</a> by <a href="http://ithands.com" target='_blank'>IT Hands</a> </div>
    </footer>



</div><!--.footer-->



</body>
<script type="text/javascript">
//<![CDATA[
var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-32101565-1']);
	_gaq.push(['_setDomainName', 'eduspire.org']);
	_gaq.push(['_trackPageview']);
(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + 	'.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();

//]]>

</script>
</html>	