<?php 
/*
 Plugin Name: Advance Startup Pro Posttype
 lugin URI: https://www.Themeshopy.com/
 Description: Creating new post type for Advance Startup Pro Posttype Theme.
 Author: Themeshopy
 Version: 1.0
 Author URI: https://www.Themeshopy.com/
*/

define( 'ADVANCE_STARTUP_PRO_POSTTYPE_VERSION', '1.0' );

add_action( 'init', 'advance_startup_pro_posttype_create_post_type' );

function advance_startup_pro_posttype_create_post_type() {
  register_post_type( 'services',
    array(
        'labels' => array(
            'name' => __( 'Services','advance-startup-pro-posttype' ),
            'singular_name' => __( 'Services','advance-startup-pro-posttype' )
        ),
        'capability_type' =>  'post',
        'menu_icon'  => 'dashicons-tag',
        'public' => true,
        'supports' => array(
        'title',
        'editor',
        'thumbnail'
        )
    )
  );

  register_post_type( 'projects',
    array(
        'labels' => array(
            'name' => __( 'Projects','advance-startup-pro-posttype' ),
            'singular_name' => __( 'Projects','advance-startup-pro-posttype' )
        ),
        'capability_type' =>  'post',
        'menu_icon'  => 'dashicons-tag',
        'public' => true,
        'supports' => array(
        'title',
        'editor',
        'thumbnail',
        'page-attributes',
        'comments'
        )
    )
  );

  register_post_type( 'testimonials',
    array(
  		'labels' => array(
  			'name' => __( 'Testimonials','advance-startup-pro-posttype' ),
  			'singular_name' => __( 'Testimonials','advance-startup-pro-posttype' )
  		),
  		'capability_type' => 'post',
  		'menu_icon'  => 'dashicons-businessman',
  		'public' => true,
  		'supports' => array(
  			'title',
  			'editor',
  			'thumbnail'
  		)
		)
	);
  register_post_type( 'team',
    array(
      'labels' => array(
        'name' => __( 'Our Team','advance-startup-pro-posttype' ),
        'singular_name' => __( 'Our Team','advance-startup-pro-posttype' )
      ),
        'capability_type' => 'post',
        'menu_icon'  => 'dashicons-businessman',
        'public' => true,
        'supports' => array( 
          'title',
          'editor',
          'thumbnail'
      )
    )
  );
}

/*--------------- Services ------------------*/
function advance_startup_pro_posttype_images_metabox_enqueue($hook) {
  if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
    wp_enqueue_script('advance-startup-pro-posttype-images-metabox', plugin_dir_url( __FILE__ ) . '/js/img-metabox.js', array('jquery', 'jquery-ui-sortable'));

    global $post;
    if ( $post ) {
      wp_enqueue_media( array(
          'post' => $post->ID,
        )
      );
    }

  }
}
add_action('admin_enqueue_scripts', 'advance_startup_pro_posttype_images_metabox_enqueue');

function advance_startup_pro_posttype_bn_meta_callback_services( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );
    
}

function advance_startup_pro_posttype_bn_meta_save_services( $post_id ) {

  if (!isset($_POST['bn_nonce']) || !wp_verify_nonce($_POST['bn_nonce'], basename(__FILE__))) {
    return;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }
 
  
}
add_action( 'save_post', 'advance_startup_pro_posttype_bn_meta_save_services' );

