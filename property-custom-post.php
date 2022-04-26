<?php

function create_custom_post_and_taxonomy(){
	register_post_type(
		'woningen',
		array(
			'thumbnail',
			'labels' => array(
				'name' => __('Woningen'),
				'singular_name' => __('Woningen')
			),
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt'),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'woningen'),
		)
	);

	register_taxonomy(
		'locaties',
		'woningen',
		array(
			'hierarchical' => true,
			'label' => 'Locaties',
			'query_var' => true,
			'rewrite' => array(
				'slug' => 'locaties',
				'with_front' => false  
			)
		)
	);

    register_taxonomy(
        'woningtype',
        'woningen',
        array(
            'hierarchical' => true,
            'label' => 'Woning Type',
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'woningtype',
                'with_front' => false  
            )
        )
    );

	register_taxonomy(
		'slaapkamer',
		'woningen',
		array(
			'hierarchical' => true,
			'label' => 'Slaapkamers',
            'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array(
				'slug' => 'slaapkamer',
				'with_front' => false  
			)
		)
	);

	register_taxonomy(
		'kenmerken',
		'woningen',
		array(
			'hierarchical' => true,
			'label' => 'Kenmerkens',
			'query_var' => true,
			'rewrite' => array(
				'slug' => 'kenmerken',
				'with_front' => false  
			)
		)
	);

	register_taxonomy(
		'status',
		'woningen',
		array(
			'hierarchical' => true,
			'label' => 'Status',
            'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array(
				'slug' => 'status',
				'with_front' => false  
			)
		)
	);

	register_taxonomy(
		'debuurt',
		'woningen',
		array(
			'hierarchical' => true,
			'label' => 'De Buurt',
			'query_var' => true,
			'rewrite' => array(
				'slug' => 'de-buurt',
				'with_front' => false  
			)
		)
	);
}

add_action('init', 'create_custom_post_and_taxonomy');


// Taxonomy DeBuurt Email field
function debuurt_add_custom_field() {
    ?>
    <div class="form-field term-image-wrap">
        <label for="debuurt_image"><?php _e( 'Image' ); ?></label>
        <p><a href="#" class="aw_upload_image_button button button-secondary"><?php _e('Upload Image'); ?></a></p>
        <input type="text" name="debuurt_image" id="debuurt_image" value="" size="40" />
    </div>
    <?php
}
add_action( 'debuurt_add_form_fields', 'debuurt_add_custom_field', 10, 2 );

function debuurt_edit_custom_field($term) {
    $image = get_term_meta($term->term_id, 'debuurt_image', true);
    ?>
    <tr class="form-field term-image-wrap">
        <th scope="row"><label for="debuurt_image"><?php _e( 'Image' ); ?></label></th>
        <td>
            <p><a href="#" class="aw_upload_image_button button button-secondary"><?php _e('Upload Image'); ?></a></p><br/>
            <input type="text" name="debuurt_image" id="debuurt_image" value="<?php echo $image; ?>" size="40" />
        </td>
    </tr>
    <?php
}
add_action( 'debuurt_edit_form_fields', 'debuurt_edit_custom_field', 10, 2 );

function aw_include_script() {
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }
  
    wp_enqueue_script( 'awscript', get_stylesheet_directory_uri() . '/js/awscript.js', array('jquery'), null, false );
}
add_action( 'admin_enqueue_scripts', 'aw_include_script' );

function save_taxonomy_custom_meta_field( $term_id ) {
    if ( isset( $_POST['debuurt_image'] ) ) {
        update_term_meta($term_id, 'debuurt_image', $_POST['debuurt_image']);
    }
}  
add_action( 'edited_debuurt', 'save_taxonomy_custom_meta_field', 10, 2 );  
add_action( 'create_debuurt', 'save_taxonomy_custom_meta_field', 10, 2 );


// Taxonomy Kenmerkens Search field
function kenmerken_add_custom_field() {
    ?>
    <div class="form-field term-image-wrap">
        <label for="useForFilter"><?php _e( 'Use for filter' ); ?></label>
        <p><input type="checkbox" name="useForFilter" id="useForFilter" value="yes"></p>
    </div>
    <?php
}
add_action( 'kenmerken_add_form_fields', 'kenmerken_add_custom_field', 10, 2 );

function kenmerken_edit_custom_field($term) {
    $useForFilter = get_term_meta($term->term_id, 'useForFilter', true);
    ?>
    <tr class="form-field term-image-wrap">
        <th scope="row"><label for="useForFilter"><?php _e( 'Use for filter' ); ?></label></th>
        <td>
            <p><input type="checkbox" name="useForFilter" id="useForFilter" value="yes" <?php if($useForFilter == 'yes') { echo 'checked'; } ?>></p>
        </td>
    </tr>
    <?php
}
add_action( 'kenmerken_edit_form_fields', 'kenmerken_edit_custom_field', 10, 2 );

