<?php
$wishListCookie = array();
if(isset($_COOKIE['wishlist']) && $_COOKIE['wishlist'] != ''){
    $wishListCookie = explode(',', $_COOKIE['wishlist']);
}
?>
<?php if(!empty($wishListCookie)) { ?>
	<div class="property-main-list grid wishlist">
		<?php foreach($wishListCookie as $wishID) { ?>
			<?php
                $prijs = get_post_meta($wishID, 'prijs', true);
                $badkamers = get_post_meta($wishID, 'badkamers', true);
                $bebouwd = get_post_meta($wishID, 'bebouwd', true);

                $statusList = get_the_terms($wishID, 'status');
                $statusName = '';
                $statusSlug = '';
                if(!empty($statusList)){
                    $statusName = $statusList[0]->name;
                    $statusSlug = $statusList[0]->slug;
                }

                $slaapkamerList = get_the_terms($wishID, 'slaapkamer');
                $slaapkamerVal = '';
                if(!empty($slaapkamerList)){
                    $slaapkamerVal = $slaapkamerList[0]->name;
                }

                $featureImage = wp_get_attachment_image_src( get_post_thumbnail_id( $wishID ), 'single-post-thumbnail' );

                $wishListClass = '';
                if(in_array($wishID, $wishListCookie)){
                    $wishListClass = 'added';
                }

                $locatiesType = get_the_terms($wishID, 'locaties');
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
                        <a href="<?php echo get_permalink($wishID); ?>"><img src="<?php echo $featureImage[0]; ?>" /></a>
                        <?php if($statusName) { ?><span class="property-status <?php echo $statusSlug; ?>"><?php echo $statusName; ?></span><?php } ?>
                    </div>
                    <div class="property-info">
                        <div class="wishlist-btn"><a href="javascript:void(0);" id="prd<?php echo $wishID; ?>" class="<?php echo $wishListClass; ?>"><i class="icon-heart"></i></a></div>
                        <div class="property-details">
                            <div class="locatie-name"><?php echo substr($locationString, 0, -2); ?></div>
                            <div class="property-name"><h2><a href="<?php echo get_permalink($wishID); ?>"><?php echo get_the_title( $wishID ); ?></a></h2></div>
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
		<?php } ?>	
	</div>
<?php } ?>
<div class="empty-wishlist" <?php if(!empty($wishListCookie)) { ?>style="display: none;"<?php } ?>>
    Je lijst met favoriete huizen is leeg.
</div>