<?php
		
		if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly
			
		
		do_action( 'ava_before_footer' );	
			
		global $avia_config;
		$blank = isset( $avia_config['template'] ) ? $avia_config['template'] : '';

		//reset wordpress query in case we modified it
		wp_reset_query();


		//get footer display settings
		$the_id = avia_get_the_id(); //use avia get the id instead of default get id. prevents notice on 404 pages
		$footer = get_post_meta( $the_id, 'footer', true );
		$footer_options = avia_get_option( 'display_widgets_socket', 'all' );
		
		//get link to previous and next post/portfolio entry
		$avia_post_nav = avia_post_nav();

		/**
		 * Reset individual page override to defaults if widget or page settings are different (user might have changed theme options)
		 * (if user wants a page as footer he must select this in main options - on individual page it's only possible to hide the page)
		 */
		if( false !== strpos( $footer_options, 'page' ) )
		{
			/**
			 * User selected a page as footer in main options
			 */
			if( ! in_array( $footer, array( 'page_in_footer_socket', 'page_in_footer', 'nofooterarea' ) ) ) 
			{
				$footer = '';
			}
		}
		else
		{
			/**
			 * User selected a widget based footer in main options
			 */
			if( in_array( $footer, array( 'page_in_footer_socket', 'page_in_footer' ) ) ) 
			{
				$footer = '';
			}
		}
		
		$footer_widget_setting 	= ! empty( $footer ) ? $footer : $footer_options;

		/*
		 * Check if we should display a page content as footer
		 */
		if( ! $blank && in_array( $footer_widget_setting, array( 'page_in_footer_socket', 'page_in_footer' ) ) )
		{
			/**
			 * Allows 3rd parties to change page id's, e.g. translation plugins
			 */
			$post = AviaCustomPages()->get_custom_page_object( 'footer_page', '' );
			
			if( ( $post instanceof WP_Post ) && ( $post->ID != $the_id ) )
			{
				/**
				 * Make sure that footerpage is set to fullwidth
				 */
				$old_avia_config = $avia_config;
				
				$avia_config['layout']['current'] = array(
											'content'	=> 'av-content-full alpha', 
											'sidebar'	=> 'hidden', 
											'meta'		=> '', 
											'entry'		=> '',
											'main'		=> 'fullsize'
										);    
				
				$builder_stat = ( 'active' == Avia_Builder()->get_alb_builder_status( $post->ID ) );
				$avia_config['conditionals']['is_builder'] = $builder_stat;
				$avia_config['conditionals']['is_builder_template'] = $builder_stat;
				
				/**
				 * @used_by			config-bbpress\config.php
				 * @since 4.5.6.1
				 * @param WP_Post $post
				 * @param int $the_id
				 */
				do_action( 'ava_before_page_in_footer_compile', $post, $the_id );
				
				$content = Avia_Builder()->compile_post_content( $post );
				
				$avia_config = $old_avia_config;
				
				/**
				 * @since 4.7.4.1
				 * @param string 
				 * @param WP_Post $post
				 * @param int $the_id
				 */
				$extra_class = apply_filters( 'avf_page_as_footer_extra_classes', 'container_wrap footer-page-content footer_color', $post, $the_id );
				
				/**
				 * Wrap footer page in case we need extra CSS changes 
				 * 
				 * @since 4.7.4.1
				 */
				echo '<div class="' . $extra_class . '" id="footer-page">';
				echo	$content;
				echo '</div>';
			}
		}
		
		/**
		 * Check if we should display a footer
		 */
		if( ! $blank && $footer_widget_setting != 'nofooterarea' )
		{
			if( in_array( $footer_widget_setting, array( 'all', 'nosocket' ) ) )
			{
				//get columns
				$columns = avia_get_option('footer_columns');
		?>
				<div class='container_wrap footer_color' id='footer'>

					<div class='container'>

						<?php
						do_action('avia_before_footer_columns');

						//create the footer columns by iterating
				        switch( $columns )
				        {
				        	case 1: 
								$class = ''; 
								break;
				        	case 2: 
								$class = 'av_one_half'; 
								break;
				        	case 3: 
								$class = 'av_one_third'; 
								break;
				        	case 4: 
								$class = 'av_one_fourth'; 
								break;
				        	case 5: 
								$class = 'av_one_fifth'; 
								break;
				        	case 6: 
								$class = 'av_one_sixth'; 
								break;
							default: 
								$class = ''; 
								break;
				        }
				        
				        $firstCol = "first el_before_{$class}";

						//display the footer widget that was defined at appearenace->widgets in the wordpress backend
						//if no widget is defined display a dummy widget, located at the bottom of includes/register-widget-area.php
						for( $i = 1; $i <= $columns; $i++ )
						{
							$class2 = ''; // initialized to avoid php notices
							if( $i != 1 ) 
							{
								$class2 = " el_after_{$class}  el_before_{$class}";
							}
							
							echo "<div class='flex_column {$class} {$class2} {$firstCol}'>";
							
							if( function_exists( 'dynamic_sidebar' ) && dynamic_sidebar( 'Footer - column' . $i ) ) : else : avia_dummy_widget( $i ); endif;
							
							echo '</div>';
							
							$firstCol = '';
						}

						do_action( 'avia_after_footer_columns' );

	?>

					</div>

				<!-- ####### END FOOTER CONTAINER ####### -->
				</div>

	<?php   } //endif   array( 'all', 'nosocket' ) ?>


	<?php

			//copyright
			$copyright = do_shortcode( avia_get_option( 'copyright', '&copy; ' . __( 'Copyright', 'avia_framework' ) . "  - <a href='" . home_url( '/' ) . "'>" . get_bloginfo('name') . '</a>' ) );

			// you can filter and remove the backlink with an add_filter function
			// from your themes (or child themes) functions.php file if you dont want to edit this file
			// you can also remove the kriesi.at backlink by adding [nolink] to your custom copyright field in the admin area
			// you can also just keep that link. I really do appreciate it ;)
			$kriesi_at_backlink = kriesi_backlink( get_option( THEMENAMECLEAN . "_initial_version" ), 'Enfold' );


			if( $copyright && strpos( $copyright, '[nolink]' ) !== false )
			{
				$kriesi_at_backlink = '';
				$copyright = str_replace( '[nolink]', '', $copyright );
			}
			
			/**
			 * @since 4.5.7.2
			 * @param string $copyright
			 * @param string $copyright_option
			 * @return string
			 */
			$copyright_option = avia_get_option( 'copyright' );
			$copyright = apply_filters( 'avf_copyright_info', $copyright, $copyright_option );

			if( in_array( $footer_widget_setting, array( 'all', 'nofooterwidgets', 'page_in_footer_socket' ) ) )
			{

			?>

				<footer class='container_wrap socket_color' id='socket' <?php avia_markup_helper( array( 'context' => 'footer' ) ); ?>>
                    <div class='container'>

                        <span class='copyright'><?php echo $copyright; ?></span>

                        <?php
                        	if( avia_get_option( 'footer_social', 'disabled' ) != 'disabled' )
                            {
                            	$social_args = array( 'outside'=>'ul', 'inside'=>'li', 'append' => '' );
								echo avia_social_media_icons( $social_args, false );
                            }

							$avia_theme_location = 'avia3';
							$avia_menu_class = $avia_theme_location . '-menu';

							$args = array(
										'theme_location'	=> $avia_theme_location,
										'menu_id'			=> $avia_menu_class,
										'container_class'	=> $avia_menu_class,
										'fallback_cb'		=> '',
										'depth'				=> 1,
										'echo'				=> false,
										'walker'			=> new avia_responsive_mega_menu( array( 'megamenu' => 'disabled' ) )
									);

                            $menu = wp_nav_menu( $args );
                            
                            if( $menu )
							{ 
								echo "<nav class='sub_menu_socket' " . avia_markup_helper( array( 'context' => 'nav', 'echo' => false ) ) . '>';
								echo	$menu;
								echo '</nav>';
							}
                        ?>

                    </div>

	            <!-- ####### END SOCKET CONTAINER ####### -->
				</footer>


			<?php
			} //end nosocket check - array( 'all', 'nofooterwidgets', 'page_in_footer_socket' )


		
		
		} //end blank & nofooterarea check
		?>
		<!-- end main -->
		</div>
		
		<?php
		
		
		//display link to previous and next portfolio entry
		echo	$avia_post_nav;
		
		echo "<!-- end wrap_all --></div>";


		if( isset( $avia_config['fullscreen_image'] ) )
		{ ?>
			<!--[if lte IE 8]>
			<style type="text/css">
			.bg_container {
			-ms-filter:"progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $avia_config['fullscreen_image']; ?>', sizingMethod='scale')";
			filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $avia_config['fullscreen_image']; ?>', sizingMethod='scale');
			}
			</style>
			<![endif]-->
		<?php
			echo "<div class='bg_container' style='background-image:url(" . $avia_config['fullscreen_image'] . ");'></div>";
		}
	?>


