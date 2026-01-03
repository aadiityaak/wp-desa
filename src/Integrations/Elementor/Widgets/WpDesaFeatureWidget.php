<?php

namespace WpDesa\Integrations\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WpDesaFeatureWidget extends Widget_Base
{
    public function get_name()
    {
        return 'wp_desa_feature';
    }

    public function get_title()
    {
        return 'Fitur WP Desa';
    }

    public function get_icon()
    {
        return 'eicon-apps';
    }

    public function get_categories()
    {
        return ['general'];
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'section_content',
            [
                'label' => 'Pengaturan Fitur',
            ]
        );

        $this->add_control(
            'feature_type',
            [
                'label' => 'Jenis Fitur',
                'type' => Controls_Manager::SELECT,
                'default' => 'profil',
                'options' => [
                    'profil'      => 'Profil Desa',
                    'kepala_desa' => 'Kepala Desa',
                    'statistik'   => 'Statistik Penduduk',
                    'umkm'        => 'UMKM Desa',
                    'potensi'     => 'Potensi Desa',
                    'layanan'     => 'Layanan Surat',
                    'aduan'       => 'Aspirasi & Pengaduan',
                    'keuangan'    => 'Keuangan Desa',
                    'bantuan'     => 'Program Bantuan',
                ],
            ]
        );

        $this->add_control(
            'umkm_limit',
            [
                'label' => 'Jumlah UMKM',
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 20,
                'step' => 1,
                'default' => 6,
                'condition' => [
                    'feature_type' => 'umkm',
                ],
            ]
        );

        $this->add_control(
            'umkm_cols',
            [
                'label' => 'Kolom Grid',
                'type' => Controls_Manager::SELECT,
                'default' => 3,
                'options' => [
                    2 => '2 Kolom',
                    3 => '3 Kolom',
                    4 => '4 Kolom',
                ],
                'condition' => [
                    'feature_type' => 'umkm',
                ],
            ]
        );

        $this->add_control(
            'potensi_limit',
            [
                'label' => 'Jumlah Potensi',
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 20,
                'step' => 1,
                'default' => 3,
                'condition' => [
                    'feature_type' => 'potensi',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $feature_type = $settings['feature_type'];

        switch ($feature_type) {
            case 'profil':
                echo do_shortcode('[wp_desa_profil]');
                break;
            case 'kepala_desa':
                echo do_shortcode('[wp_desa_kepala_desa]');
                break;
            case 'statistik':
                echo do_shortcode('[wp_desa_statistik]');
                break;
            case 'umkm':
                $limit = $settings['umkm_limit'];
                $cols = $settings['umkm_cols'];
                echo do_shortcode("[wp_desa_umkm limit='{$limit}' cols='{$cols}']");
                break;
            case 'potensi':
                $limit = $settings['potensi_limit'];
                echo do_shortcode("[wp_desa_potensi limit='{$limit}']");
                break;
            case 'layanan':
                echo do_shortcode('[wp_desa_layanan]');
                break;
            case 'aduan':
                echo do_shortcode('[wp_desa_aduan]');
                break;
            case 'keuangan':
                echo do_shortcode('[wp_desa_keuangan]');
                break;
            case 'bantuan':
                echo do_shortcode('[wp_desa_bantuan]');
                break;
            default:
                echo 'Fitur tidak ditemukan.';
        }
    }
}
