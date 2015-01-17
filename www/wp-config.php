<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

if ( ! file_exists( dirname( __FILE__ ) . '/../wp-config.php' ) ) {
	exit;
}

include( dirname( __FILE__ ) . '/../wp-config.php' );


/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

if ( ! defined( 'AUTH_KEY' ) ) {
	/**#@+
	 * Authentication Unique Keys and Salts.
	 *
	 * Change these to different unique phrases!
	 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
	 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
	 *
	 * @since 2.6.0
	 */
	define('AUTH_KEY',         'h*}--wH$z0h+gG|Xe%Chht^iCATJ]j5GuaHhY)Qe(EIN%hVg>R!G%7kJWsT:dk=$');
	define('SECURE_AUTH_KEY',  'F<}2nx_-%Rc6Cqjt!7(Ev~O|AR^f5K sFhq!@c2VC3#r5u=zk)zX2Y{Y:TiT?S c');
	define('LOGGED_IN_KEY',    'gCD*ku&k&k1 Y_6=GJ1KC[`LFn|IXT8_S%j3Qbu6)KHo3r[}5Vm)FET}+;E+Pe5<');
	define('NONCE_KEY',        'wB$+.-b1,T/Kc3~V3(N~TBFC.xU-%vR,xJ[YS2)e<,a+(]<ugn9|,A@2Gu*eXqa ');
	define('AUTH_SALT',        ' Ds`rW4M6fLIgv+-=M+k=/W7z-7&J8#-+Q`0k++=%)}N4aOrT<Paxs=``|LD+B1T');
	define('SECURE_AUTH_SALT', '^*8/gKFNM3!qBI+Ji-%L.Rp@0xH?<:G((a+EeqqWI4&lnLJ$N<OE-aLiua3:w@Z!');
	define('LOGGED_IN_SALT',   'b!w71NNm-kh;pT+Y8>ij9s:=l.|#BSrH4ozW{@Oin/+6X2YsA;kYm:+NHpWS9/-y');
	define('NONCE_SALT',       'MBN._A^V]x`7$@ta4[2G~eVwy{6^`8,><^o4nr1s${8;O&!V*}Rces rL%*`AI4C');

	/**#@-*/
}


// Define Site URL: WordPress in a subdirectory.
if ( isset( $_SERVER['HTTP_HOST'] ) ) {
	if ( ! defined( 'WP_SITEURL' ) ) {
		define( 'WP_SITEURL', 'http://' . $_SERVER['HTTP_HOST'] . '/wp' );
	}

	// Define Home URL
	if ( ! defined( 'WP_HOME' ) ) {
		define( 'WP_HOME', 'http://' . $_SERVER['HTTP_HOST'] );
	}

	define( 'WP_CONTENT_URL', WP_HOME . '/content' );
}

// Define path & url for Content
define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/content' );

// Prevent editing of files through the admin.
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', true );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
