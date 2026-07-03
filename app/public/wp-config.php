<?php
define('WP_CACHE', true); // WP-Optimize Cache
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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );
/** Database username */
define( 'DB_USER', 'root' );
/** Database password */
define( 'DB_PASSWORD', 'root' );
/** Database hostname */
define( 'DB_HOST', 'localhost' );
/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );
/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );
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
define( 'AUTH_KEY',          'XTtT^~6F3zYS/X.{Ud7=7yp|KV-??UyHSVKc?s#_lTxk$S?f),B%eQ>NU$PJVLd~' );
define( 'SECURE_AUTH_KEY',   'KF#!b2(zwLP4UCeyOJ$Xp77,ut*efZ6,O@Oa{,99Ohwh8j1 V#Ak;7BCibK7D[k;' );
define( 'LOGGED_IN_KEY',     'P1i[F=vG*`Ldfbq4x8x]w(W6@n`IH+gqoKasg1tI;--Y5DY{yaz8,rTAiqdV_uQO' );
define( 'NONCE_KEY',         ' UKlK1Fs)^UT$Di(H%/(Vc8BQh==H<Q~Km:j{E4L{r(9+|A93N6pQA-w.8h,G)[C' );
define( 'AUTH_SALT',         '*qcrrAH*cH~ }Z9H)QlRmsHUe^=>Js09?%g)IClk)B;P5(R*u)1T=@I7w*`!!g3}' );
define( 'SECURE_AUTH_SALT',  '&4Gy;Mf^[])pC;U*Sr/yhWCc/^s@g2cJ61dadlo?oY{or-%Om^:Q$(=q7Z`Th>tg' );
define( 'LOGGED_IN_SALT',    'gcW&=GY$VO@a8ZOSXYU_LcX[sF&$c:^i=7a4%(z7a>p*jMH*K+;WWK_awUjwPY;K' );
define( 'NONCE_SALT',        '*[/|w^UNbN]vOf*{>I(xqHCXxWEsUDm(YNloNF |Q7Pqk$oUE!,dnZg%lx|S*/Te' );
define( 'WP_CACHE_KEY_SALT', 'y_Z{^4Ha}l`j_O ,yh>:NaXu=eWqw.i6D3MUJ<q`F}/[w}r:mz{{xw{<@p<m8B_W' );
/**#@-*/
/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';
/* Add any custom values between this line and the "stop editing" line. */
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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}
define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';