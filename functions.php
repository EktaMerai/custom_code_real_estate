<?php
/*
* Add your own functions here. You can also copy some of the theme functions into this file. 
* Wordpress will use those functions instead of the original functions then.
*/

require_once 'property-custom-post.php';

add_shortcode( 'property-filter', 'property_custom_filter_function' );
function property_custom_filter_function() {
    ob_start();

    $woningType = get_terms([
        'taxonomy' => 'woningtype',
        'hide_empty' => true,
    ]);

    $locatiesList = get_terms([
        'taxonomy' => 'locaties',
        'hide_empty' => false,
        'parent' => 0
    ]);
	?>
        <div class="property-filter-main">
            <div class="wrapper">
                <div class="filter-item">
                    <label>Woningtype</label>
					<div class="select-design">
						<span>Alle Huizen</span>
                        <input type="hidden" id="house-type" name="house-type" value="0">
						<ul class="select-design-lists">
                            <li termid="0">Alle Huizen</li>
							<?php if(!empty($woningType)){ ?>
                                <?php foreach($woningType as $wngType) { ?>                                            
                                    <li termid="<?php echo $wngType->term_id; ?>"><?php echo $wngType->name; ?></li>
                                <?php } ?>
                            <?php } ?>
						</ul>
					</div>
                    <?php /*?><select name="house-type" id="house-type">
                        <option value="0">Alle Huizen</option>
                        <?php if(!empty($woningType)){ ?>
                            <?php foreach($woningType as $wngType) { ?>                                            
                                <option value="<?php echo $wngType->term_id; ?>"><?php echo $wngType->name; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select><?php */?>
                </div>
                <div class="filter-item">
                    <label>Waar</label>
                    <input type="text" id="locationText" placeholder="Vul in de locaties">
                    <input type="hidden" id="locationID" name="location" value="0">
                </div>
                <div class="filter-item search-btn">
                    <button>Huis Zoeken</button>
                </div>
            </div>
        </div>
        <div class="all-locaties-popup">Of kies uit deze <a href="javascript:void(0);">lijst met locaties</a></div>
    <?
	return ob_get_clean();
}
function avia_fix_breadcrumb_trail_dude($trail)
{
	foreach($trail as $key => $data)
	{
		if(strpos($data, 'Auto Draft') !== false) unset($trail[$key]);
	}
	return $trail;
}
add_filter('avia_breadcrumbs_trail','avia_fix_breadcrumb_trail_dude');

