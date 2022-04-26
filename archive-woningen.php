<?php
if ( !defined('ABSPATH') ){ die(); }
global $avia_config, $wp_query;
get_header();
do_action( 'ava_page_template_after_header' );
    if( get_post_meta(get_the_ID(), 'header', true) != 'no') echo avia_title();
    do_action( 'ava_after_main_title' );

$woningType = get_terms([
    'taxonomy' => 'woningtype',
    'hide_empty' => true,
]);

$slaapkamerType = get_terms([
    'taxonomy' => 'slaapkamer',
    'hide_empty' => true,
]);

$kenmerkenType = get_terms([
    'taxonomy' => 'kenmerken',
    'hide_empty' => true,
    'meta_query' => array(
         array(
            'key'       => 'useForFilter',
            'value'     => 'yes',
            'compare'   => '='
         )
    )
]);

$statusType = get_terms([
    'taxonomy' => 'status',
    'hide_empty' => true,
]);

$woningMinMaxPrice = $wpdb->get_row( $wpdb->prepare( "SELECT max(cast(meta_value as unsigned)) as maxPrice, min(cast(meta_value as unsigned)) as minPrice FROM ".$wpdb->prefix."postmeta WHERE meta_key = 'prijs'" ) );

$minPrice = isset($woningMinMaxPrice->minPrice) ? $woningMinMaxPrice->minPrice : 0;
$maxPrice = isset($woningMinMaxPrice->maxPrice) ? $woningMinMaxPrice->maxPrice : 0;

$args = array(
    'post_type'=> 'woningen',
    'orderby'    => 'ID',
    'post_status' => 'publish',
    'order'    => 'DESC',
    'posts_per_page' => 5,
    'relation' => 'AND',
    'tax_query' => array(
        array(
            'taxonomy' => 'status',
            'field'    => 'id',
            'terms'    => 13,
            'operator'  => 'IN'
        ),
    ),
);
$result = new WP_Query( $args );

//echo '<prE>'; print_r($result); die();

