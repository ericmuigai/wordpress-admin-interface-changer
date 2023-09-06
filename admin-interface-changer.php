<?php

/**
 * Plugin Name:       Admin Interface Changer
 * Plugin URI:        https://github.com/ericmuigai/admin-interface-changer
 * Description:       Customize Wordpress admin dashboard. Includes changing logo and color scheme change.
 * Version:           1.0.1
 * Requires PHP:      5.6
 * Text Domain:       admin-interface-changer
 * Domain Path:       /languages
 */
class AdminInterface
{
    public static $name = 'admin-interface';
    public static $name_options = 'admin_interface_changer_options';
    public static function init()
    {
        self::add_actions();
    }

    public static function add_actions()
    {
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_assets'], 999);
        add_action('admin_init', [self::class, 'register_settings']);
        add_action('login_head', [self::class, 'wordpress_custom_login_logo']);

        add_action('admin_menu', array(self::class, 'add_menu_page'));
        add_action('admin_action_admin_interface_changer_action', [self::class, 'admin_interface_changer_action']);
        add_action('admin_bar_menu', [self::class, 'add_item'], 10);
    }

    public static function add_item($admin_bar)
    {
        $menu_link = self::get_option('admin_menu_item_link');
        $menu_title = self::get_option('admin_menu_item_title');
        $admin_menu_item_new_tab = self::get_option('admin_menu_item_new_tab');
        if ($menu_title) {
            if("" === $menu_link){
                $menu_link = false;
            }
            $args = array(
                'id'    => 'home',
                'title' => $menu_title,
                'href'  => $menu_link,
                'meta'  => array(
                    'title' => __($menu_title, 'lasntgadmin'),
                ),
            );
            if($admin_menu_item_new_tab){
                $args['meta']['target'] ="__blank";
            }
            $admin_bar->add_menu($args);
        }
    }

    public static function enqueue_assets()
    {
        wp_enqueue_media();
        wp_register_script(self::$name, plugins_url('/assets/js/index.js', __FILE__), ['jquery']);

        wp_enqueue_script(self::$name);

        wp_register_style(self::$name, plugins_url('/assets/css/index.php', __FILE__));

        wp_enqueue_style(self::$name);
    }

    private static function checkboxes_style()
    {
        $checkboxes = self::get_checkboxes();
        $style = '';
        foreach ($checkboxes as $checkbox) {
            if ($checkbox['value']) {
                $style .= $checkbox['element'] . "{display:none}\n";
            }
        }
        return $style;
    }

