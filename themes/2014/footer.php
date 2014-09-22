
		<div class="felix-footer felix-footer-news">
			<div class="row">
				<div class="medium-12 columns text-center">
					<div class="felix-nametype"><img src="<?php echo STANDARD_URL.'themes/'.THEME_NAME.'/'; ?>img/white logo.png" style="width: 1.5em; height: 1.5em;">&nbsp;Felix</div>
					<p><b>Felix</b> • Beit Quad • Prince Consort Road • London • SW7 2BB • <b>Email:</b> felix@imperial.ac.uk • <b>Phone:</b> 020 7594 8072 • <b>Twitter:</b> @FelixImperial<br></p>
					<p>Web design by Philippa Skett, Philip Kent, and Jonathan Kim • &copy; Felix Imperial, All Rights Reserved</p>
				</div>
			</div>
		</div>

		<?php foreach($theme->resources->getJS() as $key => $value) { ?>
			<script src="<?php echo $value; ?>"></script>
		<?php } ?>
		<script>
			$(document).foundation();
			$(document).ready(function(){
				$('.felix-featured-slider').slick({
					dots: true,
					infinite: true,
					speed: 300,
					slidesToShow: 1,
					slidesToScroll: 1,
					autoplay: true
			})});
			$(document).ready(function(){
				$('.felix-horoscope-slider').slick({
					dots: false,
					infinite: true,
					speed: 300,
					slidesToShow: 1,
					slidesToScroll: 1,
					autoplay: true
			})});

			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-12220150-1']);
			_gaq.push(['_trackPageview']);

			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		</script>
	</body>
</html>
