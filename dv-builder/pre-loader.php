<?php 
$preLoader = $options['preLoader'] ?? '';
$loaderCode = $options['loaderCode'] ?? '';
if(!empty($preLoader) && $preLoader == 1){ ?>
	<div class="preloader">
		<?php 
			if($loaderCode == 'web_logo'){ 
				the_custom_logo();
			}if($loaderCode == 'whirlpool'){ 
				echo '<div class="preloader-whirlpool"><div class="whirlpool"></div></div>';
			}elseif($loaderCode == 'floating-circles'){
				echo '<div class="preloader-floating-circles"><div class="f_circleG" id="frotateG_01"></div><div class="f_circleG" id="frotateG_02"></div><div class="f_circleG" id="frotateG_03"></div><div class="f_circleG" id="frotateG_04"></div><div class="f_circleG" id="frotateG_05"></div><div class="f_circleG" id="frotateG_06"></div><div class="f_circleG" id="frotateG_07"></div><div class="f_circleG" id="frotateG_08"></div></div>';
			}elseif($loaderCode == 'eight-spinning'){
				echo '<div class="preloader-eight-spinning"><div class="cssload-lt"></div><div class="cssload-rt"></div><div class="cssload-lb"></div><div class="cssload-rb"></div></div>';
			}elseif($loaderCode == 'double-torus'){
				echo '<div class="preloader-double-torus"></div>';
			}elseif($loaderCode == 'tube-tunnel'){
				echo '<div class="preloader-tube-tunnel"></div>';
			}elseif($loaderCode == 'speed-wheel'){
				echo '<div class="preloader-speeding-wheel"></div>';
			}elseif($loaderCode == 'loading-wrap'){
				echo '<div class="preloader-loading-wrapper"><div class="cssload-loading"><i></i><i></i></div></div>';
			}elseif($loaderCode == 'dot-load'){
				echo '<div class="preloader-dot-loading"><div class="cssload-loading"><i></i><i></i><i></i><i></i></div></div>';
			}elseif($loaderCode == 'circle-load'){
				echo '<div class="preloader-circle-loading-wrapper"><div class="cssload-loader"></div></div>';
			}elseif($loaderCode == 'circle-rotator'){
				echo '<div class="preloader-dot-circle-rotator"></div>';
			}elseif($loaderCode == 'equalizer'){
				echo '<div class="preloader-equalizer"><ul><li></li><li></li><li></li><li></li><li></li><li></li></ul></div>';
			}elseif($loaderCode == 'bubbling'){
				echo '<div class="preloader-bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div>';
			}elseif($loaderCode == 'Text'){
				echo '<div class="preloader-fountainTextG"><div id="fountainTextG_1" class="fountainTextG">L</div><div id="fountainTextG_2" class="fountainTextG">o</div><div id="fountainTextG_3" class="fountainTextG">a</div><div id="fountainTextG_4" class="fountainTextG">d</div><div id="fountainTextG_5" class="fountainTextG">i</div><div id="fountainTextG_6" class="fountainTextG">n</div><div id="fountainTextG_7" class="fountainTextG">g</div></div>';
			}elseif($loaderCode == 'orbit'){
				echo '<div class="preloader-orbit-loading"><div class="cssload-inner cssload-one"></div><div class="cssload-inner cssload-two"></div><div class="cssload-inner cssload-three"></div></div>';
			}elseif($loaderCode == 'jackhammer'){
				echo '<div class="preloader-jackhammer"><ul class="cssload-flex-container"><li><span class="cssload-loading"></span></li></ul></div>';
			}elseif($loaderCode == 'swapping'){
				echo '<div class="preloader-square-swapping"><div class="cssload-square-part cssload-square-green"></div><div class="cssload-square-part cssload-square-pink"></div><div class="cssload-square-blend"></div></div>';
			}
		?>
	</div>
<?php } ?>