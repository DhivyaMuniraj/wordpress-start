<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache


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

//Using environment variables for memory limits
$wp_memory_limit = (getenv('WP_MEMORY_LIMIT') && preg_match("/^[0-9]+M$/", getenv('WP_MEMORY_LIMIT'))) ? getenv('WP_MEMORY_LIMIT') : '128M';
$wp_max_memory_limit = (getenv('WP_MAX_MEMORY_LIMIT') && preg_match("/^[0-9]+M$/", getenv('WP_MAX_MEMORY_LIMIT'))) ? getenv('WP_MAX_MEMORY_LIMIT') : '256M';

/** General WordPress memory limit for PHP scripts*/
define('WP_MEMORY_LIMIT', $wp_memory_limit );

/** WordPress memory limit for Admin panel scripts */
define('WP_MAX_MEMORY_LIMIT', $wp_max_memory_limit );


//Using environment variables for DB connection information

// ** Database settings - You can get this info from your web host ** //
$connectstr_dbhost = getenv('DATABASE_HOST');
$connectstr_dbname = getenv('DATABASE_NAME');
$connectstr_dbusername = getenv('DATABASE_USERNAME');
$connectstr_dbpassword = getenv('DATABASE_PASSWORD');

// Using managed identity to fetch MySQL access token
if (strtolower(getenv('ENABLE_MYSQL_MANAGED_IDENTITY')) === 'true') {
	try {
		require_once(ABSPATH . 'class_entra_database_token_utility.php');
		if (strtolower(getenv('CACHE_MYSQL_ACCESS_TOKEN')) !== 'true') {
			$connectstr_dbpassword = EntraID_Database_Token_Utilities::getAccessToken();
		} else {
			$connectstr_dbpassword = EntraID_Database_Token_Utilities::getOrUpdateAccessTokenFromCache();
		}
	} catch (Exception $e) {
		// An empty string displays a 502 HTTP error page rather than a database connection error page. So, using a dummy string instead.
		$connectstr_dbpassword = '<dummy-value>';
		error_log($e->getMessage());
	}
}

/** The name of the database for WordPress */
define('DB_NAME', $connectstr_dbname);

/** MySQL database username */
define('DB_USER', $connectstr_dbusername);

/** MySQL database password */
define('DB_PASSWORD',$connectstr_dbpassword);

/** MySQL hostname */
define('DB_HOST', $connectstr_dbhost);

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/** Enabling support for connecting external MYSQL over SSL*/
$mysql_sslconnect = (getenv('DB_SSL_CONNECTION')) ? getenv('DB_SSL_CONNECTION') : 'true';
if (strtolower($mysql_sslconnect) != 'false' && !is_numeric(strpos($connectstr_dbhost, "127.0.0.1")) && !is_numeric(strpos(strtolower($connectstr_dbhost), "localhost"))) {
	define('MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL);
}


/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'M1<)lj6-C-Cl{^.WIX 1ZU.@,e6nZgQc>na0vMcF]NF>|/+0T&[,-l9%NS:T=mqn' );
define( 'SECURE_AUTH_KEY',  'xj&_t=<w]C]Z ][w*JfMe`<I;<x/J6P*fMIC7p w!UUx{e@QK0$KbuY:>{*+1<Zk' );
define( 'LOGGED_IN_KEY',    'U9H<`#Y<uc9-bC0{#Obxg,mhm6cl,=x[t=>}E;`<+j}ZABs6;qvLi2A]8X%-76Dn' );
define( 'NONCE_KEY',        'S&JxSeI6~|)nq%7>>wS|pP]8yLA,P~[GUE z;Epe&qf|>gTvk18oxW5nv)^hHy=g' );
define( 'AUTH_SALT',        '[4l_r[#X16H{}M@p7MRnE,xzVrZ?P!N96Z*7tz?9negv-;.[o_cz]%?1aX1TC`&|' );
define( 'SECURE_AUTH_SALT', '?yU!DJeSY1r|BhD%Y--BUJ],l)4c,H>uBZ>KE:rFcA@/.SO?!YUf=;JGO%|XvPM1' );
define( 'LOGGED_IN_SALT',   'k6J:ia;~tn+5=-`lk[B2G?#J=dUG2DQnUE$?lU5A>[h|!<_(}*~Bi_>eJi`I8Seq' );
define( 'NONCE_SALT',       'a}_[a1+KJr:iA8<Kz9v}RBfA[h?ps!vD&$Bt[:6XeyB-<m~PdDP/&C|GygB^0ZlZ' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy blogging. */
/**https://developer.wordpress.org/reference/functions/is_ssl/ */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
	$_SERVER['HTTPS'] = 'on';

$http_protocol='http://';
if (!preg_match("/^localhost(:[0-9])*/", $_SERVER['HTTP_HOST']) && !preg_match("/^127\.0\.0\.1(:[0-9])*/", $_SERVER['HTTP_HOST'])) {
	$http_protocol='https://';
}

//Relative URLs for swapping across app service deployment slots
define('WP_HOME', $http_protocol . $_SERVER['HTTP_HOST']);
define('WP_SITEURL', $http_protocol . $_SERVER['HTTP_HOST']);
define('WP_CONTENT_URL', '/wp-content');
define('DOMAIN_CURRENT_SITE', $_SERVER['HTTP_HOST']);

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
