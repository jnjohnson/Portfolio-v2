<?php declare(strict_types=1);
/**
 * Config
 *
 * @package wp-fail2ban
 * @since   4.4.0   Require PHP 7.4
 * @since   4.0.0
 */
namespace org\lecklider\charles\wordpress\wp_fail2ban;

defined( 'ABSPATH' ) or exit;

require_once 'lib/tab.php';
require_once 'lib/logging.php';
require_once 'config/block.php';
require_once 'config/logging.php';
require_once 'config/plugins.php';
require_once 'config/remote-ips.php';
require_once 'config/syslog.php';

/**
 * Init
 */
function init_tabs( $init ) {
	if ( ! $init ) {
		new TabBlock();
		new TabLogging();
		new TabPlugins();
		new TabRemoteIPs();
		new TabSyslog();
	}
	return true;
}
add_filter( 'wp_fail2ban_init_tabs', __NAMESPACE__ . '\init_tabs' );

/**
 * Get network settings messages.
 *
 * @since  4.4.0    Add return type
 * @since  4.3.2.2  Merge admin_notices and network_admin_notices
 * @since  4.3.2.1  Add filter
 * @since  4.3.0
 *
 * @return void
 */
function admin_notices(): void {
	global $wp_settings_errors;

	$screen = get_current_screen();
	switch ( $screen->id ) {
		default:
			if ( false === apply_filters( __FUNCTION__, false, $screen->id ) ) {
				break;
			}
		case 'security_page_wp-fail2ban-premium-network':
		case 'wp-fail2ban_page_wpf2b-settings-network':
			if ( $transients = get_site_transient( 'settings_errors' ) ) {
				$wp_settings_errors = array_merge( (array) $wp_settings_errors, $transients );
				delete_site_transient( 'settings_errors' );
			}
			// fallthrough
		case 'security_page_wp-fail2ban-premium':
		case 'wp-fail2ban_page_wpf2b-settings':
			settings_errors();
			break;
	}
}
add_action( 'admin_notices', __NAMESPACE__ . '\admin_notices' );
add_action( 'network_admin_notices', __NAMESPACE__ . '\admin_notices' );

/**
 * Render Security settings.
 *
 * @since  4.4.0    Add return type
 * @since  4.3.0
 *
 * @return void
 */
function security(): void {
	$tabs = array(
		'logging',
		'syslog',
		'block',
		'remote-ips',
		'plugins',
	);
	$tabs = apply_filters( __METHOD__ . '.tabs', $tabs );
	$page = apply_filters( __METHOD__ . '.page', plugin_basename( WP_FAIL2BAN_DIR ) );

	render_tabs( $tabs, $page, __( 'Security', 'wp-fail2ban' ) );
}

/**
 * Render Settings.
 *
 * @since  4.4.0    Add return type
 * @since  4.4.0    Remove redundant version check
 * @since  4.0.0
 *
 * @return void
 */
function settings(): void {
	$tabs = array();

	if ( ! function_exists( '\add_security_page' ) ) {
		$tabs = array(
			'logging',
			'syslog',
			'block',
			'remote-ips',
			'plugins',
		);
	}
	$tabs = apply_filters( __METHOD__ . '.tabs', $tabs );

	render_tabs( $tabs, 'wpf2b-settings' );
}

/**
 * Render Tabs.
 *
 * @since  4.4.0    Add type hints, return type
 * @since  4.3.0
 *
 * @param  array  $tabs       List of slugs of tabs to render
 * @param  string $menu       Menu slug
 *
 * @return void
 */
function render_tabs( array $tabs, string $menu, string $title = null ): void {
	if ( is_null( $title ) ) {
		$title = __( 'Settings', 'wp-fail2ban' );
	}
	$active_tab = TabBase::getActiveTab();

	?>
<div class="wrap" id="wp-fail2ban" style="margin-top: 0">
	<?php echo apply_filters( __METHOD__ . '.title', sprintf( '<h1>%s</h1>', $title ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Always static, may contain HTML ?>
	<div class="wp-fail2ban-columns">
		<div class="content-column" style="flex: 1;">
			<hr class="wp-header-end">

			<h2 class="nav-tab-wrapper wp-clearfix">
	<?php
	foreach ( $tabs as $slug ) {
		$class = 'nav-tab';
		if ( $active_tab->getSlug() == $slug ) {
			$class .= ' nav-tab-active';
		}
		$params = apply_filters(
			__METHOD__ . '.params',
			array(
				'page' => $menu,
				'tab'  => $slug,
			)
		);
		printf(
			'<a class="%s" href="?%s">%s</a>',
			$class, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Always static
			http_build_query( $params ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already urlencoded
			TabBase::getTabName( $slug ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Always static
		);
	}
	?>
			</h2>

	<?php
	// Because the settings API was never finished we need an ugly hack
	$action = sprintf(
		'%s?tab=%s',
		admin_url(
			is_network_admin()
			? 'admin-post.php'
			: 'options.php'
		),
		$active_tab->getSlug() // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Always static
	);
	?>

			<form action="<?php echo $action; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped ?>" method="post">
	<?php
	settings_fields( 'wp-fail2ban' );
		$active_tab->render();
	?>
			</form>
		</div>
	<?php if ( 1 < count( $tabs ) ) : // @wpf2b-free-only-start ?>
		<div class="sidebar-column">
			<h6>WP fail2ban <?php echo WP_FAIL2BAN_VER; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static text ?>-LTS</h6>
			<div class="card">
				<h3 class="eyebrow">Need more protection?</h3>
				<h2><span class="title">WP fail2ban Premium</span> goes beyond <code>fail2ban</code></h2>
				<p>Add a Web Application Firewall (WAF), the full WP&nbsp;fail2ban&nbsp;Blocklist, geo-blocking, honeypots, persistent event history, and automatically maintained security data.</p>
				<p>Premium also lets you edit these settings here instead of defining them in <code>wp-config.php</code>.</p>
		<?php $active_tab->render_sidebar(); ?>
				<div class="cta"><a class="button button-primary" href="https://wp-fail2ban.com/pricing/" rel="noopener noreferrer" target="_blank">See Premium plans</a> <a class="text-link" href="https://wp-fail2ban.com/features/" rel="noopener noreferrer" target="_blank">Compare all features →</a></div>
			</div>
			<div class="compatibility">
			<?php if ( is_premium_compatible() ) : ?>
				<p class="premium-compatible"><span class="dashicons dashicons-yes"></span> Your site is compatible with Premium.</p>
			<?php else : ?>
				<p class="premium-incompatible"><span class="dashicons dashicons-no"></span> Your site is not compatible with Premium.</p>
				<p>Premium requires PHP 8.1 or higher and WordPress 6.3 or higher.</p>
			<?php endif; ?>
			</div>
		</div>
	</div>
		<?php endif; // @wpf2b-free-only-end ?>
</div>
	<?php
}

/**
 * Helper: filtered defined(...)
 *
 * @since  4.4.0    Add type hint
 * @since  4.3.0
 *
 * @param  string $define
 * @return mixed
 */
function have_defined( string $define ) {
	return apply_filters( __NAMESPACE__ . '\have_defined', defined( $define ), $define );
}

