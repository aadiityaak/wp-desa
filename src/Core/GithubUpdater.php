<?php

namespace WpDesa\Core;

class GithubUpdater
{
    private $slug;
    private $plugin_data;
    private $username;
    private $repo;
    private $plugin_file;
    private $github_response;

    public function __construct($plugin_file, $username, $repo)
    {
        $this->plugin_file = $plugin_file;
        $this->username = $username;
        $this->repo = $repo;
        $this->slug = dirname(plugin_basename($plugin_file));
    }

    public function init()
    {
        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_update']);
        add_filter('plugins_api', [$this, 'plugin_popup'], 10, 3);
        add_filter('upgrader_post_install', [$this, 'after_install'], 10, 3);
    }

    private function get_repository_info()
    {
        if (is_null($this->github_response)) {
            $url = "https://api.github.com/repos/{$this->username}/{$this->repo}/releases/latest";
            
            $response = wp_remote_get($url, [
                'headers' => [
                    'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url()
                ]
            ]);

            if (is_wp_error($response)) {
                return false;
            }

            $this->github_response = json_decode(wp_remote_retrieve_body($response));
        }

        return $this->github_response;
    }

    public function check_update($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $release = $this->get_repository_info();
        
        if (!$release) {
            return $transient;
        }

        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $this->plugin_data = get_plugin_data($this->plugin_file);
        $current_version = $this->plugin_data['Version'];
        
        // Remove 'v' from tag name if exists
        $new_version = isset($release->tag_name) ? ltrim($release->tag_name, 'v') : '';

        if (version_compare($current_version, $new_version, '<')) {
            $plugin = [
                'slug' => $this->slug,
                'plugin' => plugin_basename($this->plugin_file),
                'new_version' => $new_version,
                'url' => $release->html_url,
                'package' => $release->assets[0]->browser_download_url // Assumes first asset is the zip
            ];

            $transient->response[plugin_basename($this->plugin_file)] = (object) $plugin;
        }

        return $transient;
    }

    public function plugin_popup($result, $action, $args)
    {
        if ($action !== 'plugin_information') {
            return $result;
        }

        if ($args->slug !== $this->slug) {
            return $result;
        }

        $release = $this->get_repository_info();

        if (!$release) {
            return $result;
        }

        $plugin = [
            'name' => $this->plugin_data['Name'],
            'slug' => $this->slug,
            'version' => ltrim($release->tag_name, 'v'),
            'author' => $this->plugin_data['AuthorName'],
            'homepage' => $release->html_url,
            'requires' => '5.6', // Optional: Set requirement
            'tested' => get_bloginfo('version'), // Optional
            'download_link' => $release->assets[0]->browser_download_url,
            'sections' => [
                'description' => $this->plugin_data['Description'],
                'changelog' => $release->body
            ]
        ];

        return (object) $plugin;
    }

    public function after_install($response, $hook_extra, $result)
    {
        global $wp_filesystem;

        $install_directory = plugin_dir_path($this->plugin_file);
        $wp_filesystem->move($result['destination'], $install_directory);
        $result['destination'] = $install_directory;

        return $result;
    }
}
