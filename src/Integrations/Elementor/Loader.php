<?php

namespace WpDesa\Integrations\Elementor;

class Loader
{
    public function load()
    {
        // Check if Elementor is active
        if (did_action('elementor/loaded')) {
            add_action('elementor/widgets/register', [$this, 'register_widgets']);
        }
    }

    public function register_widgets($widgets_manager)
    {
        require_once __DIR__ . '/Widgets/WpDesaFeatureWidget.php';
        $widgets_manager->register(new Widgets\WpDesaFeatureWidget());
    }
}
