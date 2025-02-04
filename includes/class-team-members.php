<?php

class Z7_Team_Members {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_team_member_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta_data' ) );
		add_filter( 'template_include', array( $this, 'load_custom_templates' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Registers the custom post type.
	 */
	public function register_team_member_post_type() {
		$args = array(
			'labels'        => array(
				'name'                  => esc_html__( 'Team Members', 'z7-team-members' ),
				'singular_name'         => esc_html__( 'Team Member', 'z7-team-members' ),
				'featured_image'        => esc_html__( 'Profile image', 'z7-team-members' ),
				'remove_featured_image' => esc_html__( 'Remove Profile image', 'z7-team-members' ),
				'set_featured_image'    => esc_html__( 'Set Profile image', 'z7-team-members' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'rewrite'       => array( 'slug' => 'team-members' ),
			'supports'      => array( 'title', 'thumbnail' ),
			'menu_position' => 5,
			'menu_icon'     => 'dashicons-groups',
		);
		register_post_type( 'team_member', $args );
	}

	/**
	 * Adds a single meta box for all fields.
	 */
	public function add_meta_box() {
		add_meta_box(
			'team_member_meta',
			esc_html__( 'Team Member Details', 'z7-team-members' ),
			array( $this, 'render_meta_box' ),
			'team_member'
		);
	}

	/**
	 * Renders the single meta box with all fields.
	 */
	public function render_meta_box( $post ) {
		// Retrieve saved values.
		$name     = get_post_meta( $post->ID, '_team_member_name', true );
		$bio      = get_post_meta( $post->ID, '_team_member_bio', true );
		$position = get_post_meta( $post->ID, '_team_member_position', true );

		// Nonce field for security.
		wp_nonce_field( 'team_member_meta_nonce', 'team_member_nonce' );

		?>
		<p>
			<label for="team_member_name"><strong><?php esc_html_e( 'Full Name:', 'z7-team-members' ); ?></strong></label>
			<input type="text" id="team_member_name" name="team_member_name" value="<?php echo esc_attr( $name ); ?>" style="width:100%;" />
		</p>
		<p>
			<label for="team_member_position"><strong><?php esc_html_e( 'Position:', 'z7-team-members' ); ?></strong></label>
			<input type="text" id="team_member_position" name="team_member_position" value="<?php echo esc_attr( $position ); ?>" style="width:100%;" />
		</p>
		<p>
			<label for="team_member_bio"><strong><?php esc_html_e( 'Biography:', 'z7-team-members' ); ?></strong></label>
			<textarea id="team_member_bio" name="team_member_bio" rows="4" style="width:100%;"><?php echo esc_textarea( $bio ); ?></textarea>
		</p>
		<?php
	}

	/**
	 * Saves meta box data securely.
	 */
	public function save_meta_data( $post_id ) {
		// Verify nonce for security.
		if ( ! isset( $_POST['team_member_nonce'] ) || ! wp_verify_nonce( $_POST['team_member_nonce'], 'team_member_meta_nonce' ) ) {
			return;
		}

		// Prevent saving during autosaves, revisions, or unauthorized user roles.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Define meta fields with their corresponding sanitization functions.
		$meta_fields = array(
			'_team_member_name'     => 'sanitize_text_field',
			'_team_member_bio'      => 'sanitize_textarea_field',
			'_team_member_position' => 'sanitize_text_field',
		);

		foreach ( $meta_fields as $meta_key => $sanitize_callback ) {
			$field_name = str_replace( '_team_member_', 'team_member_', $meta_key );

			if ( isset( $_POST[ $field_name ] ) ) {
				$new_value = call_user_func( $sanitize_callback, $_POST[ $field_name ] );

				// Get the old value to prevent unnecessary database updates.
				$old_value = get_post_meta( $post_id, $meta_key, true );

				if ( $new_value !== $old_value ) {
					update_post_meta( $post_id, $meta_key, $new_value );
				}
			}
		}
	}

	/**
	 * Loads custom templates for the post type.
	 */
	public function load_custom_templates( $template ) {
		if ( is_post_type_archive( 'team_member' ) ) {
			$archive_template = Z7_TEAM_MEMBERS_PLUGIN_PATH . 'templates/archive-team-member.php';
			if ( file_exists( $archive_template ) ) {
				return $archive_template;
			}
		}

		if ( is_singular( 'team_member' ) ) {
			$single_template = Z7_TEAM_MEMBERS_PLUGIN_PATH . 'templates/single-team-member.php';
			if ( file_exists( $single_template ) ) {
				return $single_template;
			}
		}

		return $template;
	}

	/**
	 * Enqueues styles for the plugin.
	 */
	public function enqueue_styles() {
		wp_register_style( 'team-members-style', Z7_TEAM_MEMBERS_PLUGIN_URL . 'assets/style.css' );

		if ( is_post_type_archive( 'team_member' ) ) {
			wp_enqueue_style( 'team-members-archive-style', Z7_TEAM_MEMBERS_PLUGIN_URL . 'assets/team-archive.css' );
		}

		if ( is_singular( 'team_member' ) ) {
			wp_enqueue_style( 'team-member-style', Z7_TEAM_MEMBERS_PLUGIN_URL . 'assets/team-single.css' );
		}
	}
}