function kenmerken_save_taxonomy_custom_meta_field( $term_id ) {
    $useForFilter = isset($_POST['useForFilter']) ? $_POST['useForFilter'] : 'no';
    update_term_meta($term_id, 'useForFilter', $useForFilter);
}  
add_action( 'edited_kenmerken', 'kenmerken_save_taxonomy_custom_meta_field', 10, 2 );  
add_action( 'create_kenmerken', 'kenmerken_save_taxonomy_custom_meta_field', 10, 2 );

//property custom post type meta box
function woningen_metabox()
{
    add_meta_box(
        'woningen_metabox_id',       // $id
        'Additional Information',                  // $title
        'woningen_metabox_callback',  // $callback
        'woningen',                 // $page
        'normal',                  // $context
        'high'                     // $priority
    );
}
add_action('add_meta_boxes', 'woningen_metabox');

function woningen_metabox_callback(){
    global $post;
    $prijs = get_post_meta($post->ID, 'prijs', true);
    $refno = get_post_meta($post->ID, 'refno', true);
    $perceel = get_post_meta($post->ID, 'perceel', true);
    $badkamers = get_post_meta($post->ID, 'badkamers', true);
    $bebouwd = get_post_meta($post->ID, 'bebouwd', true);
    ?>
    <div class="product-listing">
        <table border="0" cellading="0" cellspacing="0" class="form-table cuztom-table">
            <tbody>
                <tr>
                	<th><label for="prijs">Prijs</label></th>
                    <td class="cuztom-td">
                        <input type="text" id="prijs" name="prijs" value="<?php echo $prijs; ?>">
                    </td>
                </tr>
                <tr>
                	<th><label for="refno">Ref No.</label></th>
                    <td class="cuztom-td">
                        <input type="text" id="refno" name="refno" value="<?php echo $refno; ?>">
                    </td>
                </tr>
                <tr>
                	<th><label for="perceel">Perceel</label></th>
                    <td class="cuztom-td">
                        <input type="text" id="perceel" name="perceel" value="<?php echo $perceel; ?>">
                    </td>
                </tr>
                <tr>
                	<th><label for="badkamers">Badkamers</label></th>
                    <td class="cuztom-td">
                        <input type="text" id="badkamers" name="badkamers" value="<?php echo $badkamers; ?>">
                    </td>
                </tr>
                <tr>
                	<th><label for="bebouwd">Bebouwd</label></th>
                    <td class="cuztom-td">
                        <input type="text" id="bebouwd" name="bebouwd" value="<?php echo $bebouwd; ?>">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <?php
}

function save_woningen_additional_meta($post_id)
{
    $post_type = isset($_POST['post_type']) ? $_POST['post_type'] : '';
    if ('woningen' == $post_type) {
       update_post_meta($post_id, 'prijs', $_POST['prijs']);
       update_post_meta($post_id, 'refno', $_POST['refno']);
       update_post_meta($post_id, 'perceel', $_POST['perceel']);
       update_post_meta($post_id, 'badkamers', $_POST['badkamers']);
       update_post_meta($post_id, 'bebouwd', $_POST['bebouwd']);
    }
}
add_action('save_post', 'save_woningen_additional_meta');
add_action('new_to_publish', 'save_woningen_additional_meta');

function woningen_gallery_metabox()
{
    add_meta_box(
        'woningen_gallery_metabox_id',       // $id
        'Image Gallery',                  // $title
        'woningen_gallery_metabox_callback',  // $callback
        'woningen',                 // $page
        'normal',                  // $context
        'high'                     // $priority
    );
}
add_action('add_meta_boxes', 'woningen_gallery_metabox');

function woningen_gallery_metabox_callback(){
    global $post;
    $gallery_data = get_post_meta( $post->ID, 'gallery_data', true );
?>
<div id="dynamic_form">
    <div id="field_wrap">
    <?php 
        if ( isset( $gallery_data['image_url'] ) ) {
            for( $i = 0; $i < count( $gallery_data['image_url'] ); $i++ ) {
            ?>
                <div class="field_row">
                    <div class="field_left">
                        <div class="form_field">
                            <label>Image URL</label>
                            <input type="text" class="meta_image_url" name="gallery[image_url][]" value="<?php esc_html_e( $gallery_data['image_url'][$i] ); ?>" />
                        </div>
                    </div>

                    <div class="field_right image_wrap">
                        <img src="<?php esc_html_e( $gallery_data['image_url'][$i] ); ?>" height="48" width="48" />
                    </div>

                    <div class="field_right">
                        <input class="button addimage" type="button" value="Choose File" />
                        <input class="button" type="button" value="Remove" onclick="remove_field(this)" />
                    </div>

                    <div class="clear" /></div> 
                </div>
            <?php
            } 
        } 
    ?>
    </div>
 
    <div style="display:none" id="master-row">
        <div class="field_row">
            <div class="field_left">
                <div class="form_field">
                    <label>Image URL</label>
                    <input class="meta_image_url" value="" type="text" name="gallery[image_url][]" />
                </div>
            </div>
            <div class="field_right image_wrap">
            </div> 
            <div class="field_right"> 
                <input type="button" class="button addimage" value="Choose File" />
                <input class="button" type="button" value="Remove" onclick="remove_field(this)" /> 
            </div>
            <div class="clear"></div>
        </div>
    </div>
 
    <div id="add_field_row">
        <input class="button" type="button" value="Add Field" onclick="add_field_row();" />
    </div>
</div>
    <style type="text/css">
      .field_left { float:left; }
      .field_right { float:left; margin-left:10px; }
      .clear { clear:both; }
      #dynamic_form { width:100%; }
      #dynamic_form input[type=text] { width:650px; }
      #dynamic_form .field_row { border:1px solid #999; margin-bottom:10px; padding:10px; }
      #dynamic_form label { padding:0 6px; }
    </style>
<script type="text/javascript">
    jQuery(document).ready(function(){
        var meta_image_frame;
        var meta_image;
        jQuery(document).on('click', '.addimage', function(e) { 
            e.preventDefault();
            var currDiv = jQuery(this);
            meta_image = currDiv.parents('.field_row').find('.meta_image_url');

            if (meta_image_frame) {
                meta_image_frame.open();
                return;
            }

            meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
              title: meta_image.title,
              button: {
                text: meta_image.button
              },
              library: {
                type: [ 'image' ]
              },
            });
            meta_image_frame.on('select', function () {
              var media_attachment = meta_image_frame.state().get('selection').first().toJSON();
              meta_image.val(media_attachment.url);

              currDiv.parents('.field_row').find("div.image_wrap").html('<img src="'+media_attachment.url+'" height="48" width="48" />');
            });
            meta_image_frame.open();
        });
    });


    function remove_field(obj) {
        var parent=jQuery(obj).parent().parent();
        parent.remove();
    }

    function add_field_row() {
        var row = jQuery('#master-row').html();
        jQuery(row).appendTo('#field_wrap');
    }
