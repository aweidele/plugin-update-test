<?php
/**
 * Plugin Name: Borb's Options
 * Description: A simple options page scaffold (plus self-hosted updates via info.json).
 * Version: 1.1.1
 * Author: Your Name
 * Text Domain: borbs-options
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Borbs_Options_Plugin {
	const VERSION = '1.0.0';
	const OPTION_GROUP = 'borbs_options_group';
	const OPTION_NAME  = 'borbs_options';

	/**
	 * Your hosted update metadata URL (info.json).
	 * Example: https://updates.example.com/borbs-options/info.json
	 */
	const UPDATE_JSON_URL = 'https://angrychickens.com/plugin-test/borbs-options/info.json';

	public static function init(): void {
		add_action( 'plugins_loaded', [ __CLASS__, 'bootstrap_updates' ] );
		add_action( 'admin_menu', [ __CLASS__, 'register_menu' ] );
		add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
	}

	/**
	 * Enable WordPress dashboard updates using "plugin-update-checker".
	 *
	 * Install via Composer:
	 *   composer require yahnis-elsts/plugin-update-checker
	 *
	 * Ensure your release ZIP includes the vendor/ directory (or bundle the library another way).
	 */
	public static function bootstrap_updates(): void {
		$autoload = __DIR__ . '/vendor/autoload.php';
		if ( ! file_exists( $autoload ) ) {
			return;
		}

		require_once $autoload;

		if ( ! class_exists( '\YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
			return;
		}

		$updateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
			self::UPDATE_JSON_URL,
			__FILE__,
			'borbs-options'
		);
	}
	
	public static function register_menu(): void {
		add_options_page(
			"Borb's Options",
			"Borb's Options",
			'manage_options',
			'borbs-options',
			[ __CLASS__, 'render_page' ]
		);
	}

	public static function register_settings(): void {
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_NAME,
			[
				'type'              => 'array',
				'sanitize_callback' => [ __CLASS__, 'sanitize_options' ],
				'default'           => [
					'favorite_borb' => '',
					'enabled'       => 0,
				],
			]
		);

		add_settings_section(
			'borbs_options_main',
			'Settings',
			function () {
				echo '<p>Basic settings scaffold for Borb\'s Options.</p>';
			},
			'borbs-options'
		);

		add_settings_field(
			'favorite_borb',
			'Favorite Borb',
			[ __CLASS__, 'field_favorite_borb' ],
			'borbs-options',
			'borbs_options_main'
		);

		add_settings_field(
			'enabled',
			'Enable Feature',
			[ __CLASS__, 'field_enabled' ],
			'borbs-options',
			'borbs_options_main'
		);
	}

	public static function sanitize_options( $raw ): array {
		$raw = is_array( $raw ) ? $raw : [];

		return [
			'favorite_borb' => isset( $raw['favorite_borb'] ) ? sanitize_text_field( $raw['favorite_borb'] ) : '',
			'enabled'       => ! empty( $raw['enabled'] ) ? 1 : 0,
		];
	}

	public static function get_options(): array {
		$defaults = [
			'favorite_borb' => '',
			'enabled'       => 0,
		];

		$opts = get_option( self::OPTION_NAME, [] );
		return wp_parse_args( is_array( $opts ) ? $opts : [], $defaults );
	}

	public static function field_favorite_borb(): void {
		$opts = self::get_options();
		printf(
			'<input type="text" class="regular-text" name="%s[favorite_borb]" value="%s" />',
			esc_attr( self::OPTION_NAME ),
			esc_attr( $opts['favorite_borb'] )
		);
		echo '<p class="description">Any string you want.</p>';
	}

	public static function field_enabled(): void {
		$opts = self::get_options();
		printf(
			'<label><input type="checkbox" name="%s[enabled]" value="1" %s /> Enabled</label>',
			esc_attr( self::OPTION_NAME ),
			checked( 1, (int) $opts['enabled'], false )
		);
	}

	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		echo '<div class="wrap">';
		echo '<h1>' . esc_html( "Borb's Options: You have upgraded to 1.1.1!!" ) . '</h1>';
		echo '<form method="post" action="options.php">';

		settings_fields( self::OPTION_GROUP );
		do_settings_sections( 'borbs-options' );
		submit_button();

		echo '</form>';
		echo '</div>';
	}
}

Borbs_Options_Plugin::init();