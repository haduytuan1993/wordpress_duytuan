<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'haduytuan1994' );

/** MySQL database username */
define( 'DB_USER', 'haduytuan1994' );

/** MySQL database password */
define( 'DB_PASSWORD', 'a20011994' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );
/** Bật chức năng Revisions**/
define('WP_POST_REVISIONS', 3 );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'rz7; y]a=uW2U.#~vu`1rW6JB)Rryw>^<j1xn~335tc#G/m2Ap0}%j5d(s6U^n+h' );
define( 'SECURE_AUTH_KEY',  '2P(mm(Ia}XEZhKr4P[8XvFds6(E5CgF@rM{Ko{26q~2Ym3(lb%dZR!fTzHY/zH y' );
define( 'LOGGED_IN_KEY',    'Rws{%1X6x4:~kT(N1Yme;&5$/mbB []FY)267nWa2r !8v7R=pj>hPO?sB^/jg8l' );
define( 'NONCE_KEY',        'Dk6-TZ!$W(u(|s?Iv.m{w>DpnvZKDf1?W>(X$EZF,r_4s.GR/EjI+LN(^xUJs?V&' );
define( 'AUTH_SALT',        '`$kbkmBk(f9WkroExFh~({>8,iA( ?/zoJ[6KK*ereCG)Z$#9<m!anu(s2M!fI.#' );
define( 'SECURE_AUTH_SALT', '#H+d[V zkN.1`qyUSH6hX8 ~(mu*7cwdImje(C`~]1c-E9xtQp/4jT% lW1?d8F>' );
define( 'LOGGED_IN_SALT',   'q67=A$jJLN{*X7-(]xLh ?{5;!hSm*Roj_+[^b5jTbqzQ!@Y4jv]6{#.PH_lxYtF' );
define( 'NONCE_SALT',       '}Mi^xC?^HqPd%Ofio^h1D8uN3G?rQ.ishk:b)Kf0Lg#NNceOH5(O9/x[EnYhHPpJ' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp6_';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
