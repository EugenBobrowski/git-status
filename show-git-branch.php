<?php
/*
 * Plugin Name: Git Status
 * Version: 1.0
 * Plugin URI: https://github.com/
 * Description: Add a git status indicator to the toolbar.
 * Author: eugenbobrowski
 * Author URI: https://github.com/
 * Requires at least: 3.8
 * Tested up to: 4.5.2
 *
 * Text Domain: 
 * Domain Path:
 *
 */

if (!defined('ABSPATH')) exit;

class Git_Status {
    protected static $instance;
    public $paths;

    private function __construct()
    {

        add_action('admin_bar_menu', array($this, 'add_git_node'), 999);
        add_action('admin_print_styles', array($this, 'none_styles'));

        $this->paths = apply_filters('git_paths', array(
            'root' => ABSPATH,
            'theme' => get_template_directory(),
            'plugins' => PLUGINDIR,
        ));

    }
    public static function get_instance()
    {

        if (defined( 'DOING_AJAX' ) && DOING_AJAX) return false;

        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function search_submodules () {

    }


    public function add_git_node($wp_admin_bar)
    {

        //get_home_path requires following file.. may mess up if wp is in another directory?
        require_once(ABSPATH . 'wp-admin/includes/file.php');

        //get root path from WP dir
        $root_path = get_home_path() . '.git/HEAD';

        //get root path from active WP Theme, even if child theme
        $theme_path = get_stylesheet_directory() . '/.git/HEAD';


        //get branch name
        if (file_exists($theme_path)) {
            $stringfromfile = file($theme_path);
            $branchname = implode('/', array_slice(explode('/', file_get_contents($theme_path)), 2));
        } elseif (file_exists($root_path)) {
            $stringfromfile = file($root_path);
            $branchname = implode('/', array_slice(explode('/', file_get_contents($root_path)), 2));
        } else {
            $branchname = "No git detected";
        }


        $args = array(
            "id" => "git-status",
            "title" => "$branchname"
        );
        $wp_admin_bar->add_node($args);
    }
    public function none_styles()
    {
        ?><style>
        #wp-admin-bar-git-status .ab-item:before {
            content: "\f503";
            top:2px;
        }
    </style><?php
    }
}


Git_Status::get_instance();