/* Services shortcode */
function advance_startup_pro_posttype_services_func( $atts ) {
  $services = '';
  $services = '<div id="services">
              <div class="row">';
  $query = new WP_Query( array( 'post_type' => 'services') );

    if ( $query->have_posts() ) :

  $k=1;
  $new = new WP_Query('post_type=services');

  while ($new->have_posts()) : $new->the_post();
        $custom_url ='';
        $post_id = get_the_ID();
        $excerpt = wp_trim_words(get_the_excerpt(),25);
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
        $viewmorebtn = get_theme_mod('advance_startup_pro_services_buttontext',__('View Details','advance-startup-pro-posttype'));
        if(has_post_thumbnail()) { $thumb_url = $thumb['0']; }
        if(get_post_meta($post_id,'meta-services-url',true !='')){$custom_url =get_post_meta($post_id,'meta-services-url',true); } else{ $custom_url = get_permalink(); }
        $services .= '<div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <div class="services-content">
                          <div class="services-data">
                            <div class="services-img">
                              <img src="'.esc_url($thumb_url).'" />
                            </div>
                            <a href="'.esc_url($custom_url).'">
                              <h4 class="services-title">'.esc_html(get_the_title()) .'</h4>
                            </a>
                            <div class="services-text">'.$excerpt.'</div>
                            <div class="text-center">
                              <a class="service-button" href="'.get_the_permalink().'"><span>'.$viewmorebtn.'</span></a>
                            </div> 
                          </div>
                        </div>
                      </div>';
    if($k%2 == 0){
      $services.= '<div class="clearfix"></div>';
    }
      $k++;
  endwhile;
  else :
    $services = '<h2 class="center">'.esc_html__('Post Not Found','advance-startup-pro-posttype').'</h2>';
  endif;
  $services .= '</div></div>';
  return $services;
}

add_shortcode( 'list-services', 'advance_startup_pro_posttype_services_func' );


// ----------------- latestworks Meta ---------------------
function advance_startup_pro_posttype_bn_custom_meta_projects() {

    add_meta_box( 'bn_meta', __( 'Project Meta', 'advance-startup-pro-posttype' ), 'advance_startup_pro_posttype_bn_meta_callback_projects', 'projects', 'normal', 'high' );
}
/* Hook things in for admin*/
if (is_admin()){
  add_action('admin_menu', 'advance_startup_pro_posttype_bn_custom_meta_projects');
}

function advance_startup_pro_posttype_bn_meta_callback_projects( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );
    $project_name = get_post_meta( $post->ID, 'meta-project-name', true );
    ?>
  <div id="property_stuff">
    <table id="list-table">     
      <tbody id="the-list" data-wp-lists="list:meta">
        <tr id="meta-2">
          <td class="left">
            <?php esc_html_e( 'Client Name', 'advance-startup-pro-posttype' )?>
          </td>
          <td class="left" >
            <input type="text" name="meta-project-name" id="meta-project-name" value="<?php echo esc_attr( $project_name ); ?>" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php
}

function advance_startup_pro_posttype_bn_meta_save_projects( $post_id ) {

  if (!isset($_POST['bn_nonce']) || !wp_verify_nonce($_POST['bn_nonce'], basename(__FILE__))) {
    return;
  }

  if (!current_user_can('edit_post', $post_id)) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }
  
  // Save Course Duration
  if( isset( $_POST[ 'meta-project-name' ] ) ) {
    update_post_meta( $post_id, 'meta-project-name', sanitize_text_field($_POST[ 'meta-project-name' ]) );
  }
}
add_action( 'save_post', 'advance_startup_pro_posttype_bn_meta_save_projects' );


/* --------------------- projects shortcode  ------------------- */

function advance_startup_pro_posttype_projects_func( $atts ) {
  $projects = '';
  $projects = '<div id="projects">
                <div class="row">';
  $query = new WP_Query( array( 'post_type' => 'projects') );

    if ( $query->have_posts() ) :

  $k=1;
  $new = new WP_Query('post_type=projects');

  while ($new->have_posts()) : $new->the_post();
        $custom_url ='';
        $post_id = get_the_ID();
        $excerpt = wp_trim_words(get_the_excerpt(),25);
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
        if(has_post_thumbnail()) { $thumb_url = $thumb['0']; }
        
        if(get_post_meta($post_id,'meta-projects-url',true !='')){$custom_url =get_post_meta($post_id,'meta-projects-url',true); } else{ $custom_url = get_permalink(); }
        $projects .= '<div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <div class="project-image">
                          <img src="'.esc_url($thumb_url).'">
                          <div class="projectbox-content">
                            <div class="project_content">
                              <h5 class="project_title">
                                <a href="'.get_the_permalink().'">
                                  '.get_the_title().'
                                </a>
                              </h5>
                              <div class="project-text">'.$excerpt.'</div>
                            </div>
                          </div>
                        </div>
                      </div>';
    if($k%2 == 0){
      $projects.= '<div class="clearfix"></div>';
    }
      $k++;
  endwhile;
  else :
    $projects = '<h2 class="center">'.esc_html__('Post Not Found','advance-startup-pro-posttype-pro').'</h2>';
  endif;
  $projects .= '</div>
                </div>';
  return $projects;
}