<a href='#top' title='<?php _e( 'Scroll to top', 'avia_framework' ); ?>' id='scroll-top-link' <?php echo av_icon_string( 'scrolltop' ); ?>><span class="avia_hidden_link_text"><?php _e( 'Scroll to top', 'avia_framework' ); ?></span></a>

<div id="fb-root"></div>

<?php

	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */
	wp_footer();
	
?>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/custom.js"></script>
<script type="text/javascript">
	function setCookie(key, value, expiry) {
        var expires = new Date();
        expires.setTime(expires.getTime() + (expiry * 24 * 60 * 60 * 1000));
        document.cookie = key + '=' + value + ';expires=' + expires.toUTCString()+';path=/;';
    }

    function getCookie(key) {
        var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
        return keyValue ? keyValue[2] : null;
    }

    jQuery(document).ready(function(){
    	jQuery(document).on('click', '.wishlist-btn a', function(){
    		var wishlistCookieVal = getCookie('wishlist');
    		var currItem = jQuery(this);
    		var propertID = jQuery(this).attr('id');
    		propertID = propertID.replace("prd", "");
    		
    		if(wishlistCookieVal){
    			var wishListItem = wishlistCookieVal.split(",");
    			if ( jQuery.inArray(propertID, wishListItem) > -1) {
    				var newWishListItem = '';
    				jQuery.each(wishListItem, function( index, value) {
					  if (value  === propertID ) { 
					  	wishListItem.splice(index, 1); 
					  	currItem.removeClass('added');

					  	if(currItem.parents('.property-main-list').hasClass('wishlist')){
					  		currItem.parents('.property-item').remove();
					  	}
					  }
					});

					setCookie('wishlist', wishListItem.toString(), 1);
				}else{
    				var cookieVal = wishlistCookieVal+','+propertID;
    				setCookie('wishlist', cookieVal, 1);

    				currItem.addClass('added');
    			}
    		}else{
    			setCookie('wishlist', propertID, 1);	

    			currItem.addClass('added');
    		}
    		
    		wishlistCookieVal = getCookie('wishlist');
    		if(wishlistCookieVal){
    			var wishList = wishlistCookieVal.split(",");
    			jQuery('.wishlist-area .count').text(wishList.length);
    		}else{
    			jQuery('.wishlist-area .count').text(0);
    			jQuery('.empty-wishlist').show();
    		}
    	});
    });
