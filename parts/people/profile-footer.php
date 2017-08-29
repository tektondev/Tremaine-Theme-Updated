<section class="tre-profile-tabs">
	<div class="wrap">
		<?php if ( empty( $_GET['property_status'] ) ):?>
        <div class="tre-tabs" style="margin-bottom: 18px">
        	<nav><?php if( $shortcode_1 ):?><a href="#" class="tre-active"><?php echo get_theme_mod('crest_profile_shortcode_1_label', '');?></a><?php endif;?><?php if( $shortcode_2 ):?><a href="#"><?php echo get_theme_mod('crest_profile_shortcode_2_label', '');?></a><?php endif;?></nav>
        	<div class="tre-tabs-wrapper">
            	<?php if( $shortcode_1 ):?><div class="tremaine-shortcode-item tab-section tre-active">
                	<?php echo do_shortcode( $shortcode_1 );?>
                </div><?php endif;?>
                <?php if( $shortcode_2 ):?><div class="tremaine-shortcode-item tab-section">
                	<?php echo do_shortcode( $shortcode_2 );?>
                </div><?php endif;?>
            </div>
        </div>
		<a href="?property_status=active" class="tre-button-light" style="margin-right: 18px">All Active Listings</a>
		<a href="?property_status=sold" class="tre-button-light">All Sold Listings</a>
		<?php else:?>
			<div class="tre-profile-about-content" style="margin-top: 60px;">
				<h2><?php echo $name;?>: <?php if ( 'sold' === $_GET['property_status'] ):?>Recently Sold Listings<?php else:?>Active Properties<?php endif;?></h2>
				<?php if ( 'sold' === $_GET['property_status'] ):?>
				<?php echo do_shortcode( $closed_shortcode );?>
				<?php else:?>
				<?php echo do_shortcode( $active_shortcode );?>
				<?php endif;?>
			</div>
		<?php endif;?>
    </div>
</section>