add_action( 'wp_ajax_get_property_list_by_filter', 'get_property_list_by_filter' );
add_action( 'wp_ajax_nopriv_get_property_list_by_filter', 'get_property_list_by_filter' );
function get_property_list_by_filter(){
    $woningVal = $_POST['woningVal'];
    $slaapkamerVal = $_POST['slaapkamerVal'];
    $kenmerkenVal = $_POST['kenmerkenVal'];
    $sortBy = $_POST['sortBy'];
    $locationID = $_POST['locationID'];

    $woningValOperator = 'NOT IN';
    if($woningVal > 0){
        $woningValOperator = 'IN';
    }

    $slaapkamerValOperator = 'NOT IN';
    $slappArray = array();
    if($slaapkamerVal > 0){
        $slaapkamerValOperator = 'IN';

        if($slaapkamerVal >= 3){
            $slaapkamerType = get_terms([
                'taxonomy' => 'slaapkamer',
                'hide_empty' => true,
            ]);
            if(!empty($slaapkamerType)){
                foreach($slaapkamerType as $slpkmrType) {
                    if($slpkmrType->slug >= $slaapkamerVal){
                        array_push($slappArray, $slpkmrType->slug);
                    }
                }
            }
        }else{
            array_push($slappArray, $slaapkamerVal);
        }
    }

    $kenmerkenValOperator = 'NOT IN';
    $kenmerkalArray = array();
    if($kenmerkenVal != ''){
        $kenmerkenValOperator = 'IN';
        $kenmerkalArray = explode(',',$kenmerkenVal);
    }

    $srtByField = 'meta_value_num';
    $srtByType = 'ASC';
    $srtMetaKey = 'wngStatus';
    
    $sortByOperator = 'NOT IN';

    if($sortBy == 'price-asc'){
        $srtByType = 'ASC';
        $srtMetaKey = 'prijs';
    }else if($sortBy == 'price-desc'){
        $srtByType = 'DESC';
        $srtMetaKey = 'prijs';
    }else{
        if( $sortBy > 0 ){
            $sortByOperator = 'IN';
        }
    }

    $locationOperator = 'NOT IN';
    if($locationID > 0){
        $locationOperator = 'IN';
    }

    $paged = $_POST['pageNo'];
    $minPrice = $_POST['minPrice'];
    $maxPrice = $_POST['maxPrice'];
    
    $args = array(
        'post_type'=> 'woningen',
        'post_status' => 'publish',
        'meta_key' => $srtMetaKey,
        'orderby'    => $srtByField,
        'order'    => $srtByType,
        'posts_per_page' => 10,
        'paged' => $paged,
        'relation' => 'AND',
        'tax_query' => array(
            array(
                'taxonomy' => 'woningtype',
                'field'    => 'id',
                'terms'    => $woningVal,
                'operator'  => $woningValOperator
            ),
            array(
                'taxonomy' => 'slaapkamer',
                'field'    => 'slug',
                'terms'    => $slappArray,
                'operator'  => $slaapkamerValOperator
            ),
            array(
                'taxonomy' => 'kenmerken',
                'field'    => 'id',
                'terms'    => $kenmerkalArray,
                'operator'  => $kenmerkenValOperator
            ),
            array(
                'taxonomy' => 'locaties',
                'field'    => 'id',
                'terms'    => $locationID,
                'operator'  => $locationOperator
            ),
            array(
                'taxonomy' => 'status',
                'field'    => 'id',
                'terms'    => $sortBy,
                'operator'  => $sortByOperator
            ),
        ),
        'meta_query' => array(
            array(
                'key' => 'prijs',
                'value' => array($minPrice, $maxPrice),
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC'
            )
        ),
    );
    $result = new WP_Query( $args );
    
    $wishListCookie = array();
    if(isset($_COOKIE['wishlist']) && $_COOKIE['wishlist'] != ''){
        $wishListCookie = explode(',', $_COOKIE['wishlist']);
    }

    $noResultFound = false;
    if ( !$result-> have_posts() ) {
        $result = array();
        $noResultFound = true;

        $typeName = '';
        $locationName = '';

        if($locationID > 0){
            $termLocatie = get_term_by( 'id', $locationID, 'locaties' ); 
            if(!empty($termLocatie)){
                $locationName = $termLocatie->name;
            }
        }

        if($woningVal > 0){
            $termType = get_term_by( 'id', $woningVal, 'woningtype' ); 
            if(!empty($termType)){
                $typeName = strtolower($termType->name);
            }
        }

        if($locationID > 0 && $woningVal > 0){
            $propertyHtml = '<div class="no_record_found">We hebben geen '.$typeName.' beschikbaar in '.$locationName.'</div>';
        }else if($locationID > 0){
            $propertyHtml = '<div class="no_record_found">We hebben geen woningen beschikbaar in '.$locationName.'</div>';
        }else if($woningVal > 0){
            $propertyHtml = '<div class="no_record_found">We hebben geen '.$typeName.' beschikbaar</div>';
        }else {
            $propertyHtml = '<div class="no_record_found">We hebben geen woningen beschikbaar</div>';
        }

        
        if($locationID > 0){
            $propertyHtml .= '<div class="same-region-property">Wel hebben wij deze woningen in '.$locationName.'</div>';
        }else{
            $propertyHtml .= '<div class="same-region-property">Wel hebben wij deze woningen</div>';    
        }
        

        $locationArray = array();
        $woningtypeArray = array();
        $slaapkamerArray = array();
        $kenmerkenArray = array();

        if($locationID > 0){
            $locationArray = array(
                'taxonomy' => 'locaties',
                'field'    => 'id',
                'terms'    => $locationID,
                'operator'  => $locationOperator
            );
        }else if($woningVal > 0){
            $woningtypeArray = array(
                'taxonomy' => 'woningtype',
                'field'    => 'id',
                'terms'    => $woningVal,
                'operator'  => $woningValOperator
            );
        }else{
            $slaapkamerArray = array(
                'taxonomy' => 'slaapkamer',
                'field'    => 'slug',
                'terms'    => $slappArray,
                'operator'  => $slaapkamerValOperator
            );

            $kenmerkenArray = array(
                'taxonomy' => 'kenmerken',
                'field'    => 'id',
                'terms'    => $kenmerkalArray,
                'operator'  => $kenmerkenValOperator
            );
        }

        $args = array(
            'post_type'=> 'woningen',
            'post_status' => 'publish',
            'meta_key' => $srtMetaKey,
            'orderby'    => $srtByField,
            'order'    => $srtByType,
            'posts_per_page' => 3,
            'paged' => 1,
            'relation' => 'AND',
            'tax_query' => array(
                'relation' => 'OR',
                $locationArray,
                $woningtypeArray,
                $slaapkamerArray,
                $kenmerkenArray,
            )
        );
        $result = new WP_Query( $args );
    }else{
        $propertyHtml = '';        
    }

    if ( $result-> have_posts() ) {
        while ( $result->have_posts() ) { $result->the_post();
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

            $propertyHtml .= '<div class="property-item">';
                $propertyHtml .= '<div class="wrapper">';
                    $propertyHtml .= '<div class="property-main-img">';
                        $propertyHtml .= '<a href="'.get_permalink().'"><img src="'.$featureImage[0].'" /></a>';
                        if($statusName) {
                            $propertyHtml .= '<span class="property-status '.$statusSlug.'">'.$statusName.'</span>';
                        }
                    $propertyHtml .= '</div>';    
                    $propertyHtml .= '<div class="property-info">';    
                        $propertyHtml .= '<div class="wishlist-btn"><a href="javascript:void(0);" id="prd'.get_the_ID().'" class="'.$wishListClass.'"><i class="icon-heart"></i></a></div>';    
                        $propertyHtml .= '<div class="property-details">';    
                            $propertyHtml .= '<div class="locatie-name">'.substr($locationString, 0, -2).'</div>';    
                            $propertyHtml .= '<div class="property-name"><h2><a href="'.get_permalink().'">'.get_the_title().'</a></h2></div>';    
                            $propertyHtml .= '<div class="property-price">Vanaf <span>€ '.number_format($prijs, 0, ".", ".").'</span></div>';    
                        $propertyHtml .= '</div>';    
                        $propertyHtml .= '<div class="additional-info">';    
                            $propertyHtml .= '<ul>';    
                                $propertyHtml .= '<li class="slaapkamers-info"><img src="'.get_stylesheet_directory_uri().'/media/bed-icon.png" /><span>'.$slaapkamerVal.'</span></li>';    
                                $propertyHtml .= '<li class="badkamers-info"><img src="'.get_stylesheet_directory_uri().'/media/bathroom-icon.png" /><span>'.$badkamers.'</span></li>';    
                                $propertyHtml .= '<li class="bebouwd-info"><img src="'.get_stylesheet_directory_uri().'/media/area-icon.png" /><span>'.$bebouwd.'</span></li>';    
                            $propertyHtml .= '</ul>';    
                        $propertyHtml .= '</div>';    
                    $propertyHtml .= '</div>';    
                $propertyHtml .= '</div>';    
            $propertyHtml .= '</div>';    
        }
        if($result->max_num_pages > 1 && !$noResultFound) {
            $propertyHtml .= '<div class="pagination">';   
                $big = 999999999; 
                $propertyHtml .= paginate_links(array(
                    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                    'format' => '?paged=%#%',
                    'current' => max(1, $paged),
                    'prev_text' => 'vorige',
                    'next_text' => 'volgende',
                    'total' => $result->max_num_pages 
                ));
            $propertyHtml .= '</div>'; 
        }
    }else{      
        //$propertyHtml .= '<div class="no_record_found">Geen huizen gevonden.</div>'; 
    } 
    wp_reset_postdata();    

    $returnArray = array();
    $returnArray['propertyHtml'] = $propertyHtml;
    $returnArray['totalProperty'] = ($noResultFound) ? count($result->posts) : $result->found_posts;
    
    echo json_encode($returnArray); die();
}

