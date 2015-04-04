<?php
/*  Copyright 2015  Varun Sridharan  (email : varunsridharan23@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 
    Plugin Name: WooCommerce Attributes Menu Manager
    Plugin URI: http://varunsridharan.in/
    Description: WooCommerce Attributes Menu Manager
    Version: 0.4
    Author: Varun Sridharan
    Author URI: http://varunsridharan.in/
    License: GPL2
    GitHub Plugin URI: https://github.com/technofreaky/WooCommerce-Attributes-Menu-Manager/
*/
defined('ABSPATH') or die("No script kiddies please!"); 


class wc_attributes_menu_manager     {
    private static $db_key;
    private static $wc_amm_priority;
    private static $default_priority;
    private $attributes;
    
    
    /**
     * Construct
     */
    function __construct() {
        self::$db_key = 'wc_attribute_menu';
        self::$wc_amm_priority = 'wc_amm_priority';
        self::$default_priority = 999;
        $this->attributes = $this->get_settings();
        $this->save_settings();
        
        register_activation_hook( __FILE__, array(__CLASS__ ,'_activate') );
        add_action('admin_menu', array($this,'admin_register_menu'));
        add_filter('woocommerce_attribute_show_in_nav_menus', array($this,'register_menu'), $this->get_priority(), 2);
        add_filter( 'plugin_row_meta', array( $this, 'plugin_row_links' ), 10, 2 );
    }
    
    /**
     * Registers Menu Based On Saved Settings
     * @param   String $register  Refer WC
     * @param   String  [$name = ''] Name of the attribute
     * @returns boolean
     * Since 0.1
     */
    public function register_menu( $register, $name = '' ) { 
        if(! empty($this->attributes)){
         if (in_array($name,$this->attributes)) $register = true;
         
        }
        return $register;
    }

    
    /**
     * Runs When the Plugin Is Activated
     * Filter Use register_activation_hook
     * @Since 0.1
     * @updated 0.3
     */
    public static function _activate(){
        add_option(self::$db_key,'','', ''); 
        add_option(self::$wc_amm_priority,'99','', ''); 
    }
    
    /**
     * Register Plugin Menu
     * Filter Use admin_menu
     * Since 0.1
     */
    public function admin_register_menu(){
        add_submenu_page('edit.php?post_type=product', 'Attributes Menu Manager', 'Attributes Menu Manager', 'manage_woocommerce', 'wc-attribute-menu', array($this,'wc_attribute_menu' ));
    }
    
    /**
     * Saves Settings In DB
     * @Since 0.1
     * @updated 0.3
     */
    public function save_settings(){
        if(isset($_REQUEST['action'])){
			if($_REQUEST['action'] == 'save_wc_attribute_menu'){
				if(isset($_POST['attributes'])){
					$attributes = array_keys($_POST['attributes']);
					$attributes = serialize($attributes);	
				} else {
					$attributes = '';
				}
                
                if(isset($_POST['wc_amm_priority'])){
                    $priority = intval($_POST['wc_amm_priority']);

                    if($priority > 0){
                        $wc_amm_save_priority = $priority;
                    } else {
                        $wc_amm_save_priority = self::$default_priority;
                    }
                    
                
				} else {
					$wc_amm_save_priority = self::$default_priority;
				}
                
                update_option(self::$wc_amm_priority,$wc_amm_save_priority);
                update_option(self::$db_key,$attributes);
            }
        }
        
    }
    
    /**
     * Retrives Settings From DB
     * @since 0.1
     * @updated 0.3
     */
    private function get_settings(){
        $attributes = get_option(self::$db_key);
		if(!empty($attributes)){
        	$attributes = unserialize($attributes);
		}else {
			$attributes = '';
		}
        return $attributes;
    }
    
    /**
     * Get Plugin Priority
     * @since 0.4
     * @return int
     */
    public function get_priority(){
        $priority = get_option(self::$wc_amm_priority);
        
		if(!empty($priority)){
        	return $priority;
		} else {
			return self::$default_priority;
		}
        
    }
    
