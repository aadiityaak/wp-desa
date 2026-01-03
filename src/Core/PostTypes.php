<?php

namespace WpDesa\Core;

class PostTypes
{
    public function register()
    {
        add_action('init', [$this, 'register_potensi_desa']);
    }

    public function register_potensi_desa()
    {
        // Register Taxonomy
        $labels_cat = [
            'name'              => 'Kategori Potensi',
            'singular_name'     => 'Kategori Potensi',
            'search_items'      => 'Cari Kategori',
            'all_items'         => 'Semua Kategori',
            'parent_item'       => 'Induk Kategori',
            'parent_item_colon' => 'Induk Kategori:',
            'edit_item'         => 'Edit Kategori',
            'update_item'       => 'Update Kategori',
            'add_new_item'      => 'Tambah Kategori Baru',
            'new_item_name'     => 'Nama Kategori Baru',
            'menu_name'         => 'Kategori',
        ];

        register_taxonomy('desa_potensi_cat', ['desa_potensi'], [
            'hierarchical'      => true,
            'labels'            => $labels_cat,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'kategori-potensi'],
            'show_in_rest'      => true,
        ]);

        // Register Post Type
        $labels = [
            'name'                  => 'Potensi Desa',
            'singular_name'         => 'Potensi Desa',
            'menu_name'             => 'Potensi Desa',
            'name_admin_bar'        => 'Potensi Desa',
            'add_new'               => 'Tambah Baru',
            'add_new_item'          => 'Tambah Potensi Baru',
            'new_item'              => 'Potensi Baru',
            'edit_item'             => 'Edit Potensi',
            'view_item'             => 'Lihat Potensi',
            'all_items'             => 'Semua Potensi',
            'search_items'          => 'Cari Potensi',
            'parent_item_colon'     => 'Induk Potensi:',
            'not_found'             => 'Tidak ditemukan potensi.',
            'not_found_in_trash'    => 'Tidak ditemukan di tempat sampah.',
            'featured_image'        => 'Gambar Utama',
            'set_featured_image'    => 'Atur gambar utama',
            'remove_featured_image' => 'Hapus gambar utama',
            'use_featured_image'    => 'Gunakan sebagai gambar utama',
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'potensi-desa'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-chart-pie',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
            'show_in_rest'       => true,
        ];

        register_post_type('desa_potensi', $args);
    }
}
