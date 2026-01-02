<?php

namespace WP_Desa\Api\Controllers;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class ResidentController extends BaseController {

	public function __construct( $namespace ) {
		parent::__construct( $namespace );
		$this->rest_base = 'residents';
	}

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_item' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
		] );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>\d+)', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_item' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update_item' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_item' ],
				'permission_callback' => [ $this, 'permissions_check' ],
			],
		] );
	}

	public function permissions_check( $request ) {
		return current_user_can( 'manage_options' );
	}

	public function get_items( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'desa_residents';
		$results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC" );
		return rest_ensure_response( $results );
	}

	public function create_item( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'desa_residents';
		
		$params = $request->get_json_params();
		
		// Basic validation
		if ( empty( $params['nik'] ) || empty( $params['nama_lengkap'] ) ) {
			return new WP_Error( 'missing_params', 'NIK dan Nama Lengkap wajib diisi', [ 'status' => 400 ] );
		}

		$data = [
			'nik' => sanitize_text_field( $params['nik'] ),
			'nama_lengkap' => sanitize_text_field( $params['nama_lengkap'] ),
			'tempat_lahir' => sanitize_text_field( $params['tempat_lahir'] ?? '' ),
			'tanggal_lahir' => sanitize_text_field( $params['tanggal_lahir'] ?? '' ),
			'jenis_kelamin' => sanitize_text_field( $params['jenis_kelamin'] ?? 'L' ),
			'agama' => sanitize_text_field( $params['agama'] ?? '' ),
			'status_perkawinan' => sanitize_text_field( $params['status_perkawinan'] ?? '' ),
			'pekerjaan' => sanitize_text_field( $params['pekerjaan'] ?? '' ),
		];

		$inserted = $wpdb->insert( $table_name, $data );

		if ( ! $inserted ) {
			return new WP_Error( 'db_insert_error', 'Gagal menyimpan data', [ 'status' => 500 ] );
		}

		$data['id'] = $wpdb->insert_id;
		return rest_ensure_response( $data );
	}

	public function update_item( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'desa_residents';
		$id = $request['id'];

		$params = $request->get_json_params();
		$data = [];

		// Map fields to update
		$fields = ['nik', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'status_perkawinan', 'pekerjaan'];
		foreach($fields as $field) {
			if (isset($params[$field])) {
				$data[$field] = sanitize_text_field($params[$field]);
			}
		}

		if (empty($data)) {
			return new WP_Error('no_data', 'Tidak ada data untuk diupdate', ['status' => 400]);
		}

		$updated = $wpdb->update( $table_name, $data, [ 'id' => $id ] );

		if ( $updated === false ) {
			return new WP_Error( 'db_update_error', 'Gagal mengupdate data', [ 'status' => 500 ] );
		}

		return rest_ensure_response( [ 'success' => true ] );
	}

	public function delete_item( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'desa_residents';
		$id = $request['id'];

		$deleted = $wpdb->delete( $table_name, [ 'id' => $id ] );

		if ( ! $deleted ) {
			return new WP_Error( 'db_delete_error', 'Gagal menghapus data', [ 'status' => 500 ] );
		}

		return rest_ensure_response( [ 'deleted' => true ] );
	}

}
