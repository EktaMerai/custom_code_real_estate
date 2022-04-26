<?php
if ( !defined('ABSPATH') ){ die(); }
global $avia_config, $wp_query;
get_header();
do_action( 'ava_page_template_after_header' );
    if( get_post_meta(get_the_ID(), 'header', true) != 'no') echo avia_title();
    do_action( 'ava_after_main_title' );

$gallery_data = get_post_meta( $post->ID, 'gallery_data', true );
$imageCount = isset($gallery_data['image_url']) ? count($gallery_data['image_url']) : 0;

$property_video = get_post_meta( $post->ID, 'property_video', true );
$totalDisplayImage = 4;
$videoId = '';
if($property_video){
    $value = explode("v=", $property_video);
    $videoId = $value[1];
    if($videoId) {
        $totalDisplayImage = 3;
    }
}
?>
<link href="<?php echo get_stylesheet_directory_uri(); ?>/css/tobii.css" rel="stylesheet">
<div class='container_wrap container_wrap_first main_color main-first-section'>
    <div class='container'>
        <main class='template-page content'>
            <?php if($imageCount > 0 || $videoId != '') { ?>
                <div class="proprty-gallery-main">
                    <div class="property-galley">
                        <div class="gallery-main">
                            <?php if($imageCount > 0) { ?>
                                <div class="property-media-foto col-span-2 row-span-2">
                                    <a href="<?php echo $gallery_data['image_url'][0]; ?>" class="lightbox" data-group="gallery" id="frstGalleryImage"><img src="<?php echo $gallery_data['image_url'][0]; ?>" /></a>
								</div>
                            <?php } ?>
                            <?php if($videoId) { ?>
                               <div class="property-media-foto property-video-thumb">
                                   <a href="javascript:void(0);" data-type="youtube" data-id="<?php echo $videoId; ?>" class="lightbox" data-group="video"><img src="https://img.youtube.com/vi/<?php echo $videoId; ?>/hqdefault.jpg" /></a>
                                </div>
							<?php } ?>
                            <?php if($imageCount > 0) {?>
                                <?php $i=0; foreach($gallery_data['image_url'] as $imageUrl) { ?>
                                    <?php if($i > 0 && $i <= $totalDisplayImage) { ?>
                                        <div class="property-media-foto">
                                            <a href="<?php echo $imageUrl; ?>" class="lightbox" data-group="gallery"><img src="<?php echo $imageUrl; ?>" /></a>
                                        </div>
                                    <?php } else { ?>
                                        <?php if($i > 0) { ?>
                                            <div class="property-media-foto" style="display: none;">
                                                <a href="<?php echo $imageUrl; ?>" class="lightbox" data-group="gallery"><img src="<?php echo $imageUrl; ?>" /></a>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                <?php $i++; } ?>
                            <?php } ?>
							
							<button type="button" data-type="html" data-target="#modal" class="lightbox" data-group="gallery" style="display: none;">Open Form</button>
						</div>
                    </div>
                    <div class="proprety-gallery-info">
                        <ul>
                            <?php if($imageCount > 0) { ?>
                                <li class="photos"><a href="javascript:void(0);"><img src="<?php echo get_stylesheet_directory_uri(); ?>/media/photos-icon.png" />Foto's <span><?php echo $imageCount; ?></span></a></li>
                            <?php } ?>
                            <?php if($videoId) { ?><li class="video"><a href="javascript:void(0);"><img src="<?php echo get_stylesheet_directory_uri(); ?>/media/video-icon.png" />Video</a></li><?php } ?>
                        </ul>
                    </div>
                </div>
            <?php } ?>
            <div class="proprty-detail-main">
                <div class="left-panel">
                    <?php
                        $kenmerkenType = get_the_terms($post->ID, 'kenmerken');
                        $debuurtType = get_the_terms($post->ID, 'debuurt');
                        
                        $prijs = get_post_meta($post->ID, 'prijs', true);
                        $badkamers = get_post_meta($post->ID, 'badkamers', true);
                        $refno = get_post_meta($post->ID, 'refno', true);
                        $perceel = get_post_meta($post->ID, 'perceel', true);
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

                        $woningType = get_the_terms($post->ID, 'woningtype');
                        $woningVal = '';
                        if(!empty($woningType)){
                            $woningVal = $woningType[0]->name;
                        }

                        $locatiesType = get_the_terms($post->ID, 'locaties');
                        usort($locatiesType, "sortByTermID");
                        $locationString = '';
                        if(!empty($locatiesType)){
                            foreach($locatiesType as $lcType){
                                $locationString .= $lcType->name.', ';
                            }
                        }

                        $wishListCookie = array();
                        if(isset($_COOKIE['wishlist']) && $_COOKIE['wishlist'] != ''){
                            $wishListCookie = explode(',', $_COOKIE['wishlist']);
                        }

                        $wishListClass = '';
                        if(in_array($post->ID, $wishListCookie)){
                            $wishListClass = 'added';
                        }
                        
                    ?>
                    <div class="property-overview">
                        <div class="property-meta">
                            <?php if($statusName) { ?><span class="property-status <?php echo $statusSlug; ?>"><?php echo $statusName; ?></span><?php } ?>
                            <div class="property-right-meta">
                                <div class="wishlist-btn"><a href="javascript:void(0);" id="prd<?php echo $post->ID; ?>" class="<?php echo $wishListClass; ?>"><i class="icon-heart"></i></a></div>
                                <div class="share-btn"><a href="#" class=""><i class="icon-share"></i></a> <?php echo do_shortcode('[av_social_share]'); ?></div>
                            </div>
                        </div>
                        <div class="property-details">
                            <div class="locatie-name"><?php echo substr($locationString, 0, -2); ?></div>
                            <div class="property-name"><h1><?php the_title(); ?></h1></div>
                            <div class="property-price">Vanaf <span>€ <?php echo number_format($prijs, 0, '.', '.'); ?></span></div>
                        </div>
                        <div class="additional-info">
                            <ul>
                                <?php if($slaapkamerVal) { ?>
                                    <li class="slaapkamers-info"><img src="<?php echo get_stylesheet_directory_uri(); ?>/media/bed-icon.png" /><span><?php echo $slaapkamerVal; ?></span></li>
                                <?php } ?>
                                <?php if($badkamers) { ?>
                                    <li class="badkamers-info"><img src="<?php echo get_stylesheet_directory_uri(); ?>/media/bathroom-icon.png" /><span><?php echo $badkamers; ?></span></li>
                                <?php } ?>
                                <?php if($bebouwd) { ?>
                                    <li class="bebouwd-info"><img src="<?php echo get_stylesheet_directory_uri(); ?>/media/area-icon.png" /><span><?php echo $bebouwd; ?></span></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <div class="section-sep clearfix"></div>
                    <div class="property-description showlesscontent">
                        <?php the_content(); ?>
                    </div>
                    <div class="section-sep clearfix"></div>
                    <div class="kenmerken-section">
                        <div class="card">
                            <div class="card-header"><h3>Kenmerken</h3></div>
                            <div class="card-body">
                                <ul>
                                    <?php if($refno) { ?><li><span class="item-heading">Ref:</span><span class="item-value"><?php echo $refno; ?></span></li><?php } ?>
                                    <?php if($woningVal) { ?><li><span class="item-heading">Woningtype:</span><span class="item-value"><?php echo $woningVal; ?></span></li><?php } ?>
                                    <?php if($prijs) { ?><li><span class="item-heading">Prijs:</span><span class="item-value">€ <?php echo $prijs ?></span></li><?php } ?>
                                    <?php if($bebouwd) { ?><li><span class="item-heading">Bebouwd:</span><span class="item-value"><?php echo $bebouwd; ?></span></li><?php } ?>
                                    <?php if($perceel) { ?><li><span class="item-heading">Perceel:</span><span class="item-value"><?php echo $perceel; ?> m<sup>2</sup></span></li><?php } ?>
                                    <?php if($slaapkamerVal) { ?><li><span class="item-heading">Slaapkamers:</span><span class="item-value"><?php echo $slaapkamerVal; ?></span></li><?php } ?>
                                    <?php if($badkamers) { ?><li><span class="item-heading">Badkamers:</span><span class="item-value"><?php echo $badkamers; ?></span></li><?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="section-sep clearfix"></div>
                    <?php if(!empty($kenmerkenType)){ ?>
                    <div class="pluspunten-section">
                        <div class="card">
                            <div class="card-header"><h3>Pluspunten</h3></div>
                            <div class="card-body">
                                <ul class="check-list">
                                    <?php foreach($kenmerkenType as $kenmerItem) { ?>                                            
                                        <li><?php echo $kenmerItem->name; ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="section-sep clearfix"></div>
                    <?php } ?>
                    <?php if(!empty($debuurtType)){ ?>
                    <div class="de-buurt-section">
                        <div class="card">
                            <div class="card-header"><h3>Wat is in de buurt</h3></div>
                            <div class="card-body">
                                <ul>
                                    <?php foreach($debuurtType as $debuurtItem) { ?> 
                                        <?php $debuurtImage = get_term_meta($debuurtItem->term_id); ?>
                                        <li><img src="<?php echo $debuurtImage['debuurt_image'][0]; ?>" /><?php echo $debuurtItem->name; ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="section-sep clearfix"></div>
                    <?php } ?>

                    <div class="new-related-property">
                        <h3>Nieuwe woningen</h3>
                        <?php echo do_shortcode('[new-properties limit="3" exclude="'.$post->ID.'"]');?>
                    </div>

                </div>
                <div class="right-panel">
                    <?php if (is_active_sidebar('interesse-in-deze-woning')) { ?>
                        <div class="interesse-in-deze-woning">
                            <?php dynamic_sidebar( 'interesse-in-deze-woning' ); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </main>
    </div>
</div>
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
<div id="modal" class="modal">
	<div class="modal__inner">
		<div class="property-details">
            <div class="locatie-name"><?php echo substr($locationString, 0, -2); ?></div>
            <div class="property-name"><h1><?php the_title(); ?></h1></div>
            <div class="property-price">Vanaf <span>€ <?php echo number_format($prijs, 0, '.', '.'); ?></span></div>
        </div>
		<div class="lightboxform">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/media/person-img.png">
			<?php echo do_shortcode('[gravityform id="2" title="true" description="true" ajax="true" tabindex="48"]'); ?>
		</div>
	</div>
</div>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/tobii.js"></script>
<script>
  var tobii = new Tobii();
</script>
<script>
	jQuery(document).ready(function(){
		function AddReadMore() {
			var carLmt = 450;
			var readMoreTxt = " + Lees meer";
			var readLessTxt = " - Lees minder";

			jQuery(".property-description").each(function() {
				if (jQuery(this).find(".firstSec").length)
					return;

				var allstr = jQuery(this).text();
				if (allstr.length > carLmt) {
					var firstSet = allstr.substring(0, carLmt);
					var secdHalf = allstr.substring(carLmt, allstr.length);
					var strtoadd = firstSet + "<span class='SecSec'>" + secdHalf + "</span><span class='readMore'  title='Click to Show More'>" + readMoreTxt + "</span><span class='readLess' title='Click to Show Less'>" + readLessTxt + "</span>";
					jQuery(this).html(strtoadd);
				}

			});
			jQuery(document).on("click", ".readMore,.readLess", function() {
				jQuery(this).closest(".property-description").toggleClass("showlesscontent showmorecontent");
			});
		}
		jQuery(function() {
			AddReadMore();
		});
		
		jQuery('.proprety-gallery-info .photos a').click(function(){
			jQuery('.property-media-foto.col-span-2').find('img').trigger('click');
		});

		jQuery('.proprety-gallery-info .video a').click(function(){
			jQuery('.property-video-thumb').find('img').trigger('click');
		});

        jQuery('.share-btn > a').click(function(event){
			jQuery('.av-social-sharing-box').toggleClass('active');
            event.preventDefault();
		});
	});
</script>
<?php 
get_footer();