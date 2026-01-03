<?php

namespace WpDesa\Admin;

class MetaBoxes
{
    public function register()
    {
        add_action('add_meta_boxes', [$this, 'add_umkm_meta_boxes']);
        add_action('save_post', [$this, 'save_umkm_meta_boxes']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function enqueue_scripts($hook)
    {
        global $post;
        if (($hook == 'post-new.php' || $hook == 'post.php') && 'desa_umkm' === $post->post_type) {
            wp_enqueue_media();
            wp_enqueue_script('desa-umkm-gallery', WP_DESA_URL . 'assets/js/admin/umkm-gallery.js', ['jquery'], '1.0.0', true);
        }
    }

    public function add_umkm_meta_boxes()
    {
        add_meta_box(
            'desa_umkm_details',
            'Detail UMKM',
            [$this, 'render_umkm_meta_box'],
            'desa_umkm',
            'normal',
            'high'
        );
    }

    public function render_umkm_meta_box($post)
    {
        // Get existing values
        $phone = get_post_meta($post->ID, '_desa_umkm_phone', true);
        $location = get_post_meta($post->ID, '_desa_umkm_location', true);
        $gallery_ids = get_post_meta($post->ID, '_desa_umkm_gallery', true);

        wp_nonce_field('save_desa_umkm_meta', 'desa_umkm_meta_nonce');
?>
        <table class="form-table">
            <tr>
                <th><label for="desa_umkm_phone">Nomor WhatsApp</label></th>
                <td>
                    <input type="text" id="desa_umkm_phone" name="desa_umkm_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text">
                    <p class="description">Contoh: 628123456789 (Gunakan format internasional tanpa +)</p>
                </td>
            </tr>
            <tr>
                <th><label for="desa_umkm_location">Lokasi (Koordinat)</label></th>
                <td>
                    <input type="text" id="desa_umkm_location" name="desa_umkm_location" value="<?php echo esc_attr($location); ?>" class="regular-text" placeholder="-7.123456, 110.123456">
                    <p class="description">Format: Latitude, Longitude. Bisa diambil dari Google Maps.</p>
                </td>
            </tr>
            <tr>
                <th><label>Katalog Produk (Gallery)</label></th>
                <td>
                    <div id="desa_umkm_gallery_container" style="margin-bottom: 10px;">
                        <?php
                        if ($gallery_ids) {
                            $ids = explode(',', $gallery_ids);
                            foreach ($ids as $id) {
                                $url = wp_get_attachment_thumb_url($id);
                                if ($url) {
                                    echo '<div class="gallery-item" data-id="' . $id . '" style="display: inline-block; margin: 5px; position: relative;">
                                            <img src="' . $url . '" style="width: 100px; height: 100px; object-fit: cover; border: 1px solid #ccc;">
                                            <button class="remove-image" style="position: absolute; top: 0; right: 0; background: red; color: white; border: none; cursor: pointer; padding: 2px 5px;">&times;</button>
                                          </div>';
                                }
                            }
                        }
                        ?>
                    </div>
                    <button id="desa_umkm_add_gallery" class="button">Tambah Gambar</button>
                    <input type="hidden" id="desa_umkm_gallery_ids" name="desa_umkm_gallery" value="<?php echo esc_attr($gallery_ids); ?>">
                    <p class="description">Upload foto produk lainnya untuk gallery.</p>
                </td>
            </tr>
        </table>
<?php
    }

    public function save_umkm_meta_boxes($post_id)
    {
        if (!isset($_POST['desa_umkm_meta_nonce']) || !wp_verify_nonce($_POST['desa_umkm_meta_nonce'], 'save_desa_umkm_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['desa_umkm_phone'])) {
            update_post_meta($post_id, '_desa_umkm_phone', sanitize_text_field($_POST['desa_umkm_phone']));
        }

        if (isset($_POST['desa_umkm_location'])) {
            update_post_meta($post_id, '_desa_umkm_location', sanitize_text_field($_POST['desa_umkm_location']));
        }

        if (isset($_POST['desa_umkm_gallery'])) {
            update_post_meta($post_id, '_desa_umkm_gallery', sanitize_text_field($_POST['desa_umkm_gallery']));
        }
    }
}