    /**
     * Show's Plugin Message
     * @since 0,1
     */
    private function show_messages(){
         if(isset($_REQUEST['action'])){
			if($_REQUEST['action'] == 'save_wc_attribute_menu'){
                echo '<div class="updated settings-error" id="setting-error-settings_updated"> 
        <p><strong>Settings saved.</strong></p></div>';
            }
         }
    }
    
    /**
     * Generates Page HTML
     * @since 0.1
     */
    public function wc_attribute_menu(){
        $wc_attr_names = wc_get_attribute_taxonomies();
         
        $saved_attrs = $this->get_settings();

        echo '<div class="wrap">
                <form method="post">
        <h2>WC Attributes Menu Manager </h2>';
        $this->show_messages();
        echo '
        <script>
        

        jQuery(document).ready(function () { jQuery("span.spinner").hide(); jQuery("#submit").click(function () { jQuery("span.spinner").show(); }); });
        </script>
        <style> .checkbox {display:inline-block;position:relative;text-align:left;width:60px;height:30px;background-color:#222;overflow:hidden;-webkit-box-shadow:inset 0 1px 2px black,0 1px 0 rgba(255,255,255,0.1);-moz-box-shadow:inset 0 1px 2px black,0 1px 0 rgba(255,255,255,0.1);box-shadow:inset 0 1px 2px black,0 1px 0 rgba(255,255,255,0.1);-webkit-border-radius:6px;-moz-border-radius:6px;border-radius:6px;}.checkbox input {display:block;position:absolute;top:0;right:0;bottom:0;left:0;width:100%;height:100%;margin:0 0;cursor:pointer;opacity:0;filter:alpha(opacity=0);z-index:2;}.checkbox label {background-color:#3c3c3c;background-image:-webkit-linear-gradient(-40deg,rgba(0,0,0,0),rgba(255,255,255,0.1),rgba(0,0,0,0.2));background-image:-moz-linear-gradient(-40deg,rgba(0,0,0,0),rgba(255,255,255,0.1),rgba(0,0,0,0.2));background-image:-ms-linear-gradient(-40deg,rgba(0,0,0,0),rgba(255,255,255,0.1),rgba(0,0,0,0.2));background-image:-o-linear-gradient(-40deg,rgba(0,0,0,0),rgba(255,255,255,0.1),rgba(0,0,0,0.2));background-image:linear-gradient(-40deg,rgba(0,0,0,0),rgba(255,255,255,0.1),rgba(0,0,0,0.2));-webkit-box-shadow:0 0 0 1px rgba(0,0,0,0.1),0 1px 2px rgba(0,0,0,0.7);-moz-box-shadow:0 0 0 1px rgba(0,0,0,0.1),0 1px 2px rgba(0,0,0,0.7);box-shadow:0 0 0 1px rgba(0,0,0,0.1),0 1px 2px rgba(0,0,0,0.7);-webkit-border-radius:5px;-moz-border-radius:5px;border-radius:5px;display:inline-block;width:40px;text-align:center;font:bold 11px/28px Arial,Sans-Serif;color:#999;text-shadow:0 -1px 0 rgba(0,0,0,0.7);-webkit-transition:margin-left 0.2s ease-in-out;-moz-transition:margin-left 0.2s ease-in-out;-ms-transition:margin-left 0.2s ease-in-out;-o-transition:margin-left 0.2s ease-in-out;transition:margin-left 0.2s ease-in-out;margin:1px;}.checkbox label:before {content:attr(data-off);}.checkbox input:checked + label {margin-left:19px;background-color:#034B78;color:white;}.checkbox input:checked + label:before {content:attr(data-on);}

.bounty-indicator-tab {
    margin-right: 0;
    line-height: 28px;
    display: inline-block;
    margin-left: -4px;
    padding: 0 4px;
    border-radius: 3px;
    color: #FFFFFF !important;
    font-size: 90%;
    font-weight: bold;
    margin-right: 5px;
}
.bounty-indicator-tab.red { background-color: #E74C3C; }
.bounty-indicator-tab.green { background-color: #519E2A; }        
        
        </style>
        ';
        ?>



<div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">
        <div id="post-body-content">
            <div class="meta-box-sortables ui-sortable">
                <table class="wp-list-table widefat fixed pages">
                    <thead>
                        <tr>
                            <th class="manage-column column-title"><a href="#"><span>Name</span></a></th>
                            <th class="manage-column column-title"><a href="#"><span>Slug</span></a></th>
                            <th class="manage-column column-title"><a href="#"><span>Visibility</span></a></th>
                            <th style="" class="manage-column column-author" id="author" scope="col">Menu Status</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="manage-column column-title"><a href="#"><span>Name</span></a></th>
                            <th class="manage-column column-title"><a href="#"><span>Slug</span></a></th>
                            <th class="manage-column column-title"><a href="#"><span>Visibility</span></a></th>
                            <th class="manage-column column-author">Menu Status</th>
                        </tr>
                    </tfoot>
                    <tbody id="the-list"> 
                        <?php 
                        if(!empty($wc_attr_names)){
                            foreach($wc_attr_names as $names){
                                $checked = '';
                                $attr_slug = wc_attribute_taxonomy_name($names->attribute_label);
                                $name = $names->attribute_label;
                                $status = '';
                                if(!empty($saved_attrs)) {
                                    if(in_array($attr_slug,$saved_attrs)) {$checked = 'checked';};
                                }

                                if($names->attribute_public == 1){
                                    $status = '<span class="bounty-indicator-tab green">Visible</span>';
                                } else if($names->attribute_public == 0) {
                                    $status = '<span class="bounty-indicator-tab red">Hidden</span>';
                                } else {
                                    $status = '<span class="bounty-indicator-tab green">Visible</span>';
                                }
                                echo '<tr class="" id="post-170">
                                            <td class="post-title page-title column-title" ><strong><a class="row-title">
                                                <label for="'.$attr_slug.'">'.$name.'</label></a></strong>
                                            </td>
                                            <td class="post-title page-title column-title" ><strong>
                                                <label for="'.$attr_slug.'">'.$attr_slug.'</label></strong>
                                            </td>
                                            <td>'.$status.' </td>
                                            <td class="">
    <span class="checkbox">
        <input type="checkbox" id="'.$attr_slug.'" name="attributes['.$attr_slug.']"  '.$checked.'>
        <label data-on="ON" data-off="OFF"></label>
    </span>

                                            </td>
                                        </tr>';
                            } 
                        } else {
                             echo '<tr class="" id="post-170">
                                            <td colspan="3" class="post-title page-title column-title" > No Attributes Created.. <a href="'.admin_url('edit.php?post_type=product&page=product_attributes').'"> Please 
                                            Create One </a>.
                                            </td> 
                                        </tr>';
                        }
            
                        ?>
                    </tbody>
                </table>
              
                <input type="hidden" name="action" value="save_wc_attribute_menu">
    <p class="submit" style="text-align:right; padding:0px 30px;"> <span class="spinner" style="display: inline-block; float:none; vertical-align:middle; margin-right:10px;"></span><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
                 <br class="clear">
                
                <div class="postbox">
                    <h3><span><?php _e( 'Create a template', '' ); ?></span></h3>
                    <div class="inside">
                        <p>You will need to theme your attribute to make it display products how you want. To do this:</p>
                        
                        <ul>    
                            <li>* Copy <strong>woocommerce/templates/taxonomy-product_cat.php</strong> into your theme folder</li>
                            <li>* Rename the template to reflect your attribute <code>taxonomy-{attribute_slug}.php</code> – in our example we’d use <strong>taxonomy-pa_size.php</strong></li>
                        </ul>
                        

                        Thats all there is to it. You will now see this template when viewing taxonomy terms for your custom attribute.
                    </div>
                </div>
            </div>
            
            
        </div>
        
        <div id="postbox-container-1" class="postbox-container">
            <div class="meta-box-sortables">
                <div class="postbox">
                    <h3><span><?php _e( 'Troubleshoot / F.A.Q', '' ); ?></span></h3>
                    <div class="inside">
                        <p> <strong> Some Attribute Not Listing In WP Menu Page ? </strong> <br/> <br/>
                            1. Check attribute Visibility if using latest WooCommerce. if hidden please enable by <strong>Enable Archives?</strong> in edit page
                            <br/><br/>
                            
                            2. Increase plugin priority If Some attribute is not showing in WP Admin Menu Page. also enable the attribute in screen option at WP Admin Menu Page</p>
                        <strong>Plugin Priority : </strong>
                        <input type="text" value="<?php echo $this->get_priority(); ?> "  name="wc_amm_priority" id="wc_amm_priority" class="small-text" />
                    </div>
                </div>
                <div class="postbox">
                    <h3><span><?php _e( 'About WC Attributes Menu Manager <small> V0.4 </small>', 'wp_admin_style' ); ?></span></h3>
                    <div class="inside">
                        <p>Show Woocommerce Custom Attributes in WordPress Menu Page. Attributes (which can be used for the layered nav) are a custom taxonomy, meaning you can display them in menus, or display products by attributes.</p>
                        
                        <ul>
                            <li><a href="https://github.com/technofreaky/WooCommerce-Attributes-Menu-Manager">View On Github</a></li>
                            <li><a href="https://wordpress.org/support/plugin/woocommerce-attributes-menu-manager">WordPress Support</a></li>
                            <li><a href="https://github.com/technofreaky/WooCommerce-Attributes-Menu-Manager/issues">Report Issue</a></li>
                            <li><a href="https://wordpress.org/support/view/plugin-reviews/woocommerce-attributes-menu-manager">Write A Review</a></li>
                            <li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=75TP8MABWJNSG">♥ Donate</a></li>
                            
                        </ul>
                        <p>&copy; Copyright 2014 - <?php echo date( 'Y' ); ?> <a href="http://varunsridharan.in/">Varun Sridharan</a></p>
                    </div>
                </div>
                

               
            </div>
        </div>
    </div>
    <br class="clear">
    
    </form>
</div>
</div>
<?php
        
        
    }
    
    /**
	 * Adds Some Plugin Options
	 * @param  array  $plugin_meta
	 * @param  string $plugin_file
	 * @since 0.11
	 * @return array
	 */
	public function plugin_row_links( $plugin_meta, $plugin_file ) {
		if ( plugin_basename( __FILE__ ) == $plugin_file ) {
            $plugin_meta[ ] = sprintf(
                ' <a href="%s">%s</a>',
                admin_url('edit.php?post_type=product&page=wc-attribute-menu'),
                'Settings'
            );
            
            $plugin_meta[ ] = sprintf(
				'<a href="%s">%s</a>',
				'https://wordpress.org/plugins/woocommerce-attributes-menu-manager/faq/',
				'F.A.Q'
			);
            $plugin_meta[ ] = sprintf(
				'<a href="%s">%s</a>',
				'https://github.com/technofreaky/WooCommerce-Attributes-Menu-Manager',
				'View On Github'
			);
            
            $plugin_meta[ ] = sprintf(
				'<a href="%s">%s</a>',
				'https://github.com/technofreaky/WooCommerce-Attributes-Menu-Manager/issues/new',
				'Report Issue'
			);
            $plugin_meta[ ] = sprintf(
				'&hearts; <a href="%s">%s</a>',
				'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=75TP8MABWJNSG',
				'Donate'
			);
		}
		return $plugin_meta;
	}	
}


/**
 * Check if WooCommerce is active 
 * if yes then call the class
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	new wc_attributes_menu_manager; 
} else {
	add_action( 'admin_notices', 'wc_attributes_menu_manager_plugin_notice' );
}

function wc_attributes_menu_manager_plugin_notice() {
	echo '<div class="error"><p><strong> <i> Woocommerce Attributes Menu Manager </i> </strong> Requires <a href="'.admin_url( 'plugin-install.php?tab=plugin-information&plugin=woocommerce').'"> <strong> <u>Woocommerce</u></strong>  </a> To Be Installed And Activated </p></div>';
} 
?>