<?php
/*
Plugin Name: Fail2ban
Description: Write all login attempts to syslog for integration with fail2ban.
Version: 1.0
Author: Marko Heijnen & Charles Lecklider
License: GPL2
*/

class Fail2Ban {

	public function __construct() {
		add_action( 'wp_login', array( $this, 'wp_login' ),10,2);
		add_action( 'wp_login_failed', array( $this, 'wp_login_failed' ) );

		if ( defined('WP_FAIL2BAN_BLOCKED_USERS') ) {
			add_action( 'authenticate', array( $this, 'authenticate' ), 1, 3 );
		}



		if (defined('WP_FAIL2BAN_BLOCK_USER_ENUMERATION') && true === WP_FAIL2BAN_BLOCK_USER_ENUMERATION) {
			add_filter( 'redirect_canonical', array( $this, 'authenticate' ), 10, 2 );
		}

		if (defined('WP_FAIL2BAN_LOG_PINGBACKS') && true === WP_FAIL2BAN_LOG_PINGBACKS) {
			add_action( 'xmlrpc_call', array( $this, 'xmlrpc_call' ) );
		}
	}

	public function log($log = LOG_AUTH, $custom_log = 'WP_FAIL2BAN_AUTH_LOG') {
		openlog(
			'wordpress('.$_SERVER['HTTP_HOST'].')',
			LOG_NDELAY|LOG_PID,
			defined( $custom_log ) ? constant( $custom_log ) : $log
		);
	}

	public function bail() {
		ob_end_clean();
		header('HTTP/1.0 403 Forbidden');
		header('Content-Type: text/plain');
		exit('Forbidden');
	}

	public function remote_addr() {
		if ( defined('WP_FAIL2BAN_PROXIES') ) {
			if ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) ) {
				$ip = ip2long( $_SERVER['REMOTE_ADDR'] );

				foreach ( explode( ',',WP_FAIL2BAN_PROXIES ) as $proxy ) {
					if ( 2 == count( $cidr = explode( '/',$proxy ) ) ) {
						$net = ip2long( $cidr[0] );
						$mask = ~ ( pow( 2, ( 32 - $cidr[1] ) ) - 1 );
					} else {
						$net = ip2long( $proxy );
						$mask = -1;
					}

					if ( $net == ( $ip & $mask ) ) {
						return ( false === ( $len = strpos( $_SERVER['HTTP_X_FORWARDED_FOR'],',' ) ) )
								? $_SERVER['HTTP_X_FORWARDED_FOR']
								: substr( $_SERVER['HTTP_X_FORWARDED_FOR'], 0, $len );
					}
				}
			}
		}

		return $_SERVER['REMOTE_ADDR'];
	}


	public function wp_login( $user_login, $user ) {
		$this->log();

		syslog( LOG_INFO, "Accepted password for $user_login from " . $this->remote_addr() );
	}

	public function wp_login_failed( $username ) {
		$this->log();
		
		syslog( LOG_NOTICE, "Authentication failure for $username from " . $this->remote_addr() );
	}


	public function authenticate( $user, $username, $password ) {
		if ( ! empty( $username ) && preg_match( '/' . WP_FAIL2BAN_BLOCKED_USERS . '/i', $username ) ) {
			$this->log();

			syslog( LOG_NOTICE, "Blocked authentication attempt for $username from " . $this->remote_addr() );

			$this->bail();
		}

		return $user;
	}

	public function redirect_canonical( $redirect_url, $requested_url ) {
		if ( intval( $_GET['author'] ) ) {
			$this->log();
			
			syslog( LOG_NOTICE, 'Blocked user enumeration attempt from ' . $this->remote_addr() );
			
			$this->bail();
		}

		return $redirect_url;
	}

	public function xmlrpc_call( $call ) {
		if ( 'pingback.ping' == $call ) {
			$this->log( LOG_USER, 'WP_FAIL2BAN_PINGBACK_LOG' );
			
			syslog( LOG_INFO ,"Pingback requested from " . $this->remote_addr() );
		}
	}

}

new Fail2Ban;