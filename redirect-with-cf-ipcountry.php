<?php
/*
 * Plugin Name: redirect-with-cf-ipcountry
 * Plugin URI: https://occ.me
 * Description: Simple plugin to conditionally redirect users to a sub page, depending on their origin country.
 * Version: 1.0.0
 * Author: Onur Cakmak
 * Author URI: https://occ.me
 * Text Domain: redirect-with-cf-ipcountry
 * Domain Path: /languages/
 * //Author: Onur Cakmak
 * //Author URI: http://occ.me
 */

/* Copyright 2021  Onur Cakmak  (email: occ@occ.me)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined('ABSPATH') or die('Cannot access pages directly.');

if (!defined('WPINC')) {
        die;
}

class Redirect_With_CF_IPCountry {
    public static $cookie_name = 'CMAKLANGREDIR';
    public static $redir_if_not_country = 'TR';
    public static $redir_from_uri = '/';
    public static $redir_to_uri = '/en';

    function is_bot(string $ua) {
        return preg_match('/bot|crawl|slurp|spider|mediapartners/i', $ua) === 1;
    }

    function should_redirect() {
        return $_SERVER['REQUEST_URI'] == self::$redir_from_uri &&
            !isset($_COOKIE[self::$cookie_name]) &&
            !$this->is_bot($_SERVER['HTTP_USER_AGENT']) &&
            $_SERVER['HTTP_CF_IPCOUNTRY'] != self::$redir_if_not_country;
    }

    function redirect_with_country() {
        if ($this->should_redirect()) {
            setcookie(self::$cookie_name, '1');
            wp_safe_redirect(self::$redir_to_uri);
            exit;
        }
    }

    function register_with_wp() {
        add_action('init', [$this, 'redirect_with_country'], 1);
    }
}

$redirect_with_cf_ipcountry = new Redirect_With_CF_IPCountry();
$redirect_with_cf_ipcountry->register_with_wp();
