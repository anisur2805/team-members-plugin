<?php

class Z7_Team_Members_Shortcode {

	/**
	 * Constructor to register the shortcode.
	 */
	public function __construct() {
		add_shortcode( 'team_members', array( $this, 'render_team_members' ) );
	}

	/**
	 * Renders the team members shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output for the shortcode.
	 */
	public function render_team_members( $atts ) {
		wp_enqueue_style( 'team-members-style' );

		// Set default attributes and sanitize inputs.
		$atts = shortcode_atts(
			array(
				'number'          => 5,
				'show_all_button' => 'true',
				'image_position'  => 'top',
			),
			$atts,
			'team_members'
		);

		$posts_per_page  = intval( $atts['number'] );
		$show_all_button = filter_var( $atts['show_all_button'], FILTER_VALIDATE_BOOLEAN );
		$image_position  = ( $atts['image_position'] === 'bottom' ) ? 'bottom' : 'top'; // Ensure only "top" or "bottom"

		$query = new WP_Query(
			array(
				'post_type'      => 'team_member',
				'posts_per_page' => $posts_per_page,
			)
		);

		ob_start();

		if ( $query->have_posts() ) {
			?>
			<section class="team-members-section">
				<div class="team-section-container">
					<div class="team-section-header">
						<h2><?php esc_html_e( 'Meet Our Team', 'z7-team-members' ); ?></h2>
						<p><?php esc_html_e( 'These people work on making our product best.', 'z7-team-members' ); ?></p>
					</div>

					<div class="team-members-grid">
						<?php
						while ( $query->have_posts() ) :
							$query->the_post();

							// Retrieve and sanitize custom fields.
							$bio             = get_post_meta( get_the_ID(), '_team_member_bio', true );
							$truncated_bio   = ( strlen( $bio ) > 80 ) ? substr( $bio, 0, 77 ) . '...' : $bio;
							$member_name     = get_post_meta( get_the_ID(), '_team_member_name', true );
							$member_position = get_post_meta( get_the_ID(), '_team_member_position', true );
							?>
							<div class="team-member-card">
								<?php if ( 'top' === $image_position ) : ?>
									<div class="team-member-image">
										<a href="<?php echo esc_url( get_permalink() ); ?>">
										<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail( 'medium' );
										}
										?>
										</a>
									</div>
								<?php endif; ?>

								<h3 class="team-member-name">
									<a href="<?php echo esc_url( get_permalink() ); ?>">
										<?php echo esc_html( $member_name ); ?>
									</a>
								</h3>
								<div class="team-member-position">
									<?php echo esc_html( $member_position ); ?>
								</div>

								<div class="team-member-bio">
									<?php echo esc_html( $truncated_bio ); ?>
								</div>

								<?php if ( 'bottom' === $image_position ) : ?>
									<div class="team-member-image team-member-image-bottom">
										<a href="<?php echo esc_url( get_permalink() ); ?>">
											<?php
											if ( has_post_thumbnail() ) {
												the_post_thumbnail( 'medium' );
											}
											?>
										</a>
									</div>
								<?php endif; ?>

							</div>
						<?php endwhile; ?>
					</div>

					<?php if ( $show_all_button ) : ?>
						<div class="see-all-button">
							<a href="<?php echo esc_url( get_post_type_archive_link( 'team_member' ) ); ?>">
								<?php esc_html_e( 'See All', 'z7-team-members' ); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</section>
			<?php
		}

		wp_reset_postdata();
		return ob_get_clean();
	}
}
