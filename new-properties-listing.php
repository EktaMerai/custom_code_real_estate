<?php
$limit = isset($params['limit']) ? $params['limit'] : 6;
$excludePostId = isset($params['exclude']) ? $params['exclude'] : 0;

$args = array(
    'post__not_in' => array($excludePostId),
    'post_type'=> 'woningen',
    'orderby'    => 'ID',
    'post_status' => 'publish',
    'order'    => 'DESC',
    'posts_per_page' => $limit,
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

$wishListCookie = array();
if(isset($_COOKIE['wishlist']) && $_COOKIE['wishlist'] != ''){
    $wishListCookie = explode(',', $_COOKIE['wishlist']);
}

?>

<div class="property-main-list grid">
    <?php if ( $result-> have_posts() ) : ?>
        <?php while ( $result->have_posts() ) : $result->the_post(); ?>
            <?php
                $prijs = get_post_meta(get_the_ID(), 'prijs', true);
                $badkamers = get_post_meta(get_the_ID(), 'badkamers', true);
                $bebouwd = get_post_meta(get_the_ID(), 'bebouwd', true);

                $statusList = get_the_terms(get_the_ID(), 'status');
                $statusName = '';
                $statusSlug = '';
                if(!empty($statusList)){
                    $statusName = $statusList[0]->name;
                    $statusSlug = $statusList[0]->slug;
                }

                $slaapkamerList = get_the_terms(get_the_ID(), 'slaapkamer');
                $slaapkamerVal = '';
                if(!empty($slaapkamerList)){
                    $slaapkamerVal = $slaapkamerList[0]->name;
                }

                $featureImage = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'single-post-thumbnail' );

                $wishListClass = '';
                if(in_array(get_the_ID(), $wishListCookie)){
                    $wishListClass = 'added';
                }

                $locatiesType = get_the_terms(get_the_ID(), 'locaties');
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
                        <div class="wishlist-btn"><a href="javascript:void(0);" id="prd<?php echo get_the_ID(); ?>" class="<?php echo $wishListClass; ?>"><i class="icon-heart"></i></a></div>
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
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>
</div>