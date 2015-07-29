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
define('WP_CACHE', true); //Added by WP-Cache Manager
define('ENABLE_CACHE', TRUE);
define('DB_NAME', 'tenzi');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         '6Cft<+PD7E}kcIqqoYEm9tW0Y`Dp}w_e =;Sw1.$w:E>0)9sVqCGW[&:u#cAtn$K');
define('SECURE_AUTH_KEY',  'cL$jOJ}OE/tX,qqWqIoKFUvQEKfa|!yK5<I1>NXzI%Ffd+aG.XLx2+-%xM.;|C7X');
define('LOGGED_IN_KEY',    'LHT=BfzUzmaMI#gwmg#q{^$hWzNJ0}r+k33Y/-STCtn#G0R_p{|iYI|;9kSg5InJ');
define('NONCE_KEY',        'C62Zmc##dv/3+_^%G=)vFe$Q~KE.V1W,3!+}Fr!JuOt-PXj=$t~O:-Z?;-]SPS+9');
define('AUTH_SALT',        'ZFSb(v`JdO;5eSu{z0|%(/d3f%*TH6g8}w@C$~i/sny0=dP{ApH_WK+%>BYta?J.');
define('SECURE_AUTH_SALT', 'zM!$ nJn[J0wFL[ajGT|+v_&Q+y:iKiM)l#1WM>ySGaA5QBKnEpi)9Tt- .-?TGD');
define('LOGGED_IN_SALT',   '+~p%d]2hhRe(dk%frx <2.pt*2`oE>#7^3?cG#%+2$jnwTwAQ+c-0E(vUyQ}Oy.[');
define('NONCE_SALT',       '4S]*:@uE,<+QL<.g^*3G[[b)|Ag6BH#_ILpVX{WLa&]!62Pu- 00g]|#PYl0~)dw');

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

//admin
//Tanzi_2015#
///shop/%product_cat%