add_shortcode('new-properties' , 'newPropertyListing');
function newPropertyListing($params=""){
    ob_start();
        require_once 'new-properties-listing.php';
    return ob_get_clean();
}

add_action( 'wp_ajax_get_property_list_by_refno', 'get_property_list_by_refno' );
add_action( 'wp_ajax_nopriv_get_property_list_by_refno', 'get_property_list_by_refno' );
function get_property_list_by_refno(){
    $refNo = $_POST['refNo'];
    $args = array(
        'post_type'=> 'woningen',
        'orderby'    => 'ID',
        'post_status' => 'publish',
        'order'    => 'DESC',
        'posts_per_page' => -1,
        'relation' => 'AND',
        'meta_query' => array(
            array(
                'key' => 'refno',
                'value' => $refNo,
                'compare' => '=',
            )
        ),
    );
    $result = new WP_Query( $args );
    
    $wishListCookie = array();
    if(isset($_COOKIE['wishlist']) && $_COOKIE['wishlist'] != ''){
        $wishListCookie = explode(',', $_COOKIE['wishlist']);
    }

    $propertyHtml = '';
    if ( $result-> have_posts() ) {
        while ( $result->have_posts() ) { $result->the_post();
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

            $propertyHtml .= '<div class="property-item">';
                $propertyHtml .= '<div class="wrapper">';
                    $propertyHtml .= '<div class="property-main-img">';
                        $propertyHtml .= '<a href="'.get_permalink().'"><img src="'.$featureImage[0].'" /></a>';
                        if($statusName) {
                            $propertyHtml .= '<span class="property-status '.$statusSlug.'">'.$statusName.'</span>';
                        }
                    $propertyHtml .= '</div>';    
                    $propertyHtml .= '<div class="property-info">';    
                        $propertyHtml .= '<div class="wishlist-btn"><a href="javascript:void(0);" id="prd'.get_the_ID().'" class="'.$wishListClass.'"><i class="icon-heart"></i></a></div>';    
                        $propertyHtml .= '<div class="property-details">';    
                            $propertyHtml .= '<div class="locatie-name">'.substr($locationString, 0, -2).'</div>';    
                            $propertyHtml .= '<div class="property-name"><h2><a href="'.get_permalink().'">'.get_the_title().'</a></h2></div>';    
                            $propertyHtml .= '<div class="property-price">Vanaf <span>€ '.number_format($prijs, 0, ".", ".").'</span></div>';    
                        $propertyHtml .= '</div>';    
                        $propertyHtml .= '<div class="additional-info">';    
                            $propertyHtml .= '<ul>';    
                                $propertyHtml .= '<li class="slaapkamers-info"><img src="'.get_stylesheet_directory_uri().'/media/bed-icon.png" /><span>'.$slaapkamerVal.'</span></li>';    
                                $propertyHtml .= '<li class="badkamers-info"><img src="'.get_stylesheet_directory_uri().'/media/bathroom-icon.png" /><span>'.$badkamers.'</span></li>';    
                                $propertyHtml .= '<li class="bebouwd-info"><img src="'.get_stylesheet_directory_uri().'/media/area-icon.png" /><span>'.$bebouwd.'</span></li>';    
                            $propertyHtml .= '</ul>';    
                        $propertyHtml .= '</div>';    
                    $propertyHtml .= '</div>'; 
                $propertyHtml .= '</div>';    
            $propertyHtml .= '</div>';    
        }
    } else {
        $propertyHtml .= '<div class="no_record_found">Geen huizen gevonden.</div>'; 
    }    
    
    wp_reset_postdata();    

    $returnArray = array();
    $returnArray['propertyHtml'] = $propertyHtml;
    $returnArray['totalProperty'] = $result->found_posts;
    
    echo json_encode($returnArray); die();
}

add_shortcode('wishlist-property' , 'wishlistPropertyListing');
function wishlistPropertyListing(){
    ob_start();
        require_once 'wishlist-properties-listing.php';
    return ob_get_clean();
}

function sortByTermID($a, $b) {
  return $a->parent > $b->parent;
}

add_action('pre_get_posts', 'kill_taxonomy_archive');
function kill_taxonomy_archive($qry) {

    if (is_admin()) return;

    if (is_tax('kenmerken') || is_tax('locaties') || is_tax('woningtype') || is_tax('slaapkamer') || is_tax('status') || is_tax('debuurt')){
        $qry->set_404();
    }
}