add_shortcode( 'list-projects', 'advance_startup_pro_posttype_projects_func' );



/*------------------ Testimonial section -------------------*/

/* Adds a meta box to the Testimonial editing screen */
function advance_startup_pro_posttype_bn_testimonial_meta_box() {
	add_meta_box( 'advance-startup-pro-posttype-testimonial-meta', __( 'Enter Details', 'advance-startup-pro-posttype' ), 'advance_startup_pro_posttype_bn_testimonial_meta_callback', 'testimonials', 'normal', 'high' );
}
// Hook things in for admin
if (is_admin()){
    add_action('admin_menu', 'advance_startup_pro_posttype_bn_testimonial_meta_box');
}

/* Adds a meta box for custom post */
function advance_startup_pro_posttype_bn_testimonial_meta_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'advance_startup_pro_posttype_posttype_testimonial_meta_nonce' );
  $bn_stored_meta = get_post_meta( $post->ID );
	$desigstory = get_post_meta( $post->ID, 'advance_startup_pro_posttype_testimonial_desigstory', true );
	?>
	<div id="testimonials_custom_stuff">
		<table id="list">
			<tbody id="the-list" data-wp-lists="list:meta">
				<tr id="meta-1">
					<td class="left">
						<?php _e( 'Designation', 'advance-startup-pro-posttype' )?>
					</td>
					<td class="left" >
						<input type="text" name="advance_startup_pro_posttype_testimonial_desigstory" id="advance_startup_pro_posttype_testimonial_desigstory" value="<?php echo esc_attr( $desigstory ); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
}

/* Saves the custom meta input */
function advance_startup_pro_posttype_bn_metadesig_save( $post_id ) {
	if (!isset($_POST['advance_startup_pro_posttype_posttype_testimonial_meta_nonce']) || !wp_verify_nonce($_POST['advance_startup_pro_posttype_posttype_testimonial_meta_nonce'], basename(__FILE__))) {
		return;
	}

	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	// Save desig.
	if( isset( $_POST[ 'advance_startup_pro_posttype_testimonial_desigstory' ] ) ) {
		update_post_meta( $post_id, 'advance_startup_pro_posttype_testimonial_desigstory', sanitize_text_field($_POST[ 'advance_startup_pro_posttype_testimonial_desigstory']) );
	}

}

add_action( 'save_post', 'advance_startup_pro_posttype_bn_metadesig_save' );

