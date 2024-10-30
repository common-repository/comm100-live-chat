<?php
/*
Plugin Name: Comm100 Live Chat & Chatbot
Plugin URI: http://www.comm100.com/livechat/wordpresschat.aspx
Description: Quickly install Comm100 Live Chat onto your WordPress site and engage your website/blog visitors in real time.
Author: Comm100
Version: 3.1
Author URI:  https://www.comm100.com/
Text Domain: comm100-live-chat
*/

// Add settings page
add_action('admin_menu', 'cjsi_add_admin_menu');
add_action('admin_init', 'cjsi_settings_init');

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'cjsi_add_action_links');

register_uninstall_hook(__FILE__, 'cjsi_uninstall');

function cjsi_uninstall() {
    delete_option('cjsi_settings');
}

function cjsi_add_action_links($links) {
    $settings_link = '<a href="options-general.php?page=comm100livechat">' . __('Settings') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

function cjsi_add_admin_menu() {
    add_options_page(
        'Comm100 Live Chat & Chatbot', // Page title
        'Comm100 Live Chat & Chatbot', // Menu title
        'manage_options',        // Capability
        'comm100livechat',       // Menu slug
        'cjsi_options_page'      // Callback function
    );
}

function cjsi_settings_init() {
    register_setting('pluginPage', 'cjsi_settings');

    add_settings_section(
        'cjsi_pluginPage_section', 
        __('Settings', 'cjsi'), 
        'cjsi_settings_section_callback', 
        'pluginPage'
    );

    add_settings_field( 
        'cjsi_textarea_field', 
        __('Live Chat code', 'cjsi'), 
        'cjsi_textarea_field_render', 
        'pluginPage', 
        'cjsi_pluginPage_section' 
    );

    add_settings_field( 
        'cjsi_select_field', 
        __('Select Pages', 'cjsi'), 
        'cjsi_select_field_render', 
        'pluginPage', 
        'cjsi_pluginPage_section' 
    );
}

function cjsi_textarea_field_render() {
    $options = get_option('cjsi_settings');
    ?>
    <textarea cols="55" rows="15" name="cjsi_settings[cjsi_textarea_field]"><?php echo isset($options['cjsi_textarea_field']) ? esc_textarea($options['cjsi_textarea_field']) : ''; ?></textarea>
    <?php
}

function cjsi_select_field_render() {
    $options = get_option('cjsi_settings');
    ?>
    <select name="cjsi_settings[cjsi_select_field]">
        <option value="all" <?php selected(isset($options['cjsi_select_field']) ? $options['cjsi_select_field'] : '', 'all'); ?>>All Pages</option>
        <option value="home" <?php selected(isset($options['cjsi_select_field']) ? $options['cjsi_select_field'] : '', 'home'); ?>>Home Page Only</option>
    </select>
    <?php
}

function cjsi_settings_section_callback() {
    echo __('Enter your Comm100 Live Chat code below and select where you want it to be applied.', 'cjsi');
}

function cjsi_options_page() {
    ?>
    <form action='options.php' method='post'>
        <h2>Comm100 Live Chat & Chatbot</h2>
        <?php
        settings_fields('pluginPage');
        do_settings_sections('pluginPage');
        submit_button();
        ?>
    </form>
    <?php
}

add_action('wp_footer', 'cjsi_insert_js');

function cjsi_insert_js() {
    $options = get_option('cjsi_settings');
    $js_code = isset($options['cjsi_textarea_field']) ? $options['cjsi_textarea_field'] : '';
    $select_pages = isset($options['cjsi_select_field']) ? $options['cjsi_select_field'] : 'all';

    if (!empty($js_code)) {
        if ($select_pages == 'all' || ($select_pages == 'home' && is_front_page())) {
            echo  $js_code;
        }
    }
}
?>
