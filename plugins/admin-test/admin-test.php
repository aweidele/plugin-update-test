<?php
/**
 * Plugin Name:       Admin Test
 * Description:       Testing the admin
 * Version:           0.1.0
 * Requires at least: 6.8
 * Requires PHP:      7.4
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       admin-test
 */


add_action('admin_menu', function() {

  add_menu_page(
    'Hello there!',
    'Howdy',
    'manage_options',
    'admin-test',
    'render_my_plugin_page',
    'dashicons-admin-generic'
  );

});

function render_my_plugin_page() {
?>
  <div class="wrap">
    <h1>Hello!</h1>
  </div>
<?php } ?>