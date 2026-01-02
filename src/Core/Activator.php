<?php

namespace WP_Desa\Core;

use WP_Desa\Core\Database\Migration;

class Activator {

	public static function activate() {
		// Code to execute on plugin activation (e.g. create database tables)
		Migration::migrate();
		flush_rewrite_rules();
	}

}
