<?php // uninstall remove options

if(!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) exit();

// delete options
delete_option('usp_options');
delete_option('user-submitted-posts-dismiss-notice');