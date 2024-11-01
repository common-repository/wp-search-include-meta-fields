<?php
/**
 * Plugin Name: WP Search Include Meta Fields
 * Plugin URI: https://eastsidecode.com
 * Description: WordPress plugin to extend default search to include meta fields
 * Version: 1.0
 * Author: Louis Fico
 * Author URI: http://eastsidecode.com
 * License: GPL12
 */

if ( ! defined( 'ABSPATH' ) ) exit;


// Join post and post meta tables

function escode_search_join_tables( $join ) {
    global $wpdb;

    // only if on search page
    if ( is_search() ) {    
        $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }

    return $join;
}
add_filter('posts_join', 'escode_search_join_tables' );


// use preg_replace to modify the where to include post meta

function escode_search_where_edit( $where ) {
    global $pagenow, $wpdb;

    // only on search page
    if ( is_search() ) {
        $where = preg_replace(
            "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
    }

    return $where;
}
add_filter( 'posts_where', 'escode_search_where_edit' );


// use post_distinct filter to eliminate duplicates

function escode_search_distinct_only( $where ) {
    global $wpdb;

    //only on search
    if ( is_search() ) {
        return "DISTINCT";
    }

    return $where;
}
add_filter( 'posts_distinct', 'escode_search_distinct_only' );