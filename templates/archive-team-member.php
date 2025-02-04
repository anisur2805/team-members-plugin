<?php get_header(); ?>

<div class="team-members-archive">
	<div class="team-section-container">
		<div class="team-section-header">
			<h2><?php esc_html_e( 'Meet Our Team: Archive', 'z7-team-members' ); ?></h2>
			<p><?php esc_html_e( 'These people work on making our product best.', 'z7-team-members' ); ?></p>
		</div>

		<?php
		// Get current page number.
		$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

		// Custom Query for Pagination.
		$team_members_query = new WP_Query(
			array(
				'post_type'      => 'team_member',
				'posts_per_page' => 6,
				'paged'          => $paged,
			)
		);

		if ( $team_members_query->have_posts() ) :
			?>
			<div class="team-members-grid">
				<?php
				while ( $team_members_query->have_posts() ) :
					$team_members_query->the_post();

					// Retrieve custom fields with proper escaping.
					$bio             = get_post_meta( get_the_ID(), '_team_member_bio', true );
					$truncated_bio   = ( strlen( $bio ) > 80 ) ? substr( $bio, 0, 77 ) . '...' : $bio;
					$member_name     = get_post_meta( get_the_ID(), '_team_member_name', true );
					$member_position = get_post_meta( get_the_ID(), '_team_member_position', true );
					?>
					<div class="team-member-card">
						<div class="team-member-image">
							<a href="<?php echo esc_url( get_permalink() ); ?>">
								<?php
								if ( has_post_thumbnail() ) {
									the_post_thumbnail( 'medium' );
								}
								?>
							</a>
						</div>
						<h3 class="team-member-name">
							<a href="<?php echo esc_url( get_permalink() ); ?>">
								<?php echo esc_html( $member_name ); ?>
							</a>
						</h3>
						<p class="team-member-position">
							<strong><?php echo esc_html( $member_position ); ?></strong>
						</p>
						<p class="team-member-bio"><?php echo esc_html( $truncated_bio ); ?></p>
					</div>
				<?php endwhile; ?>
			</div>

			<!-- Pagination Section -->
			<div class="pagination">
				<?php
				echo wp_kses_post(
					paginate_links(
						array(
							'total'     => $team_members_query->max_num_pages,
							'current'   => $paged,
							'prev_text' => esc_html__( '← Previous', 'z7-team-members' ),
							'next_text' => esc_html__( 'Next →', 'z7-team-members' ),
						)
					)
				);
				?>
			</div>

		<?php endif; ?>

	</div>
</div>

<?php
// Restore the global post data.
wp_reset_postdata();

get_footer();