    public static function wordpress_custom_login_logo()
    {
        $logo_url = self::get_option('logo_url');
        $logo_url = esc_url(apply_filters('generate_logo', $logo_url));

        $logo_width = self::get_option('logo_width');
        $logo_height = self::get_option('logo_height');
        $wp_logo_width   = $logo_width ?  $logo_width . "px" : '100%';
        $wp_logo_height   = $logo_height ?  $logo_height . "px" : '100%';
        echo '<style type="text/css">' .
            '.login h1 a { 
				background-image:url(' .
            $logo_url
            . ') !important; 
				height:' .
            $wp_logo_height .
            ' !important;
				width:' .
            $wp_logo_width
            . ' !important;
				background-size:100% !important;
				line-height:inherit !important;
                background-position: center;
                 pointer-events: none;
				}
				#backtoblog{
					display:none;
				}
                ' .
            '</style>';
    }

    public static function get_style()
    {
        $admin_menu_color = self::get_option('admin_menu_color');
        $admin_sidebar_submenu_color = self::get_option('admin_sidebar_submenu_color');
        $admin_text_color = self::get_option('admin_text_color');
        $admin_hover_color = self::get_option('admin_hover_color');
        $admin_hover_text_color = self::get_option('admin_hover_text_color');
        $logo_url = self::get_option('logo_url');

        $style = ".aic-hide{display: none;}\n";
        $style .= self::checkboxes_style();
        if ($logo_url) {
            $style .= ".login h1 a{background-image: url($logo_url)}\n";
        }
        if ($admin_menu_color) {
            $style .= "#adminmenu, #wpadminbar, #adminmenuback, #adminmenuwrap, #adminmenu{\n"
                . "background-color:  #$admin_menu_color !important;\n"
                . "}\n";
        }
        if ($admin_sidebar_submenu_color) {
            $style .= "#adminmenu li.wp-has-submenu.wp-not-current-submenu.opensub:hover:after, #adminmenu li.wp-has-submenu.wp-not-current-submenu:focus-within:after\n"
                . "{"
                . "\n   border-right-color: #$admin_sidebar_submenu_color !important;\n"
                . "}";
            $style .= "
            
            #wp-admin-bar-wp-logo-external,
            #wpadminbar .menupop .ab-sub-wrapper, #wpadminbar .shortlink-input,
            #adminmenu .wp-submenu .wp-submenu-head{
                background-color: #$admin_sidebar_submenu_color !important;
            }";
        }

        if ($admin_hover_color) {
            $style .= "
            .current .menu-top,
            .ab-item:focus,
            .ab-item:hover,
            .menupop hover,
            #wpadminbar .ab-icon:hover,
            #wpadminbar .ab-top-menu>li.hover>.ab-item,
            .ab-item:hover,
            #adminmenu li.menu-top:hover, #adminmenu li.opensub>a.menu-top, #adminmenu li>a.menu-top:focus
           {
               background-color: #$admin_hover_color !important;
            }";
        }
        if ($admin_hover_text_color) {
            $style .= "
            .current .menu-top > .wp-menu-image:before,
            #wp-admin-bar-wp-logo-external > a:focus,
            #wp-admin-bar-root-default> a:focus,
            .wp-menu-name:focus,
            .wp-menu-name:hover,
            .current > .wp-menu-name:focus,
            #wpadminbar .quicklinks .menupop ul li a:hover,
            .current > .wp-menu-name,
            #adminmenu li a:focus div.wp-menu-image:before, #adminmenu li.opensub div.wp-menu-image:before, #adminmenu li:hover div.wp-menu-image:before,
            #adminmenu .wp-submenu a:focus, #adminmenu .wp-submenu a:hover, #adminmenu a:hover, #adminmenu li.menu-top>a:focus{
                color: #$admin_hover_text_color !important;
            }";
        }

        if ($admin_text_color) {
            $style .= "
            #wpadminbar .ab-empty-item, 
            a.ab-item,
            #wpadminbar a.ab-item:before,
            #wpadminbar span.ab-item:before,
            #wpadminbar .ab-icon:before,
            #wpadminbar .ab-icon,
             #wpadminbar>#wp-toolbar span.ab-label,
            #wpadminbar>#wp-toolbar span.noticon,

            #wpadminbar .quicklinks .menupop ul li a,
            .wp-menu-name,
            #wpadminbar, #wp-admin-bar-root-default,
            .wp-submenu, .menu-top,
            #adminmenu .wp-submenu a,
            #adminmenu .wp-submenu .wp-submenu-head,
            #adminmenu div.wp-menu-image:before,
            #wpadminbar .ab-empty-item, #wpadminbar a.ab-item, #wpadminbar>#wp-toolbar span.ab-label, #wpadminbar>#wp-toolbar span.noticon, #adminmenu .wp-submenu , 
            #adminmenu div.wp-menu-name{
                color: #$admin_text_color ;
            }";
        }
        if ($admin_sidebar_submenu_color) {
            $style .= "#adminmenu .wp-submenu{
                   background-color: #$admin_sidebar_submenu_color !important;
                }
                ";
        }

        return $style;
    }
    public static function add_menu_page()
    {
        add_menu_page('Admin Interface', 'Admin Interface', 'administrator', 'admin-interface', array(self::class, 'show_form'), 'dashicons-edit', 70);
    }

    public static function get_option($name)
    {
        $options = get_option(self::$name_options);
        return $options && isset($options[$name]) ? $options[$name] : "";
    }
    public static function register_settings()
    {
        register_setting(self::$name_options, self::$name_options);
    }

    public static function get_checkboxes()
    {
        return [
            "site_name"            => [
                "value" => self::get_option('site_name'),
                "element" => "#wp-admin-bar-site-name"
            ],
            "comments"            => [
                "value" => self::get_option('comments'),
                "element" => "#wp-admin-bar-comments"
            ],
            "new_content"         => [
                "value" => self::get_option('new_content'),
                "element" => "#wp-admin-bar-new-content"
            ],
            "new_post"            => [
                "value" => self::get_option('new_post'),
                "element" => "#wp-admin-bar-new-post"
            ],
            "new_media"           => [
                "value" => self::get_option('new_media'),
                "element" => "#wp-admin-bar-new-media"
            ],
            "new_page"            => [
                "value" => self::get_option('new_page'),
                "element" => "#wp-admin-bar-new-page"
            ],
            "new_product"         => [
                "value" => self::get_option('new_product'),
                "element" => "#wp-admin-bar-new-product"
            ],
            "new_order"           => [
                "value" => self::get_option('new_order'),
                "element" => "#wp-admin-bar-new-shop_order"
            ],
            "new_coupon"          => [
                "value" => self::get_option('new_coupon'),
                "element" => "#wp-admin-bar-new-shop_coupon"
            ],
            "new_user"            => [
                "value" => self::get_option('new_user'),
                "element" => "#wp-admin-bar-new-user"
            ],
            "update_actions"      => [
                "value" => self::get_option('update_actions'),
                "element" => "#wp-admin-bar-updates"
            ],
            "wordpress_menu_logo" => [
                "value" => self::get_option('wordpress_menu_logo'),
                "element" => "#wp-admin-bar-wp-logo"
            ],
        ];
    }

    public static function get_name($name)
    {
        $name = str_replace("_", " ", $name);
        return ucwords($name);
    }
    public static function show_form()
    {
        if (!current_user_can("administrator")) {
            return;
        }
        $checkboxes = self::get_checkboxes();

        $logo_url = self::get_option('logo_url');
        $option_name = self::$name_options;

?>
        <h3>Admin Interface Changer</h3>
        <form method="POST" action="options.php">
            <?php settings_fields(self::$name_options); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Logo', 'admin-interface-changer'); ?></th>
                    <td>

                        <img src="<?= esc_url($logo_url); ?>" id="logoImage" height="200px" width="200px" class="<?= !$logo_url ? "aic-hide" : "" ?>" /><?= $logo_url ? "<br/>" : "" ?>

                        <input type="hidden" id="logo_url" name="<?= $option_name ?>[logo_url]" value="<?php if (isset($logo_url)) {
                                                                                                            echo esc_url($logo_url);
                                                                                                        } ?>" />
                        <input type="button" name="fateh-upload-btn" id="fateh-upload-btn" value="Choose Logo Image">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Logo Width X Height', 'admin-interface-changer'); ?></th>
                    <td>

                        <input type="number" id="logo_width" name="<?= $option_name ?>[logo_width]" value="<?= self::get_option('logo_width') ?>" /> X
                        <input type="number" id="logo_height" name="<?= $option_name ?>[logo_height]" value="<?= self::get_option('logo_height') ?>" />

                    </td>
                </tr>

                <tr valign="top">
                    <td colspan="2">
                        <h3>Colors</h3>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Admin menu background Color', 'admin-interface-changer'); ?></th>
                    <td>
                        <input type="text" id="admin_menu_color" name="<?= $option_name ?>[admin_menu_color]" value="<?= self::get_option('admin_menu_color') ?>" />
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Admin Sidebar submenu Color', 'admin-interface-changer'); ?></th>
                    <td>
                        <input type="text" id="admin_sidebar_submenu_color" name="<?= $option_name ?>[admin_sidebar_submenu_color]" value="<?= self::get_option('admin_sidebar_submenu_color') ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Admin menu text color', 'admin-interface-changer'); ?></th>
                    <td>
                        <input type="text" id="admin_text_color" name="<?= $option_name ?>[admin_text_color]" value="<?= self::get_option('admin_text_color') ?>" />
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Admin menu hover color', 'admin-interface-changer'); ?></th>
                    <td>
                        <input type="text" id="admin_hover_color" name="<?= $option_name ?>[admin_hover_color]" value="<?= self::get_option('admin_hover_color') ?>" />
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Admin menu hover text color', 'admin-interface-changer'); ?></th>
                    <td>
                        <input type="text" id="admin_hover_text_color" name="<?= $option_name ?>[admin_hover_text_color]" value="<?= self::get_option('admin_hover_text_color') ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Admin menu hover text color', 'admin-interface-changer'); ?></th>
                    <td>
                        <input type="text" id="admin_hover_text_color" name="<?= $option_name ?>[admin_hover_text_color]" value="<?= self::get_option('admin_hover_text_color') ?>" />
                    </td>
                </tr>



                <tr valign="top">
                    <td colspan="2">
                        <h3>Top Bar menus</h3>
                    </td>
                </tr>
                <?php foreach ($checkboxes as $name => $value) : ?>
                    <tr valign="top">
                        <th scope="row"><?= __(self::get_name($name), 'admin-interface-changer'); ?></th>
                        <td>
                            <input type="checkbox" name="<?= $option_name ?>[<?= "$name" ?>]" <?= $value['value'] !== "" ? "checked" : "" ?> />
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr valign="top">
                    <td colspan="2">
                        <h3>Add menu item</h3>
                    </td>
                </tr>
                <tr valign="top">
                    <td colspan="2">
                        <table>

                            <tr>
                                <th scope="row"><?php _e('Menu Item Title', 'admin-interface-changer'); ?></th>
                                <td>
                                    <input type="text" id="admin_menu_item_title" name="<?= $option_name ?>[admin_menu_item_title]" value="<?= self::get_option('admin_menu_item_title') ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Menu Item Link', 'admin-interface-changer'); ?></th>
                                <td>
                                    <input type="text" id="admin_menu_item_link" name="<?= $option_name ?>[admin_menu_item_link]" value="<?= self::get_option('admin_menu_item_link') ?>" />
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row"><?php _e('Menu Item Open New Tab?', 'admin-interface-changer'); ?></th>
                                <td>
                                    <input type="checkbox" id="admin_menu_item_new_tab" name="<?= $option_name ?>[admin_menu_item_new_tab]"  <?= self::get_option('admin_menu_item_new_tab') !== "" ? "checked" : "" ?> />
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>
            <input type="submit" value="Save" class="button" />
        </form>
<?php
    }
}

AdminInterface::init();
