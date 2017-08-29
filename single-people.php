<?php

class Tremaine_Profile_Template extends Tremaine_Template {
	
	public function __construct(){
		
		if ( isset( $_GET['test'] ) ) {
			
			global $post;
			
			var_dump( $post );
		
		} // End if
		
		//add_theme_support( 'genesis-structural-wraps', array( 'header', 'footer-widgets', 'footer' ) );
		
		parent::__construct();
		
	}
	
	
	public function edit_template(){
		
		//error_reporting(E_ALL);
		
		//ini_set('display_errors', 1);
		
		//add_theme_support( 'genesis-structural-wraps', array( 'header', 'nav', 'subnav','footer-widgets', 'footer' ) );

		remove_action( 'genesis_loop', 'genesis_do_loop' );
		
		//add_action( 'genesis_loop', array( $this, 'do_custom_loop' ), 999 );
		
		if ( empty( $_GET['property_status'] ) ){
			
			add_action( 'genesis_after_header' , array( $this , 'the_profile' ), 999 );
		
		} // end if
		
		add_action( 'genesis_after_header' , array( $this , 'the_profile_footer' ), 1000 );
		
	} // end edit_template
	
	/*function do_custom_loop() {
 
		global $post;
		
		while( have_posts() ){
			
			$post->post_content = '[wovaxembed listview="yes" mapview="no" accountview="no" pagination="no" posts_per_page="3" meta="Listing_Agent=Jennifer Adams&Status!=Closed"]';
			
			the_post();
			
			//echo apply_filters('the_content', '');
			
			the_content();
			
		} // end while
		
		remove_action( 'genesis_loop', array( $this, 'do_custom_loop' ), 999 );
	 
	} // end do_custom_loop*/
	
	
	public function the_profile(){
		
		global $post;
		
		//var_dump( $post );
		
		if ( isset( $_GET['debug'] ) ){
			
			the_content();
			
		} else {
			
		
		echo apply_filters( 'the_content' , $post->post_content );
		
		} // end if
		
		/*$user = $this->return_user();
		
		if ( ! empty( get_user_meta( $user->ID , '_wovax_user_profile_video', true ) ) || isset( $_GET['video'] ) ){
			
			$embed = wp_oembed_get( 'https://www.youtube.com/watch?v=Da8jaZUW-Gg' );
			
			$this->add_auto_play( $embed );
			
			include locate_template( 'inc/inc-profile-video.php' ) ;
			
			echo '<div class="has-video">';
			
			include locate_template( 'inc/inc-profile-banner.php' ) ;
			
			echo '</div>';
			
		} else {
			
			include locate_template( 'inc/inc-profile-banner.php' ) ;
			
		} // end if
		
		include locate_template( 'inc/inc-profile-about.php' ) ;
		
		include locate_template( 'inc/inc-profile-tabs.php' ) ;*/
		
	}
	
	public function the_profile_footer(){
		
				$scode_active = get_post_meta( get_the_ID(),  '_shortcode_active_override', true );
					
				if ( empty( $scode_active ) ){
					
					$scode_active = get_theme_mod('crest_profile_shortcode_1', '');
					
				} // end if
				
				$scode_closed = get_post_meta( get_the_ID(),  '_shortcode_closed_override', true );
				
				if ( empty( $scode_closed ) ){
					
					$scode_closed = get_theme_mod('crest_profile_shortcode_2', '');
					
				} // end if
				
				$shortcode_1 = $this->replace_values( $scode_active );
				$shortcode_2 = $this->replace_values( $scode_closed );
				
				$agent_id = get_post_meta( get_the_ID(), '_crest_id', true );
				
				$email = get_post_meta( get_the_ID(), '_primary_email', true );
				
				$name = get_post_meta( get_the_ID(), '_display_name', true ); 
				
				$active_shortcode = '[tremaine_listing show_controls="1" property_status="Active,Auction,Back on Market,Contingent,Pending,Price Change,Re-activated,Temporarily No Showings" agent_id="' . $agent_id . '"  posts_per_page="9" sort_by="Price-numeric-desc"]';
			
				$closed_shortcode = '[tremaine_listing show_controls="1" property_status="Sold or Off Market" agent_id="' . $agent_id . '"  posts_per_page="9" sort_by="Price-numeric-desc"]';
				
				$agent_id = get_post_meta( get_the_ID(), '_crest_id', true );
				
				/*include WOVAXTREMAINEPATH . 'parts/people/profile-footer.php';*/
		
	}
	
	protected function replace_values( $shortcode ){
		
		global $post;
		
		$replace = array(
			'%crest_id%' => get_post_meta( $post->ID, '_crest_id', true ),
			'%primary_email%' => get_post_meta( $post->ID, '_primary_email', true ),
			'%rfg_office_staff_id%' => get_post_meta( $post->ID, '_rfg_office_staff_id', true ),
			'%first_name%' => get_post_meta( $post->ID, '_first_name', true ),
			'%last_name%' => get_post_meta( $post->ID, '_last_name', true ),
		);
		
		foreach( $replace as $key => $value ){
			
			$shortcode = str_replace( $key, $value, $shortcode );
			
		} // end foreach
		
		return $shortcode;
		
	}
	
	public function return_user(){
		
		global $wp_query;
		
		$roles = array( 'agent');
		
		$user_id = false;
		
		foreach( $roles as $role ){
			
			if ( ! empty( $wp_query->query_vars[ $role ] ) ) {
				
				$user_id = $wp_query->query_vars[ $role ];
				break;
				
			} // end if
			
		} // end foreach
		
		if (  $user_id ){
			
			$user = get_user_by( 'slug' , $user_id );
			
			return $user;
			
		} // end if
		
		return false;
		
	} // end return_user
	
	
	public function add_auto_play( &$video_embed ){
		
		if ( strpos( $video_embed , 'youtube' ) > 0 ) {
			
			$video_embed = preg_replace_callback( 
				'/src="(.*?)"/',
				function ($matches) {
					return 'src="' . $matches[1] . '&rel=0&autoplay=1"';
				}, 
				$video_embed
			);
			
		} // end if
		
	} // end add_auto_play
	
	
} // end Tremaine_Property_Template

class Tremaine_People_Template {
	
	public function _construct(){
		
		$this->do_actions();
		
	} // End _construct
	
	protected function do_actions(){
		
		add_filter( 'genesis_structural_wrap-site-inner', '__return_empty_string' );
		
	} // End add_actions 
	
} // End Tremaine_People_Template

if ( isset( $_GET['test'] ) ){
	
	add_filter( 'genesis_structural_wrap-site-inner', '__return_empty_string' );
	
	$single_person = new Tremaine_People_Template();
	
} else {

	$profile = new Tremaine_Profile_Template();

} // End If

genesis();

add_theme_support( 'genesis-structural-wraps', array( 'header', 'footer-widgets', 'footer' ) );