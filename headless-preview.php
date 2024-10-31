<?php
/*
Plugin Name: Headless Preview
Description: Custom plugin for previewing posts on a headless CMS.
Author: Ardie Wen <ardie.wen@hunterlily.co>
Version: 0.1
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Main class for the Headless Preview plugin
class HeadlessPreview {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_options_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_filter('preview_post_link', [$this, 'modify_preview_link'], 10, 2);
    }

    // Adds an options page under the "Settings" menu in WordPress
    public function add_options_page() {
        add_options_page(
            'Headless Preview Settings',
            'Headless Preview',
            'manage_options',
            'headless-preview',
            [$this, 'render_options_page']
        );
    }

    // Registers the settings for storing the preview URL
    public function register_settings() {
        register_setting('headless_preview', 'headless_preview_url', [
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'default' => ''
        ]);
    }

    // Renders the options page in the admin area
    public function render_options_page() {
        ?>
        <div class="wrap">
            <h1>Headless Preview Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('headless_preview');
                do_settings_sections('headless_preview');
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="headless_preview_url">Preview URL</label></th>
                        <td>
                            <input type="url" id="headless_preview_url" name="headless_preview_url" value="<?php echo esc_attr(get_option('headless_preview_url')); ?>" class="regular-text" required />
                            <p class="description">Enter the URL of the headless CMS for previewing posts.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    // Modifies the preview post link based on the specified preview URL
    public function modify_preview_link($link, $post) {
        $preview_url = get_option('headless_preview_url');

        // If no preview URL is set, return the original link
        if (empty($preview_url)) {
            return $link;
        }

        // Otherwise, return the custom preview URL with the post ID
        return trailingslashit($preview_url) . $post->ID;
    }
}

// Initialize the plugin
new HeadlessPreview();