</script>
<?php 
	$locatiesList = get_terms([
	    'taxonomy' => 'locaties',
	    'hide_empty' => false,
	    'parent' => 0
	]);

	$allLocatiesList = get_terms([
	    'taxonomy' => 'locaties',
	    'hide_empty' => false,
	]);

	$allLocalieArray = array();
	if(!empty($allLocatiesList)) {
		$i=0;
		foreach($allLocatiesList as $allLocatie) {
			$allLocalieArray[$i]['value'] = $allLocatie->name;
			$allLocalieArray[$i]['label'] = $allLocatie->name;
			$allLocalieArray[$i]['id'] = $allLocatie->term_id;
			$i++;
		}
	}

	$allLocatieJson = json_encode($allLocalieArray);
?>
<div class="all-locaties-model">
	<div class="all-locaties-modal-dialog">
		<div class="modal-content">
			<a href="javascript:void(0);" class="close">X close</a>
			<div class="locaties-item">
				<div class="locaties-main-title"><h2><a href="javascript:void(0);">Alle locaties</a></h2></div>
			</div>
			<?php if(!empty($locatiesList)) { ?>
				<?php foreach($locatiesList as $parentLocatie) { 
						$subLocatiesList = get_terms([
						    'taxonomy' => 'locaties',
						    'hide_empty' => false,
						    'parent' => $parentLocatie->term_id
						]);
					?>
					<div class="locaties-item">
						<div class="locaties-main-title"><h4><?php echo $parentLocatie->name; ?></h4></div>
						<ul>
							<li><a href="javascript:void(0);" data-id_loc="<?php echo $parentLocatie->term_id; ?>" data-texto_loc="<?php echo $parentLocatie->name; ?>"><?php // echo $parentLocatie->name; ?> Alle locaties</a></li>
							<?php if(!empty($subLocatiesList)) { ?>
								<?php foreach($subLocatiesList as $subLocatie) { ?>
									<li><a href="javascript:void(0);" data-id_loc="<?php echo $subLocatie->term_id; ?>" data-texto_loc="<?php echo $subLocatie->name; ?>"><?php echo $subLocatie->name; ?></a></li>
								<?php } ?>
							<?php } ?>	
						</ul>
					</div>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</div>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/jquery-ui.js"></script>
<script type="text/javascript">
	var dataLocatie = <?php echo $allLocatieJson; ?>;
    jQuery(document).ready(function(){
        jQuery('#locationText').val('');
        jQuery('#locationID').val(0);
        jQuery('#house-type').val(0);
        
        jQuery('.all-locaties-modal-dialog').find('.locaties-item a').click(function(){
            var dataLocId = jQuery(this).attr('data-id_loc');
            var dataLocText = jQuery(this).attr('data-texto_loc');

            jQuery('#locationText').val(dataLocText);
            jQuery('#locationID').val(dataLocId);
        });

        jQuery('.all-locaties-modal-dialog').find('a').click(function(){
            jQuery('.all-locaties-model').removeClass('show');
            jQuery('html').removeClass('modal-open');
        });

        jQuery('#locationText').on('input propertychange paste', function() {
		    jQuery('#locationID').val(0);
		});

		jQuery('.property-filter-main').find('.search-btn').click(function(){
			var houseType = jQuery('#house-type').val();
			var locationID = jQuery('#locationID').val();

			var redirectUrl = '<?php echo site_url(); ?>/woningen/?type='+houseType+'&location='+locationID;
			window.location.href = redirectUrl;
		});

        jQuery("#locationText").autocomplete({
	        source: dataLocatie,
	        minLength: 1,
	        highlight: true,
	        autoFocus: true,
	        classes: {
    			"ui-autocomplete": "highlight"
  			},
	        select: function(event, ui) {
	          var itemVal = ui.item.value;
	          jQuery("#locationText").val(itemVal);
	          jQuery("#locationID").val(ui.item.id);

	          if(jQuery('body').hasClass('post-type-archive-woningen')){
	          	propertyFilter();
	          }
	        }
	    });
    });
</script>
<?php if(isset($_GET['type']) && isset($_GET['location'])) { 
    $queryType = ($_GET['type'] > 0) ? $_GET['type'] : 0;
    $queryLocation = ($_GET['location'] > 0) ? $_GET['location'] : 0;

    $typeName = 'Alle Huizen';
    $termType = get_term_by( 'id', $queryType, 'woningtype' ); 
    if(!empty($termType)){
    	$typeName = $termType->name;
    }

    $term_name = '';
    $termLocatie = get_term_by( 'id', $queryLocation, 'locaties' ); 
    if(!empty($termLocatie)){
    	$term_name = $termLocatie->name;
    }
?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#woningtype').val(<?php echo $queryType; ?>);
        jQuery('#locationID').val(<?php echo $queryLocation; ?>);
        jQuery('#locationText').val('<?php echo $term_name; ?>');

        jQuery("#woningtype").parents('.select-design').find('span').text('<?php echo $typeName; ?>');

        propertyFilter();
    });
</script>
<?php } ?>
<script>
equalheight = function(container){

var currentTallest = 0,
     currentRowStart = 0,
     rowDivs = new Array(),
     $el,
     topPosition = 0;
 jQuery(container).each(function() {

   $el = jQuery(this);
   jQuery($el).height('auto')
   topPostion = $el.position().top;

   if (currentRowStart != topPostion) {
     for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
       rowDivs[currentDiv].height(currentTallest);
     }
     rowDivs.length = 0; // empty the array
     currentRowStart = topPostion;
     currentTallest = $el.height();
     rowDivs.push($el);
   } else {
     rowDivs.push($el);
     currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
  }
   for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
     rowDivs[currentDiv].height(currentTallest);
   }
 });
}