/*---------------Testimonials shortcode -------------------*/
function advance_startup_pro_posttype_testimonial_func( $atts ) {

    $testimonial = ''; 
    $testimonial = '<div id="testimonials">
                    <div class="row">';
    $custom_url = '';
      $new = new WP_Query( array( 'post_type' => 'testimonials' ) );
      if ( $new->have_posts() ) :
        $k=1;
        while ($new->have_posts()) : $new->the_post();
          $post_id = get_the_ID();
          $excerpt = wp_trim_words(get_the_excerpt(),25);
          if(has_post_thumbnail()) {
            $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
            $thumb_url = $thumb['0'];
          }
          $desigstory= get_post_meta($post_id,'advance_startup_pro_posttype_testimonial_desigstory',true);
            $testimonial .= '<div class="col-lg-4 col-md-4 col-sm-12 col-12">
                              <div class="testimonial-content"> 
                                <div class="testimonial-shbox w-100 mb-3" >  
                                  <div class="testimonial-image text-center">
                                    <img src="'.esc_url($thumb_url).'">
                                  </div> 
                                  <div class="testimonial-data">
                                    
                                    <h4 class="testimonial-title"><a href="'.get_permalink().'">'.get_the_title().'</a></h4>
                                    <div class="testimonial-designation">'.$desigstory.'
                                    </div>
                                  
                                    <div class="testimonial-text">
                                      <div class="short_text pb-3">
                                        <i class="fas fa-quote-left"></i> '.$excerpt.' <i class="fas fa-quote-right"></i>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>';  
            $testimonial .= '</div>';

            if($k%3 == 0){
                $testimonial.= '<div class="clearfix"></div>'; 
            } 
          $k++;         
        endwhile; 
        wp_reset_postdata();
      else :
        $project = '<h2 class="center">'.__('Not Found','advance-startup-pro-posttype').'</h2>';
      endif;
    $testimonial.= '</div></div>';
  return $testimonial;
  //
}
add_shortcode( 'list-testimonials', 'advance_startup_pro_posttype_testimonial_func' );

