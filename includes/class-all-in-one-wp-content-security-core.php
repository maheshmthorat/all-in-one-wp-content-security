<?php
/**
 * Class All in one WP Content Protector
 * The file that defines the core plugin class
 *
 * @author Mahesh Thorat
 * @link https://maheshthorat.web.app
 * @version 0.1
 * @package All_in_one_WP_Content_Security
*/
class All_In_One_WP_Content_Security_Core
{
   /**
    * The unique identifier of this plugin
   */
   protected $plugin_name;

   /**
    * The current version of the plugin
   */
   protected $version;

   /**
    * Define the core functionality of the plugin
   */
   public function __construct()
   {
      $this->plugin_name = AOWPCS_PLUGIN_IDENTIFIER;
      $this->version = AOWPCS_PLUGIN_VERSION;
   }
   public function run()
   {
      /**
       * The admin of plugin class 
       * admin related content and options
      */
      require AOWPCS_PLUGIN_ABS_PATH.'admin/class-all-in-one-wp-content-security-admin.php';

      $plugin_admin = new All_In_One_WP_Content_Security_Admin($this->get_plugin_name(), $this->get_version());
      if(is_admin())
      {
         add_action('admin_enqueue_scripts', array($plugin_admin, 'enqueue_backend_standalone'));
         add_action('admin_menu', array($plugin_admin, 'return_admin_menu'));
         add_action('init', array($plugin_admin, 'return_update_options'));
         add_filter( 'plugin_action_links_all-in-one-wp-content-security/all-in-one-wp-content-security.php', array($plugin_admin, 'aowpcs_settings_link'));
      }

      $opts = get_option('_all_in_one_wp_content_security');
      if(@$opts['block_right_clicking'] == 'on')
      {
         if(!is_admin())
         {
            if(@$opts['block_selection'] == 'on')
            {
               add_action( 'wp_head', array($plugin_admin, 'call_action_block_selection') );
            }
            if(@$opts['block_image_dragging'] == 'on')
            {
               add_action( 'wp_head', array($plugin_admin, 'call_action_block_image_dragging') );
            }
            if(@$opts['block_right_clicking'] == 'on')
            {
               add_action( 'wp_head', array($plugin_admin, 'call_action_block_right_clicking') );
            }
            if(@$opts['block_hacking_website'] == 'on')
            {
               add_action( 'wp_head', array($plugin_admin, 'call_action_block_hacking_website') );
            }
         }
      }      
   }

   public function get_plugin_name()
   {
      return $this->plugin_name;
   }
   public function get_version()
   {
      return $this->version;
   }
}
?>