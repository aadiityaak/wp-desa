<?php

namespace WP_Desa\Admin;

class Menu {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function add_plugin_admin_menu() {
		add_menu_page(
			'WP Desa', 
			'WP Desa', 
			'manage_options', 
			'wp-desa', 
			array( $this, 'display_plugin_setup_page' ),
			'dashicons-building', 
			6
		);

		add_submenu_page(
			'wp-desa',
			'Kependudukan',
			'Kependudukan',
			'manage_options',
			'wp-desa-residents',
			array( $this, 'display_residents_page' )
		);
	}

	public function display_plugin_setup_page() {
		echo '<h1>WP Desa Dashboard</h1><p>Selamat datang di sistem manajemen desa.</p>';
	}

	public function display_residents_page() {
		include_once WP_DESA_PATH . 'src/Admin/Views/residents.php';
	}

}
