<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 */

/**
 * Displays each project.
 *
 * @param    string   $project_type    			 The type of project to be displayed.
 * @param    string   $project_type_title    The title of the project type to be displayed.
 */
function loop_over_type( $project_type, $project_type_title ) {
	// Display the project type title
	?>

	<div class="project-type">
		<button class="project-type-title collapse">
			<?php echo esc_html( $project_type_title ); ?>
			<span class="expansion-symbol plus" style="display: none;">+</span>
			<span class="expansion-symbol minus">-</span>
		</button>
		<div class="projects">

			<?php
			while ( have_posts() ) :
				global $post;
				the_post();
				$post_id = $post->ID;
				// Display associated projects for the given project type
				$post_project_type = get_post_meta( $post_id, 'project_type', true);
				if ( $post_project_type == $project_type ) {
					display_project( $post );
				}
			endwhile;
			?>

		</div>
	</div>

	<?php
}

/**
 * Displays each project.
 *
 * @param    WP_POST   $post            The project post.
 */
function display_project( $post ) {

	$contact_name = get_post_meta( $post->ID, 'contact_name', true );
	$contact_email = get_post_meta( $post->ID, 'contact_email', true );
	$slug = get_post_field( 'post_name', get_post() );
	$link = get_page_link();
	?>

	<div class="project-container">

		<h3 class="project-title"><?php the_title(); ?></h3>
		<div class="project-info">
			<div class="project-description">

				<?php
				the_content();
				if ( $contact_name ) {
					?>
	
					<p class="project-contact">
						<b>Contact:</b>
						
						<?php
						if ( $contact_email ) {
							?>

							<a href="mailto:<?php echo esc_attr( $contact_email ); ?>">
	 							<?php echo esc_html( $contact_name ); ?>
							</a>

							<?php
						} else {
							echo esc_html( $contact_name );
						}
						?>

					</p>

					<?php
				}
				?>

			</div>

			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'thumbnail', array('class' => 'project-logo' ));
			}
			?>

		</div>
	</div>

	<?php
}

get_header(); 
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">

		<div class="wp-spg-projects">
			<div class="clearfix">
	
				<?php
				if ( have_posts() ) :
					?>
	
					<header class="entry-header">
						<h1 class="entry-title">
							<?php	echo esc_html( post_type_archive_title( '', false ) ); ?> 
						</h1>
					</header><!-- .page-header -->
		
					<?php
					// Repeat the Loop (for current, recurring, and past projects)
					loop_over_type( 'current', 'Current Projects' );
					loop_over_type( 'recurring', 'Recurring Projects' );
					loop_over_type( 'past', 'Past Projects' );
					// (Op-Eds currently follow the same behavior as other projects)
					loop_over_type( 'oped', 'Science Policy Op-Eds' );
	
				endif;
				?>
	
			</div>
		</div><!-- .wp-spg-projects -->

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
