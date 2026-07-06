<?php
/**
 * Site header: skip link, utility bar, brand lockup, primary tab nav.
 *
 * @package nu-research
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url( get_theme_file_uri( 'assets/img/favicon-32x32.png' ) ); ?>">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url( get_theme_file_uri( 'assets/img/favicon-16x16.png' ) ); ?>">
	<link rel="apple-touch-icon" sizes="152x152" href="<?php echo esc_url( get_theme_file_uri( 'assets/img/apple-touch-icon.png' ) ); ?>">
	<?php wp_head(); ?>
	<noscript>
		<style>
			/* AOS hides [data-aos] elements (opacity:0) until JavaScript reveals them.
			   Without JS, restore visibility so all content is readable. */
			[data-aos] { opacity: 1 !important; transform: none !important; }
		</style>
	</noscript>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to main content', 'nu-research' ); ?></a>

<header class="site-header">
	<div class="utility-bar">
		<div class="wrap utility-bar__row">
			<span class="utility-label"><?php esc_html_e( 'Explore Northeastern', 'nu-research' ); ?></span>
			<?php do_action( 'nu_research_utility_bar' ); ?>
		</div>
	</div>

	<div class="brand-bar">
		<div class="wrap brand-row">
			<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="brand-lockup"><?php esc_html_e( 'Northeastern University', 'nu-research' ); ?></span>
				<span class="brand-unit"><?php esc_html_e( 'Dept. of Software Engineering', 'nu-research' ); ?></span>
				<span class="brand-sub"><?php esc_html_e( 'WordPress Research Fellows Program', 'nu-research' ); ?></span>
			</a>
			<button class="nav-toggle" aria-expanded="false" aria-controls="primary-nav">
				<span class="nav-toggle-icon" aria-hidden="true"></span>
				<span class="nav-toggle-label"><?php esc_html_e( 'Menu', 'nu-research' ); ?></span>
			</button>
		</div>
	</div>

	<nav id="primary-nav" class="primary-nav" aria-label="<?php esc_attr_e( 'Program pages', 'nu-research' ); ?>">
		<div class="wrap">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav-tabs',
					'fallback_cb'    => 'wp_page_menu',
					'depth'          => 2,
					'walker'         => new NU_Research_Nav_Walker(),
				)
			);
			?>
		</div>
	</nav>
</header>

<main id="main" class="site-main">
