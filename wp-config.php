<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'wordpress' );

/** Database password */
define( 'DB_PASSWORD', '0a78f58b48c31d1235fb5fc021b9553df2fedcd2ba6d85e6' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

// define('WP_ALLOW_REPAIR', true);
/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'R?t+PP7@[NmfxsE]A;UNAg@i:mK%zae@AHZ*?f;Jwj#dYWlSO+)=R^h4YrK6h!,h' );
define( 'SECURE_AUTH_KEY',  ':!Y^)D76t>X8-wu*_TB0`d@80lTwt$L9.=a8i.U]0d#PtO2!^k]h|:MPBH~b.x 4' );
define( 'LOGGED_IN_KEY',    'vH=F|g#Ps^d{xLJf]%?MI7xO4JMx+Zr]AX_nC7pj..g/FV6wmAZ%K+ycVL}*TLk*' );
define( 'NONCE_KEY',        'vt;[FFOJ[fu7iA9T&=IxW#Dj`1xHPJ%x}VQ3bf<V=wUb8]3S&kXOKTMhDuIeCq@6' );
define( 'AUTH_SALT',        '|pHm,?@9knH9M-VhSLC98`}I~wfN/7;S[UKY:`W&6xOf1fHCby[7zUX%Ws]0[4MO' );
define( 'SECURE_AUTH_SALT', 'L--hAkLnm!0-5VA[?BLXQo}z#w-vzk Dl|OZG$]yK~8tyn@5K~}~m_}ER{;#g<:A' );
define( 'LOGGED_IN_SALT',   '^LV?5FM3go*FoJ;|50n(y5#dCov4@C,%[cR]C>+ryC/WQ8cr.`u3$@=GKWCf0Yc>' );
define( 'NONCE_SALT',       '1{<`GE`H#Jfp0Ug5%u)?<8=+z<Fl;vT47963My?If0( nUJoc4:zq)bDpo!XBDQ[' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