/*--------------Team -----------------*/
/* Adds a meta box for Designation */
function advance_startup_pro_posttype_bn_team_meta() {
    add_meta_box( 'advance_startup_pro_posttype_bn_meta', __( 'Enter Details','advance-startup-pro-posttype' ), 'advance_startup_pro_posttype_ex_bn_meta_callback', 'team', 'normal', 'high' );
}
// Hook things in for admin
if (is_admin()){
    add_action('admin_menu', 'advance_startup_pro_posttype_bn_team_meta');
}
/* Adds a meta box for custom post */
function advance_startup_pro_posttype_ex_bn_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'advance_startup_pro_posttype_bn_nonce' );
    $bn_stored_meta = get_post_meta( $post->ID );

    //Email details
    if(!empty($bn_stored_meta['meta-desig'][0]))
      $bn_meta_desig = $bn_stored_meta['meta-desig'][0];
    else
      $bn_meta_desig = '';

    //Phone details
    if(!empty($bn_stored_meta['meta-call'][0]))
      $bn_meta_call = $bn_stored_meta['meta-call'][0];
    else
      $bn_meta_call = '';


    //facebook details
    if(!empty($bn_stored_meta['meta-facebookurl'][0]))
      $bn_meta_facebookurl = $bn_stored_meta['meta-facebookurl'][0];
    else
      $bn_meta_facebookurl = '';


    //linkdenurl details
    if(!empty($bn_stored_meta['meta-linkdenurl'][0]))
      $bn_meta_linkdenurl = $bn_stored_meta['meta-linkdenurl'][0];
    else
      $bn_meta_linkdenurl = '';

    //twitterurl details
    if(!empty($bn_stored_meta['meta-twitterurl'][0]))
      $bn_meta_twitterurl = $bn_stored_meta['meta-twitterurl'][0];
    else
      $bn_meta_twitterurl = '';

    //twitterurl details
    if(!empty($bn_stored_meta['meta-googleplusurl'][0]))
      $bn_meta_googleplusurl = $bn_stored_meta['meta-googleplusurl'][0];
    else
      $bn_meta_googleplusurl = '';

    //Designation details
    if(!empty($bn_stored_meta['meta-designation'][0]))
      $bn_meta_designation = $bn_stored_meta['meta-designation'][0];
    else
      $bn_meta_designation = '';

    //Icon Img
    if(!empty($bn_stored_meta['meta-image'][0]))
      $bn_meta_image = $bn_stored_meta['meta-image'][0];
    else
      $bn_meta_image = '';

    ?>
    <div id="agent_custom_stuff">
        <table id="list-table">         
            <tbody id="the-list" data-wp-lists="list:meta">
                <tr id="meta-1">
                  <td class="left">
                    <?php _e( 'Email', 'advance-startup-pro-posttype' )?>
                  </td>
                  <td class="left" >
                    <input type="text" name="meta-desig" id="meta-desig" value="<?php echo esc_attr($bn_meta_desig); ?>" />
                  </td>
                </tr>
                <tr id="meta-2">
                    <td class="left">
                        <?php _e( 'Phone Number', 'advance-startup-pro-posttype' )?>
                    </td>
                    <td class="left" >
                        <input type="text" name="meta-call" id="meta-call" value="<?php echo esc_attr($bn_meta_call); ?>" />
                    </td>
                </tr>
                <tr id="meta-3">
                  <td class="left">
                    <?php _e( 'Facebook Url', 'advance-startup-pro-posttype' )?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-facebookurl" id="meta-facebookurl" value="<?php echo esc_url($bn_meta_facebookurl); ?>" />
                  </td>
                </tr>
                <tr id="meta-4">
                  <td class="left">
                    <?php _e( 'Linkedin URL', 'advance-startup-pro-posttype' )?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-linkdenurl" id="meta-linkdenurl" value="<?php echo esc_url($bn_meta_linkdenurl); ?>" />
                  </td>
                </tr>
                <tr id="meta-5">
                  <td class="left">
                    <?php _e( 'Twitter Url', 'advance-startup-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-twitterurl" id="meta-twitterurl" value="<?php echo esc_url( $bn_meta_twitterurl); ?>" />
                  </td>
                </tr>
                <tr id="meta-6">
                  <td class="left">
                    <?php _e( 'GooglePlus URL', 'advance-startup-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="url" name="meta-googleplusurl" id="meta-googleplusurl" value="<?php echo esc_url($bn_meta_googleplusurl); ?>" />
                  </td>
                </tr>
                <tr id="meta-7">
                  <td class="left">
                    <?php _e( 'Designation', 'advance-startup-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="text" name="meta-designation" id="meta-designation" value="<?php echo esc_attr($bn_meta_designation); ?>" />
                  </td>
                </tr>
                <tr id="meta-8">
                  <td class="left">
                    <?php _e( 'Icon Image', 'advance-startup-pro-posttype' ); ?>
                  </td>
                  <td class="left" >
                    <input type="text" name="meta-image" id="meta-image" class="meta-image regular-text" value="<?php echo esc_attr($bn_meta_image); ?>">
                    <input type="button" class="button image-upload" value="Browse">
                    <div class="image-preview"><img src="<?php echo $bn_stored_meta['meta-image'][0]; ?>" style="max-width: 250px;"></div>
                  </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
}
/* Saves the custom Designation meta input */
function advance_startup_pro_posttype_ex_bn_metadesig_save( $post_id ) {
    if( isset( $_POST[ 'meta-desig' ] ) ) {
        update_post_meta( $post_id, 'meta-desig', esc_html($_POST[ 'meta-desig' ]) );
    }
    if( isset( $_POST[ 'meta-call' ] ) ) {
        update_post_meta( $post_id, 'meta-call', esc_html($_POST[ 'meta-call' ]) );
    }
    // Save facebookurl
    if( isset( $_POST[ 'meta-facebookurl' ] ) ) {
        update_post_meta( $post_id, 'meta-facebookurl', esc_url($_POST[ 'meta-facebookurl' ]) );
    }
    // Save linkdenurl
    if( isset( $_POST[ 'meta-linkdenurl' ] ) ) {
        update_post_meta( $post_id, 'meta-linkdenurl', esc_url($_POST[ 'meta-linkdenurl' ]) );
    }
    if( isset( $_POST[ 'meta-twitterurl' ] ) ) {
        update_post_meta( $post_id, 'meta-twitterurl', esc_url($_POST[ 'meta-twitterurl' ]) );
    }
    // Save googleplusurl
    if( isset( $_POST[ 'meta-googleplusurl' ] ) ) {
        update_post_meta( $post_id, 'meta-googleplusurl', esc_url($_POST[ 'meta-googleplusurl' ]) );
    }
    // Save designation
    if( isset( $_POST[ 'meta-designation' ] ) ) {
        update_post_meta( $post_id, 'meta-designation', esc_html($_POST[ 'meta-designation' ]) );
    }
    // Icon Img
    if( isset( $_POST[ 'meta-image' ] ) ) {
        update_post_meta( $post_id, 'meta-image', esc_html($_POST[ 'meta-image' ]) );
    }
}
add_action( 'save_post', 'advance_startup_pro_posttype_ex_bn_metadesig_save' );

add_action( 'save_post', 'bn_meta_save' );
/* Saves the custom meta input */
function bn_meta_save( $post_id ) {
  if( isset( $_POST[ 'advance_startup_pro_posttype_team_featured' ] )) {
      update_post_meta( $post_id, 'advance_startup_pro_posttype_team_featured', esc_attr(1));
  }else{
    update_post_meta( $post_id, 'advance_startup_pro_posttype_team_featured', esc_attr(0));
  }
}
/*------------- Team Shorthcode -------------*/
function advance_startup_pro_posttype_team_func( $atts ) {
    $team = ''; 
    $team = '<div id="team">
              <div class="row">';
      $new = new WP_Query( array( 'post_type' => 'team') );
      if ( $new->have_posts() ) :
        $k=1;
        while ($new->have_posts()) : $new->the_post();
          $post_id = get_the_ID();
          $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'large' );
          $thumb_url = $thumb['0'];
          $excerpt = advance_startup_pro_string_limit_words(get_the_excerpt(),20);
          $designation = get_post_meta($post_id,'meta-designation',true);
          $facebookurl = get_post_meta($post_id,'meta-facebookurl',true);
          $linkedin = get_post_meta($post_id,'meta-linkdenurl',true);
          $twitter = get_post_meta($post_id,'meta-twitterurl',true);
          $googleplus = get_post_meta($post_id,'meta-googleplusurl',true);
          $iconimg=get_post_meta($post_id,'meta-image',true);
          $team .= '<div class="team_outer col-lg-3 col-sm-12 col-12 mb-4">
                      <div class="team-content">
                        <div class="team-image">
                          <img src="'.esc_url($thumb_url).'"> 
                        </div>';
                        if($iconimg != ''){
                        $team .=  '<div class="meta-image">
                            <img src="'.esc_url($iconimg).'">
                          </div>';
                        }
                        $team .=  '<div class="team-data">
                          <a href="'.get_the_permalink().'">
                            <h4 class="team-title">'.get_the_title().'</h4>
                          </a>
                        </div>
                        <div class="team-designation mb-3">'.$designation.'</div>
                        <div class="team-socialshrt">';
                        if($facebookurl != ''){
                          $team .= '<a class="" href="'.esc_url($facebookurl).'" target="_blank"><i class="fab fa-facebook-f"></i></a>';
                        } if($twitter != ''){
                          $team .= '<a class="" href="'.esc_url($twitter).'" target="_blank"><i class="fab fa-twitter"></i></a>';
                        } if($googleplus != ''){
                          $team .= '<a class="" href="'.esc_url($googleplus).'" target="_blank"><i class="fab fa-google-plus-g"></i></a>';
                        } if($linkedin != ''){
                          $team .= '<a class="" href="'.esc_url($linkedin).'" target="_blank"><i class="fab fa-linkedin-in"></i></a>';
                        }
                      $team .= '</div>
                      </div>';
            $team .='</div>';     
          if($k%4 == 0){
              $team.= '<div class="clearfix"></div>'; 
          } 
          $k++;         
        endwhile; 
        wp_reset_postdata();
        $team.= '</div></div>';
      else :
        $team = '<div id="team" class="team_wrap col-md-3 mt-3 mb-4"><h2 class="center">'.__('Not Found','advance-startup-pro-posttype').'</h2></div>';
      endif;
    return $team;
}
add_shortcode( 'list-team', 'advance_startup_pro_posttype_team_func' );