jQuery(window).load(function() {
  equalheight('.property-item .property-details .locatie-name');
  equalheight('.property-item .property-details .property-name');
});


jQuery(window).resize(function(){
  equalheight('.property-item .property-details .locatie-name');
  equalheight('.property-item .property-details .property-name');
});
	
jQuery(document).ready(function(){
	jQuery('#footer .widget h3.widgettitle').click(function() {
		jQuery(this).toggleClass('active');
		jQuery(this).next('div').slideToggle();
	});
	
	jQuery('.select-design').click(function(){
			if (jQuery(this).find('ul').is(":visible")){
				jQuery(this).find('ul').slideUp();
			} else {
				jQuery('.select-design ul').slideUp();
				jQuery(this).find('ul').slideDown();
			}
		});

	jQuery(window).click(function() {
		jQuery('.select-design ul').slideUp();
	});
	
	jQuery('.select-design').click(function(event){
			event.stopPropagation();
		});
	
	jQuery('.select-design').find('.select-design-lists li').click(function(){
		jQuery('.select-design').find('.select-design-lists li').removeClass('active');
		jQuery(this).addClass('active');
		
		var selectTitle = jQuery(this).text();
		jQuery(this).parents('.select-design').find('span').text(selectTitle);

		var termid = jQuery(this).attr('termid'); 
		jQuery(this).parents('.select-design').find('input[type="hidden"]').val(termid);

		jQuery('.select-design').find('.select-design-lists').slideUp();

		if(jQuery('body').hasClass('post-type-archive-woningen')){
          	propertyFilter();
        }
	});
});
</script>
</body>
</html>
