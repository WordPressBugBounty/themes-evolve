<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * evolve: WooCommerce 4.4.0's template (still current) replaced by evolve's tabbed dashboard: the actions below print
 * the greeting, dashboard, downloads, orders, addresses and account forms (inc/woocommerce-support.php).
 * woocommerce_account_dashboard is WooCommerce's own action, so plugins that add to the dashboard keep working.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}



do_action('woocommerce_account_dashboard');

do_action('woocommerce_view_dashboard');

do_action('woocommerce_account_downloads');

do_action('woocommerce_account_orders');

do_action('woocommerce_account_address');

do_action('woocommerce_account_editaccount');