$wishListCookie = array();
if(isset($_COOKIE['wishlist']) && $_COOKIE['wishlist'] != ''){
    $wishListCookie = explode(',', $_COOKIE['wishlist']);
}
?>
    <div class='container_wrap container_wrap_first main_color main-first-section'>
        <div class='container'>
            <main class='template-page content'>
                <div class="woningen-wrapper">
                    <?php if(isset($_GET['type']) && isset($_GET['location'])) { ?>
                        <div class="filter-loader"><img src="<?php echo get_stylesheet_directory_uri(); ?>/media/molders-meza-loader.png"></div>
                    <?php } else { ?>
                        <div class="filter-loader" style="display: none;"><img src="<?php echo get_stylesheet_directory_uri(); ?>/media/molders-meza-loader.png"></div>
                    <?php } ?>
                    <div class="left-panel filter">
                        <div class="widget">
                            <h2>Woningzoeker</h2>
							<a href="javascript:void(0);" class="close-mobile-filter">x</a>
                            <div class="selected-filter-wrapper">
                                <div class="total-filter-selected">
                                    <div class="total-num"><span>0</span>filters</div>
                                    <div class="clear-filter">Verwijder filters</div>
                                </div>
                                <div class="selected-filter-list" style="display: none">
                                    <ul>
                                        
                                    </ul>
                                </div>
                            </div>
                            <div class="fitler-item woningtype-filter">
                                <label>Woningtype</label>
								<div class="select-design">
									<span>Alle Huizen</span>
                                    <input type="hidden" id="woningtype" name="woningtype" value="0">
									<ul class="select-design-lists">
										<li termid="0">Alle Huizen</li>
										<?php if(!empty($woningType)){ ?>
											<?php foreach($woningType as $wngType) { ?>                                            
												<li termid="<?php echo $wngType->term_id; ?>"><?php echo $wngType->name; ?></li>
											<?php } ?>
										<?php } ?>
									</ul>
								</div>
                            </div>
                            <div class="fitler-item waar-filter">
                                <label>Waar</label>
                                <input type="text" id="locationText" placeholder="Locatie">
                                <input type="hidden" id="locationID" name="location" value="0">
                            </div>
                            <div class="fitler-item prijs-filter">
                                <label>Prijs</label>
								
								<div class="price_value"><span>Van €<span class="prijs-filter-van"></span></span> <span>Total €<span class="prijs-filter-total"></span></span></div>
                                
                                <input type="hidden" id="minprc" value="">
                                <input type="hidden" id="maxprc" value="">

                                <input type="hidden" id="isSelect" value="">
                                <div class="slider-range-out">
                                    <div id="slider-range"></div>
                                </div>
                            </div>
                            <div class="fitler-item slaapkamers-filter">
                                <label>Slaapkamers</label>
								
								<div class="select-design">
									<span>Slaapkamers</span>
                                    <input type="hidden" id="slaapkamers" name="slaapkamers" value="0">
									<ul class="select-design-lists">
										<li termid="0">Slaapkamers</li>
										 <?php if(!empty($slaapkamerType)){ ?>
                                        <?php foreach($slaapkamerType as $slpkmrType) { ?>                                         
												<li termid="<?php echo $slpkmrType->term_id; ?>"><?php echo $slpkmrType->name; ?> of meer kamers</li>
											<?php } ?>
										<?php } ?>
									</ul>
								</div>
                            </div>
                            <div class="fitler-item kenmerken-filter">
                                <label>Kenmerken</label>
								<div class="kenmerken-items">
									<?php if(!empty($kenmerkenType)){ ?>
										<?php foreach($kenmerkenType as $knmrknType) { ?>     
											<div class="form-check">
												<input class="form-check-input" type="checkbox" value="<?php echo $knmrknType->term_id; ?>" id="knmr<?php echo $knmrknType->term_id; ?>">
												<label class="form-check-label" for="knmr<?php echo $knmrknType->term_id; ?>"><?php echo $knmrknType->name; ?></label>
											</div>                                           
										<?php } ?>
									<?php } ?>
								</div>
                                <div class="more-kenmerken">
                                    <a href="javascript:void(0);">Kenmerken</a>
                                </div>
                            </div>
							
							<a href="javascript:void(0);" class="avia-button avia-size-large woningen-tonen">Woningen tonen</a>
                        </div>
                        <div class="widget ref-filter-widget">
                            <div class="fitler-item">
                                <label>Ken jede referentie van het Wowoning?</label>
                                <div class="input-wrap">
                                    <input type="text" name="ref-invoeren" id="ref-invoeren" placeholder="REF invoeren">
                                    <button type="button">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="right-panel">
                        <div class="property-listing-wrapper">
                            <div class="result-top">
                                <div class="total-result">
                                    <span class="total-res-no"><?php echo $result->found_posts; ?> koopwoningen</span>
                                    <span class="result-locatie">In locatie</span>
                                </div>
								<div class="mobile-filter-button"><a href="javascript:void(0);"><span>Filter</span></a></div>
                                <div class="sorting-box">
									<div class="select-design">
    									<span>Nieuw</span>
                                        <input type="hidden" id="sortBy" name="sorting-by" value="13">
    									<ul class="select-design-lists">
    										<li termid="0">Sort By</li>
    										<?php if(!empty($statusType)){ ?>
                                                <?php foreach($statusType as $stusType) { ?>                                         
    												<li termid="<?php echo $stusType->term_id; ?>"><?php echo $stusType->name; ?></li>
    											<?php } ?>
    										<?php } ?>
    									</ul>
    								</div>
								</div>
                            </div>
                            <div class="property-main-list">
                                <?php if ( $result-> have_posts() ) : ?>
                                    <?php while ( $result->have_posts() ) : $result->the_post(); ?>
                                        <?php
                                            $prijs = get_post_meta($post->ID, 'prijs', true);
                                            $badkamers = get_post_meta($post->ID, 'badkamers', true);
                                            $bebouwd = get_post_meta($post->ID, 'bebouwd', true);

                                            $statusList = get_the_terms($post->ID, 'status');
                                            $statusName = '';
                                            $statusSlug = '';
                                            if(!empty($statusList)){
                                                $statusName = $statusList[0]->name;
                                                $statusSlug = $statusList[0]->slug;
                                            }

                                            $slaapkamerList = get_the_terms($post->ID, 'slaapkamer');
                                            $slaapkamerVal = '';
                                            if(!empty($slaapkamerList)){
                                                $slaapkamerVal = $slaapkamerList[0]->name;
                                            }

                                            $featureImage = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );

                                            $wishListClass = '';
                                            if(in_array($post->ID, $wishListCookie)){
                                                $wishListClass = 'added';
                                            }

                                            $locatiesType = get_the_terms($post->ID, 'locaties');
                                            usort($locatiesType, "sortByTermID");
                                            $locationString = '';
                                            if(!empty($locatiesType)){
                                                foreach($locatiesType as $lcType){
                                                    $locationString .= $lcType->name.', ';
                                                }
                                            }
                                        ?>
                                        <div class="property-item">
                                            <div class="wrapper">
                                                <div class="property-main-img">
                                                    <a href="<?php echo the_permalink(); ?>"><img src="<?php echo $featureImage[0]; ?>" /></a>
                                                    <?php if($statusName) { ?><span class="property-status <?php echo $statusSlug; ?>"><?php echo $statusName; ?></span><?php } ?>
                                                </div>
                                                <div class="property-info">
                                                    <div class="wishlist-btn"><a href="javascript:void(0);" id="prd<?php echo $post->ID; ?>" class="<?php echo $wishListClass; ?>"><i class="icon-heart"></i></a></div>
                                                    <div class="property-details">
                                                        <div class="locatie-name"><?php echo substr($locationString, 0, -2); ?></div>
                                                        <div class="property-name"><h2><a href="<?php echo the_permalink(); ?>"><?php the_title(); ?></a></h2></div>
                                                        <div class="property-price">Vanaf <span>€ <?php echo number_format($prijs, 0, '.', '.'); ?></span></div>
                                                    </div>
                                                    <div class="additional-info">
                                                        <ul>
                                                            <li class="slaapkamers-info"><img src="<?php echo get_stylesheet_directory_uri(); ?>/media/bed-icon.png" /><span><?php echo $slaapkamerVal; ?></span></li>
                                                            <li class="badkamers-info"><img src="<?php echo get_stylesheet_directory_uri(); ?>/media/bathroom-icon.png" /><span><?php echo $badkamers; ?></span></li>
                                                            <li class="bebouwd-info"><img src="<?php echo get_stylesheet_directory_uri(); ?>/media/area-icon.png" /><span><?php echo $bebouwd; ?></span></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                    <?php if($result->max_num_pages > 1) { ?>
                                        <div class="pagination">
                                            <?php 
                                                $big = 999999999; // need an unlikely integer
                                                echo  paginate_links(array(
                                                    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                                                    'format' => '?paged=%#%',
                                                    'current' => max(1, get_query_var('paged')),
                                                    'prev_text' => 'vorige',
                                                    'next_text' => 'volgende',
                                                    'total' => $result->max_num_pages //$q is your custom query
                                                ));
                                            ?>
                                        </div>
                                    <?php } ?>
                                <?php endif; ?>
                                <?php wp_reset_postdata(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div><!--end container-->
    </div><!-- close default .container_wrap element -->
    <?php if (is_active_sidebar('call-to-action-box-one') || is_active_sidebar('call-to-action-box-two')) { ?>
<div class="avia-section main_color avia-section-default pb-0 container_wrap fullsize call-to-action-box-main">
    <div class="container">
        <div class="template-page content  av-content-full alpha units">
            <div class="post-entry post-entry-type-page post-entry-2">
                <div class="entry-content-wrapper clearfix">
                    <?php if (is_active_sidebar('call-to-action-box-one')) { ?>
                        <div class="flex_column av_one_half flex_column_div av-zero-column-padding first el_before_av_one_half avia-builder-el-first">
                            <div class="avia-builder-widget-area clearfix avia-builder-el-no-sibling">
                                <?php dynamic_sidebar( 'call-to-action-box-one' ); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if (is_active_sidebar('call-to-action-box-two')) { ?>
                        <div class="flex_column av_one_half flex_column_div av-zero-column-padding el_after_av_one_half avia-builder-el-last">
                            <div class="avia-builder-widget-area clearfix avia-builder-el-no-sibling">
                                <?php dynamic_sidebar( 'call-to-action-box-two' ); ?>                                
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<?php if (is_active_sidebar('background-with-heading')) { ?>
<div class="avia-section main_color avia-section-large bg-with-heading-section default-margin container_wrap fullsize">
    <div class="container">
        <div class="template-page content av-content-full alpha units">
            <div class="post-entry post-entry-type-page">
                <div class="entry-content-wrapper clearfix">
                    <div class="flex_column av_one_full flex_column_div av-zero-column-padding first avia-builder-el-no-sibling">
                        <div class="avia-builder-widget-area clearfix avia-builder-el-no-sibling">
                            <?php dynamic_sidebar( 'background-with-heading' ); ?>     
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/jquery-ui.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/jquery.ui.touch-punch.min.js"></script>    
<script type="text/javascript">
	function numberWithThousandSeprator(x) {
	    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
	}

    jQuery(document).ready(function(){
        jQuery( "#slider-range" ).slider({
            range: true,
            min: <?php echo $minPrice; ?>,
            max: <?php echo $maxPrice; ?>,
            values: [ <?php echo $minPrice; ?>, <?php echo $maxPrice; ?> ],
            slide: function( event, ui ) {
                
				jQuery('.prijs-filter-van').text(numberWithThousandSeprator(ui.values[ 0 ]));
				jQuery('.prijs-filter-total').text(numberWithThousandSeprator(ui.values[ 1 ]));
            },
            stop: function( event, ui ) {
                jQuery("#minprc").val(ui.values[ 0 ]);
                jQuery("#maxprc").val(ui.values[ 1 ]);
                jQuery("#isSelect").val(1);
                
                propertyFilter();
            }
        });
        jQuery("#minprc").val(jQuery( "#slider-range" ).slider( "values", 0 ));
        jQuery("#maxprc").val(jQuery( "#slider-range" ).slider( "values", 1 ));
        
		jQuery('.prijs-filter-van').append(numberWithThousandSeprator(jQuery('#slider-range').slider('values', 0)));
		jQuery('.prijs-filter-total').append(numberWithThousandSeprator(jQuery('#slider-range').slider('values', 1)));
	
	
	
    });

    jQuery(document).ready(function(){
        jQuery('#woningtype').val(0);
        jQuery('#slaapkamers').val(0);
        jQuery('#sortBy').val(13);
        jQuery('.kenmerken-filter').find('input[type="checkbox"]').prop('checked', false);
        jQuery("#isSelect").val(0);
        jQuery('#ref-invoeren').val('');

        jQuery('#woningtype, #sortBy, #slaapkamers').change(function(){
            propertyFilter();
        });

        jQuery('.kenmerken-filter').find('input[type="checkbox"]').click(function(){
            propertyFilter();
        });

        jQuery(document).on('click', '.selected-filter-list li span', function(){
            jQuery(this).parent().remove();
            if( jQuery(this).hasClass('wngtype') ){
                jQuery('#woningtype').val(0);
                var wAll = jQuery('#woningtype').parents('.select-design').find('li:first').text();
                jQuery('#woningtype').parents('.select-design').find('span').text(wAll);
            }
            if( jQuery(this).hasClass('slpkmtype') ){
                jQuery('#slaapkamers').val(0);

                var spkAll = jQuery('#slaapkamers').parents('.select-design').find('li:first').text();
                jQuery('#slaapkamers').parents('.select-design').find('span').text(spkAll);
            }

            if( jQuery(this).hasClass('prcfltr') ){
                jQuery("#isSelect").val(0);

                jQuery( "#slider-range" ).slider({
                    range: true,
                    min: <?php echo $minPrice; ?>,
                    max: <?php echo $maxPrice; ?>,
                    values: [ <?php echo $minPrice; ?>, <?php echo $maxPrice; ?> ],
                });
                jQuery("#minprc").val(jQuery( "#slider-range" ).slider( "values", 0 ));
                jQuery("#maxprc").val(jQuery( "#slider-range" ).slider( "values", 1 ));
               
				jQuery('.prijs-filter-van').text(numberWithThousandSeprator(jQuery('#slider-range').slider('values', 0)));
				jQuery('.prijs-filter-total').text(numberWithThousandSeprator(jQuery('#slider-range').slider('values', 1)));
            }
            
            if( jQuery(this).hasClass('knmrkntype') ){
                var krmrClass = jQuery(this).attr('class');
                krmrClass = krmrClass.replace("knmrkntype ", "");

                jQuery('#'+krmrClass).prop('checked', false);
            }

            if( jQuery(this).hasClass('lct') ){
                jQuery('#locationID').val(0);
                jQuery('#locationText').val('');
            }
            

            propertyFilter();
        });

        jQuery(document).on('click', '.pagination a.page-numbers', function(e){
            e.preventDefault();
            if(jQuery(this).hasClass('prev')){
               var currNo = jQuery('.pagination').find('.page-numbers.current').text();
               var pageNo = (currNo * 1) - 1;
            }else if(jQuery(this).hasClass('next')){
               var currNo = jQuery('.pagination').find('.page-numbers.current').text();
               var pageNo = (currNo * 1) + 1;
            }else{
                var pageNo = jQuery(this).text();
            }

            propertyFilter(pageNo);
        });

        jQuery(document).on('click', '.clear-filter', function(e){
            jQuery('#woningtype').val(0);
            jQuery('#slaapkamers').val(0);
            jQuery('#sortBy').val(13);
            jQuery('.kenmerken-filter').find('input[type="checkbox"]').prop('checked', false);

            jQuery('#locationID').val(0);
            jQuery('#locationText').val('');

            var wAll = jQuery('#woningtype').parents('.select-design').find('li:first').text();
            jQuery('#woningtype').parents('.select-design').find('span').text(wAll);

            var spkAll = jQuery('#slaapkamers').parents('.select-design').find('li:first').text();
            jQuery('#slaapkamers').parents('.select-design').find('span').text(spkAll);

            jQuery('#sortBy').parents('.select-design').find('span').text('Nieuw');

            jQuery("#isSelect").val(0);
            jQuery( "#slider-range" ).slider({
                range: true,
                min: <?php echo $minPrice; ?>,
                max: <?php echo $maxPrice; ?>,
                values: [ <?php echo $minPrice; ?>, <?php echo $maxPrice; ?> ],
            });
            jQuery("#minprc").val(jQuery( "#slider-range" ).slider( "values", 0 ));
            jQuery("#maxprc").val(jQuery( "#slider-range" ).slider( "values", 1 ));
            
			jQuery('.prijs-filter-van').text(numberWithThousandSeprator(jQuery('#slider-range').slider('values', 0)));
			jQuery('.prijs-filter-total').text(numberWithThousandSeprator(jQuery('#slider-range').slider('values', 1)));

            propertyFilter();
        });

        jQuery(document).on('click', '.ref-filter-widget button', function(e){
            var refNo = jQuery.trim(jQuery('#ref-invoeren').val());
            if(refNo != ''){
                jQuery('.filter-loader').show();
                jQuery.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    data: {
                        action: 'get_property_list_by_refno',
                        refNo: refNo,
                    },
                    success: function (res) {
                        jQuery('.property-main-list').html(res.propertyHtml);
                        jQuery('.total-res-no').html(res.totalProperty+' koopwoningen');

                        jQuery('.filter-loader').hide();
                        var target = jQuery('.woningen-wrapper');
                        jQuery('html, body').animate({
                            scrollTop: target.offset().top
                        }, 500);
                    }
                });
            }
        });
		
		jQuery('.more-kenmerken a').click(function(){
			jQuery('.kenmerken-filter').toggleClass('active');
		});
		
		jQuery('.mobile-filter-button a').click(function(){
			jQuery('.left-panel').show();
		});
		jQuery('.close-mobile-filter').click(function(){
			jQuery('.left-panel').hide();
		});
		jQuery('.woningen-tonen').click(function(){
			jQuery('.left-panel').hide();
		});
		
    });

    function propertyFilter(pageNo=1){
        var filterSelectedString = '';
        var totalFilter = 0;
        var woningVal = 0;
        var slaapkamerVal = 0;
        var kenmerkenVal = '';

        if(jQuery("#woningtype").val() > 0){
            var woningType = jQuery("#woningtype").parents('.select-design').find('span').text();
            filterSelectedString += '<li><span class="wngtype">X</span>'+woningType+'</li>';
            woningVal = jQuery("#woningtype").val();
            totalFilter++;
        }

        if(jQuery("#slaapkamers").val() > 0){
            var slaapkamersType = jQuery("#slaapkamers").parents('.select-design').find('span').text();
            filterSelectedString += '<li><span class="slpkmtype">X</span>'+slaapkamersType+'</li>';
            slaapkamerVal = jQuery("#slaapkamers").val();
            totalFilter++;
        }

        jQuery('.kenmerken-filter').find('input[type="checkbox"]').each(function(){
            if(jQuery(this).is(':checked')){
                var kenmerkenType = jQuery(this).parent().find('label').text();
                var knmrVal = jQuery(this).val();
                kenmerkenVal += knmrVal+','; 
                filterSelectedString += '<li><span class="knmrkntype knmr'+knmrVal+'">X</span>'+kenmerkenType+'</li>';
                totalFilter++;
            }
        });

        kenmerkenVal = kenmerkenVal.slice(0,-1);

        var sortBy = jQuery('#sortBy').val();
        var minPrice = jQuery("#minprc").val();
        var maxPrice = jQuery("#maxprc").val();
        var locationID = jQuery("#locationID").val();

        var isSelect = jQuery("#isSelect").val();
        if(isSelect == 1){
            filterSelectedString += '<li><span class="prcfltr">X</span>€'+numberWithThousandSeprator(minPrice)+' - €'+numberWithThousandSeprator(maxPrice)+'</li>';
        }    

        if(locationID > 0){
            var lctName = jQuery('#locationText').val();
            filterSelectedString += '<li><span class="lct">X</span>'+lctName+'</li>';
            totalFilter++;
        }  

        jQuery('.total-filter-selected').find('.total-num span').text(totalFilter);
        if(totalFilter > 0){
			jQuery('.selected-filter-list ul').html(filterSelectedString);
			jQuery('.selected-filter-list').show();
		}else{
			jQuery('.selected-filter-list').hide();
		}	

        jQuery('.filter-loader').show();
        jQuery.ajax({
            type: "POST",
            dataType: 'json',
            url: "<?php echo admin_url('admin-ajax.php'); ?>",
            data: {
                action: 'get_property_list_by_filter',
                woningVal: woningVal,
                slaapkamerVal: slaapkamerVal,
                kenmerkenVal: kenmerkenVal,
                sortBy: sortBy,
                minPrice: minPrice,
                maxPrice: maxPrice,
                locationID: locationID,
                pageNo: pageNo
            },
            success: function (res) {
                jQuery('.property-main-list').html(res.propertyHtml);
                jQuery('.total-res-no').html(res.totalProperty+' koopwoningen');

                jQuery('.filter-loader').hide();
				var target = jQuery('.woningen-wrapper');
				jQuery('html, body').animate({
					scrollTop: target.offset().top
				}, 500);
            }
        });
    }
</script>

<?php 
get_footer();
