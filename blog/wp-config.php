<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wbkgbvggmh');

/** MySQL database username */
define('DB_USER', 'wbkgbvggmh');

/** MySQL database password */
define('DB_PASSWORD', 'kx8KR2K8GB');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'CRyR2?|^jH>D&{%S|# XVy/qgxxZ3`THA252.^H`O9r%s`w7>kQ^=1 !n7QCj+HY');
define('SECURE_AUTH_KEY',  'F{-ctvh#r*S@Q15`~3K %wS]Hd&0Y?pPMoB}|M,HIc<]IdC!+@]LZf.sSH|W!YA+');
define('LOGGED_IN_KEY',    ']~|J*RRC0bL*Xric_ nF&nnGs]ZXuk%Ct{*rXh+5Itzq1.t..J]-eRTV9,1|$?_p');
define('NONCE_KEY',        ' nn_BaIV@It5*~Z4J<4}chh^qS7yHU5<<%buKa:5|.G;igyw?NAhO^zwQt^$![6~');
define('AUTH_SALT',        ',X&SOe5 ,W:k]xMU*||}EM49Z}VY+p4Q$_(+m28-@zV>}$8|MOu{/O~?:}%ez]Mr');
define('SECURE_AUTH_SALT', 'kN?d]m(+OpSBZCh{?KA&I}q+:+|6ld%2!sNE<$<4l,8e6gG@/7&FYeRs-;r8lI+.');
define('LOGGED_IN_SALT',   'i+#+?m||-1nxxWCyTdEKn-2w_s.w>l=_2qM7k@0J{F+QL%lFCP2TFYy+cPXZ`}TR');
define('NONCE_SALT',       '5`-jzSSc~0x(cNRZy|BIi!W@&s{<*rn++m;xV+r|;^_mvS3IF-FmMwYR jH~@-It');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
