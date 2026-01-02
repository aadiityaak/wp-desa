<?php

namespace WP_Desa\Core\Database;

class Migration {

	public static function migrate() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_families = $wpdb->prefix . 'desa_families';
		$table_residents = $wpdb->prefix . 'desa_residents';

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		// Table Families (Kartu Keluarga)
		$sql_families = "CREATE TABLE $table_families (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			no_kk varchar(20) NOT NULL,
			kepala_keluarga varchar(100) NOT NULL,
			alamat text NOT NULL,
			rt varchar(5) NOT NULL,
			rw varchar(5) NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			UNIQUE KEY no_kk (no_kk)
		) $charset_collate;";

		dbDelta( $sql_families );

		// Table Residents (Penduduk)
		$sql_residents = "CREATE TABLE $table_residents (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			family_id bigint(20) DEFAULT NULL,
			nik varchar(20) NOT NULL,
			nama_lengkap varchar(100) NOT NULL,
			tempat_lahir varchar(100) NOT NULL,
			tanggal_lahir date NOT NULL,
			jenis_kelamin enum('L','P') NOT NULL,
			agama varchar(50) NOT NULL,
			status_perkawinan varchar(50) NOT NULL,
			pekerjaan varchar(100) NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			UNIQUE KEY nik (nik),
			KEY family_id (family_id)
		) $charset_collate;";

		dbDelta( $sql_residents );
	}
}