</script>
  <?php
}

function save_woningen_gallery($post_id)
{
    $post_type = isset($_POST['post_type']) ? $_POST['post_type'] : '';
    if ('woningen' == $post_type) {
        if ( isset($_POST['gallery']) ) {
            $gallery_data = array();
            for ($i = 0; $i < count( $_POST['gallery']['image_url'] ); $i++ ){
                if ( '' != $_POST['gallery']['image_url'][ $i ] ) {
                   $gallery_data['image_url'][]  = $_POST['gallery']['image_url'][ $i ];
                }
            }

            if ( $gallery_data ) 
                update_post_meta( $post_id, 'gallery_data', $gallery_data );
            else 
                delete_post_meta( $post_id, 'gallery_data' );
        } else {
            delete_post_meta( $post_id, 'gallery_data' );
        }
    }
}
add_action('save_post', 'save_woningen_gallery');
add_action('new_to_publish', 'save_woningen_gallery');

function woningen_video_metabox()
{
    add_meta_box(
        'woningen_video_metabox_id',       // $id
        'Upload Video',                  // $title
        'woningen_video_metabox_callback',  // $callback
        'woningen',                 // $page
        'normal',                  // $context
        'high'                     // $priority
    );
}
add_action('add_meta_boxes', 'woningen_video_metabox');

function woningen_video_metabox_callback(){
    global $post;
    $property_video = get_post_meta( $post->ID, 'property_video', true );
?>
<div id="dynamic_form">
    <div id="field_wrap">
        <div class="field_row">
            <div class="field_left">
                <div class="form_field">
                    <label>Youtube Video URL</label>
                    <input type="text" class="meta_video_url" name="property_video" value="<?php echo $property_video; ?>" />
                </div>
            </div>

            <!-- 
            <div class="field_right">
                <input class="button addvideo" type="button" value="Choose File" />
                <input class="button" type="button" value="Remove" onclick="remove_video(this)" /> 
            </div>
            -->

            <div class="clear" /></div> 
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        var meta_video_frame;
        var meta_video;
        jQuery(document).on('click', '.addvideo', function(e) { 
            e.preventDefault();
            var currDiv = jQuery(this);
            meta_video = currDiv.parents('.field_row').find('.meta_video_url');

            if (meta_video_frame) {
                meta_video_frame.open();
                return;
            }

            meta_video_frame = wp.media.frames.meta_video_frame = wp.media({
              title: meta_video.title,
              button: {
                text: meta_video.button
              },
              library: {
                type: [ 'video' ]
              },
            });
            meta_video_frame.on('select', function () {
                var media_attachment = meta_video_frame.state().get('selection').first().toJSON();
                meta_video.val(media_attachment.url);
            });
            meta_video_frame.open();
        });
    });


    function remove_video(obj) {
        var parent=jQuery(obj).parent().parent();
        parent.remove();
    }
</script>
  <?php
}

function save_woningen_video($post_id)
{
    $post_type = isset($_POST['post_type']) ? $_POST['post_type'] : '';
    if ('woningen' == $post_type) {
        if ( isset($_POST['property_video']) && trim($_POST['property_video']) != '' ) {
            update_post_meta( $post_id, 'property_video', trim($_POST['property_video']) );
        } else {
            delete_post_meta( $post_id, 'property_video' );
        }
    }
}
add_action('save_post', 'save_woningen_video');
add_action('new_to_publish', 'save_woningen_video');
?>