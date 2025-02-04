<?php get_header(); ?>

<div class="team-member-single">
	<h1><?php echo esc_html( get_post_meta( get_the_ID(), '_team_member_name', true ) ); ?></h1>

	<?php
	if ( has_post_thumbnail() ) {
		the_post_thumbnail( 'large' );
	}
	?>

	<p>
		<strong>
			<?php echo esc_html( get_post_meta( get_the_ID(), '_team_member_position', true ) ); ?>
		</strong>
	</p>

	<p>
		<?php echo wp_kses_post( get_post_meta( get_the_ID(), '_team_member_bio', true ) ); ?>
	</p>

	<div class="back-to-team">
		<a href="<?php echo esc_url( get_post_type_archive_link( 'team_member' ) ); ?>">
			← <?php esc_html_e( 'Back to Team', 'z7-team-members' ); ?>
		</a>
	</div>
</div>

<?php get_footer(); ?>
