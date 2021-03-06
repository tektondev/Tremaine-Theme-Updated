<?php

class Shortcode_Tremaine_Listing extends Tremaine_Shortcode {
	
	protected $tag = 'tremaine_listing';
	
	protected $default_atts = array(
			'paged' 				=> 1,
			'posts_per_page' 		=> 12,
			'display_as' 			=> 'gallery',
			'keyword' 				=> '',
			'sort_by' 				=> 'date-desc',
			'custom_field' 			=> '',
			'custom_field_value' 	=> '',
			'compare' 				=> '',
			'property_status' 		=> 'Active,Auction,Back on Market,New,Price Change,Re-activated',
			'show_controls'  		=> 1,
			'show_header' 			=> 1,
			'sort_types'			=> 0,
			'agent_id'				=> '',
		);
	
	
	public function render_shortcode( $atts , $content, $tag, $atts_orig ){
		
		/*if ( isset( $_GET['show-properties'] ) ){
			
			/*$args = array(
				'post_type' => 'wovaxproperty',
				'status'	=> 'publish',
				'posts_per_page' => -1,
				'orderby' => 'post_date',
				'order' => 'ASC',
				'meta_query' => array(
					//'relation' => 'AND',
					array(
						//'key'     => 'Primary_Agent_Id',
						//'value'   => '7d081ffa-0e10-4bd0-9aa9-e1a253c80d31',
						//'compare' => 'LIKE',
						'relation' => 'OR',
						array(
							'key'     => 'Listing_Agent',
							'value'   => 'Elizabeth Amidon',
							'compare' => 'LIKE',
						),
						array(
							'key'     => 'Colisting_Agent',
							'value'   => 'Elizabeth Amidon',
							'compare' => 'LIKE',
						)
					),
					//array(
						
						//'key'     => 'Listing_Agent',
						//'value'   => 'Elizabeth Amidon',
						//'compare' => 'LIKE',
					//)
				)
			);
			
			var_dump( $atts );
			
			$query = new WP_Query( $args );
			
			while ( $query->have_posts() ) {
			
				$query->the_post();
				
				var_dump( $query->post->ID . ' ' . get_post_permalink() . ' ' . get_post_meta( $query->post->ID, 'Status', true ) . '<br>' );
				
			} // end while
			
			wp_reset_postdata();
			
		} // End if*/
		
		
		require_once WOVAXTREMAINEPATH . 'classes/class-tremaine-property-factory.php';
		$property_factory = new Tremaine_Property_Factory();
		
		$presets = $this->get_presets( $atts );
		
		$query = $this->get_query( $presets );
		
		$page = $presets['paged'];
		$total_results = $query->found_posts;
		$total_pages = $query->max_num_pages;
		$next_page = ( ( $page + 1 ) < $total_results ) ? ( $page + 1 ) : 'na';
		$prev_page = ( ( $page - 1 ) > 0 ) ? ( $page - 1 ) : 'na';
		$start_set = ( $page == 1 ) ? 1 : ( $page - 1 );
		$end_set = ( ( $start_set + 4 ) > $total_pages ) ? $total_pages : $start_set + 4;
		$showing_start = ( $page == 1 ) ? 1 : ( $page - 1 ) * $presets['posts_per_page'];
		$showing_end = ( $total_results < $presets['posts_per_page'] ) ? $total_results : $showing_start + ( $presets['posts_per_page'] - 1 ) ;
		
		$properties = $property_factory->get_properties_from_query( $query );
		
		$sort_options = get_option('wovax_sort_fields');
		
		$sort_types = ( ! empty( $atts['sort_types'] ) )? 1 : 0;
		
		$property_type = ( ! empty( $_GET['property_type'] ) ) ? sanitize_text_field( $_GET['property_type'] ) : '';
		
		//if ( $atts['status'] ) {
		
			//$properties = $this->filter_by_status( $properties, $atts['status'] );
		
		//} // end if
		
		global $tremaine_modals;
		
		ob_start();
		
		include 'includes/include-tremaine-listings-gallery.php';
		
		//include locate_template( 'inc/inc-listing-gallery.php' );
		
		return ob_get_clean();
		
	} // end render_shortcode
	
	
	protected function get_presets( $atts ){
		
		$defaults = array(
			'paged' 				=> 1,
			'posts_per_page' 		=> 12,
			'display_as' 			=> 'gallery',
			'keyword' 				=> '',
			'sort_by' 				=> 'date-desc',
			'custom_field' 			=> '',
			'custom_field_value' 	=> '',
			'compare' 				=> '',
			'property_status' 		=> 'Active,Auction,Back on Market,New,Price Change,Re-activated',
			'show_controls' 		=> 1,
			'show_header' 			=> 1,
			'agent_id' 				=> '',
		);
		
		if ( isset( $_GET['cpage'] ) ) $atts['paged'] = sanitize_text_field( $_GET['cpage'] );
		
		if ( isset( $_GET['skeyword'] ) ) $atts['keyword'] = sanitize_text_field( $_GET['skeyword'] );
		
		if ( isset( $_GET['sort_by'] ) ) $atts['sort_by'] = sanitize_text_field( $_GET['sort_by'] );
		
		$presets = shortcode_atts( $defaults , $atts );
		
		return $presets;
		
	} // end get_presets
	
	
	protected function get_query( $presets ) {
		
		$args = array(
			'post_type' => 'wovaxproperty',
			'status'	=> 'publish',
			'posts_per_page' => 12,
			'orderby' => 'post_date',
			'order' => 'ASC',
		);
		
		if ( ! empty( $presets['posts_per_page'] ) ) $args['posts_per_page'] = $presets['posts_per_page'];
		
		if ( ! empty( $presets['paged'] ) ) $args['paged'] = $presets['paged']; 
		
		if ( ! empty( $presets['sort_by'] ) ) $this->add_sort_args( $args, $presets );
		
		$status = explode( ',', $presets['property_status'] );
		
		if ( is_array( $status ) ){
			
			$smeta_query = array('relation' => 'OR');
			
			//$args['meta_query'][] = array(
				//'key'     => 'Status',
				//'value'   => $status,
				//'compare' => 'IN',
			//);
			
			foreach( $status as $status_key ){
				
				$smeta_query[] = array(
					'key'     => 'Status',
					'value'   => $status_key,
					'compare' => 'LIKE',
				);
				
			} // End foreach
			
			$args['meta_query'][] = $smeta_query;
		
		} // end if
		
		if ( ! empty( $presets['agent_id'] ) ){
			
			$agent_query = array(
				'relation' => 'OR',
				array(
					'key'     => 'Primary_Agent_Id',
					'value'   => $presets['agent_id'],
					'compare' => 'LIKE',
				),
				array(
					'key'     => 'Secondary_Agent_Id',
					'value'   => $presets['agent_id'],
					'compare' => 'LIKE',
				)
			);
			
			$args['meta_query'][] = $agent_query;
			
		} // End if
		
		if( ! empty( $_GET['property_type'] ) && ( $_GET['property_type'] !== 'any' ) ){
			
			$property_type = sanitize_text_field( $_GET['property_type'] );
			
			$args['meta_query'][] = $this->get_property_type_query( $property_type );
			
		} // end if
		
		if ( ! empty( $presets['custom_field'] ) ){
			
			$field_query = array();
			
			$args['meta_query']['relation'] = 'AND';
			
			$field_query['relation'] = 'OR';
			
			$field_values = explode( ',' , $presets['custom_field_value'] );
			
			foreach( $field_values as $f_value ){
				
				$field_query[] = array(
					'key'     => $presets['custom_field'],
					'value'   => $f_value,
					'compare' => 'LIKE',
				);
				
			} // end foreach
			
			$args['meta_query'][] = $field_query;
			
		} // end if
		
		$the_query = new WP_Query( $args );
	
		return $the_query;
		
	} // end get_query
	
	
	protected function get_property_type_query( $property_type ){
		
		$query = array();
		
		switch ( $property_type ){
			
			case 'single-family':
				$query = array(
					'key'     => 'Sub-type',
					'value'   => 'Single Family',
					'compare' => 'LIKE',
				);
				break;
				
			case 'commercial':
				$query = array(
					'key'     => 'Property_Type',
					'value'   => 'Commercial',
					'compare' => 'LIKE',
				);
				break;
			
			case 'condo':
				$query = array(
					'key'     => 'Sub-type',
					'value'   => 'Condominium',
					'compare' => 'LIKE',
				);
				break;
				
			case 'luxury':
				$query = array(
					'key'     => 'Price',
					'value'   => 999999,
					'compare' => '>',
					'type' => 'numeric',
				);
				break;
			case 'development':
				$address_search = array( 'relation' => 'OR' );
				$dev_search_text = $this->get_development_search_text_array();
				foreach( $dev_search_text as $text ){
					$address_search[] = array(
						'key'     => 'Address',
						'value'   => $text,
						'compare' => 'LIKE',
					);
				} // End foreach
				$query = $address_search;
				break;
		} // end switch
		
		return $query;
		
	} // end get_property_type_query
	
	
	public function get_development_search_text_array(){
		
		$search_text = array();
		
		$post_array = get_posts( 
			array( 
				'post_type' 		=> 'developments', 
				'post_status' 		=> 'publish', 
				'posts_per_page' 	=> -1 
			) 
		);
		
		foreach( $post_array as $dpost ){
			
			$text = get_post_meta( $dpost->ID, '_prop_search_text', true );
			
			if ( ! empty( $text ) ){
				
				$search_text[] = $text;
				
			} // End if
			
		} // End foreach
		
		return $search_text;
		
	} // end get_development_search_text_array
	
	
	public function add_status_query( $query ){
		
		$query_args = array();
		
	} // end add_status_query
	
	
	public function add_sort_args( &$args, $presets ){
		
		switch( $presets['sort_by'] ){
			
			case 'Price-numeric-asc':
				$args['orderby'] = 'meta_value_num';
				$args['meta_key']  = 'Price_Sort';
				break;
			case 'Price-numeric-desc':
				$args['orderby'] = 'meta_value_num';
				$args['meta_key']  = 'Price_Sort';
				$args['order'] = 'DESC';
				break;
			case 'date-desc':
				$args['orderby'] = 'post_date';
				$args['order'] = 'DESC';
				break;
			case 'date-asc':
				$args['orderby'] = 'post_date';
				break;
			
		} // end switch
		
	} // end add_sort_args
	
	
	protected function filter_by_status( $properties, $status ){
		
		$filtered_properties = array();
		
		$status_array = explode( $status );
		
		$status_array = array_map( 'strtolower', $status_array );
		
		foreach( $properties as $index => $property ){
			
			$p_stat = strtolower( $property->get_status() );
			
			if ( in_array( $p_stat, $status_array ) ){
				
				$filtered_properties[] = $property;
				
			} // end if
			
		} // end foreach
		
		return $filtered_properties;
		
	}
	
} // end Tremaine_Shortcode_Agents