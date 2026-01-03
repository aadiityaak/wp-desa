<?php

namespace WpDesa\Frontend;

class Shortcode
{
    public function register()
    {
        add_shortcode('wp_desa_layanan', [$this, 'render_layanan']);
        add_shortcode('wp_desa_aduan', [$this, 'render_aduan']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function render_aduan()
    {
        ob_start();
?>
        <div id="wp-desa-aduan" class="wp-desa-wrapper" x-data="aduanWarga()">
            <div class="wp-desa-tabs">
                <button @click="tab = 'form'" :class="{'active': tab === 'form'}" class="wp-desa-tab-btn">Buat Laporan</button>
                <button @click="tab = 'track'" :class="{'active': tab === 'track'}" class="wp-desa-tab-btn">Cek Status Laporan</button>
            </div>

            <div class="wp-desa-content">
                <!-- Form Aduan -->
                <div x-show="tab === 'form'">
                    <div x-show="message.content"
                        :class="message.type === 'success' ? 'wp-desa-alert wp-desa-alert-success' : 'wp-desa-alert wp-desa-alert-error'">
                        <span x-text="message.content"></span>
                        <template x-if="trackingCode">
                            <div style="margin-top: 10px;">
                                <div style="font-size: 0.9em; margin-bottom: 5px;">Kode Tracking Anda:</div>
                                <div class="wp-desa-tracking-code" x-text="trackingCode"></div>
                                <p class="wp-desa-helper">Simpan kode ini untuk mengecek status laporan.</p>
                            </div>
                        </template>
                    </div>

                    <form @submit.prevent="submitComplaint" enctype="multipart/form-data">
                        <div class="wp-desa-form-group">
                            <label class="wp-desa-label">Nama Pelapor (Opsional)</label>
                            <input type="text" x-model="form.reporter_name" class="wp-desa-input" placeholder="Nama Anda (Boleh dikosongkan)">
                        </div>

                        <div class="wp-desa-form-group">
                            <label class="wp-desa-label">Kontak (HP/Email)</label>
                            <input type="text" x-model="form.reporter_contact" class="wp-desa-input" placeholder="Untuk konfirmasi status">
                        </div>

                        <div class="wp-desa-form-group">
                            <label class="wp-desa-label">Kategori Masalah</label>
                            <select x-model="form.category" required class="wp-desa-select">
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Infrastruktur">Infrastruktur (Jalan, Jembatan, dll)</option>
                                <option value="Pelayanan Publik">Pelayanan Publik</option>
                                <option value="Keamanan">Keamanan & Ketertiban</option>
                                <option value="Kebersihan">Kebersihan & Lingkungan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="wp-desa-form-group">
                            <label class="wp-desa-label">Judul Laporan</label>
                            <input type="text" x-model="form.subject" required class="wp-desa-input" placeholder="Ringkasan masalah">
                        </div>

                        <div class="wp-desa-form-group">
                            <label class="wp-desa-label">Isi Laporan</label>
                            <textarea x-model="form.description" required rows="5" class="wp-desa-textarea" placeholder="Jelaskan detail masalah, lokasi, dll"></textarea>
                        </div>

                        <div class="wp-desa-form-group">
                            <label class="wp-desa-label">Upload Foto Bukti</label>
                            <input type="file" @change="handleFileUpload" accept="image/*" class="wp-desa-input">
                            <small class="wp-desa-helper">Format: JPG, PNG. Maks 2MB.</small>
                        </div>

                        <button type="submit" :disabled="submitting" class="wp-desa-btn wp-desa-btn-primary">
                            <span x-show="!submitting">Kirim Laporan</span>
                            <span x-show="submitting">Mengirim...</span>
                        </button>
                    </form>
                </div>

                <!-- Tracking Form -->
                <div x-show="tab === 'track'">
                    <form @submit.prevent="checkStatus" style="margin-bottom: 1.5rem;">
                        <div style="display: flex; gap: 0.75rem;">
                            <input type="text" x-model="trackCode" placeholder="Masukkan Kode Tracking (Contoh: ADU-XXXXXX)" required class="wp-desa-input" style="flex: 1;">
                            <button type="submit" :disabled="tracking" class="wp-desa-btn wp-desa-btn-primary" style="width: auto;">
                                <span x-show="!tracking">Cek</span>
                                <span x-show="tracking">...</span>
                            </button>
                        </div>
                    </form>

                    <div x-show="trackResult" class="wp-desa-card">
                        <h4 style="margin-top: 0; margin-bottom: 1rem; font-size: 1.1rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem;">Status Laporan</h4>

                        <div class="wp-desa-card-row">
                            <span class="wp-desa-card-label">Judul</span>
                            <span class="wp-desa-card-value" x-text="trackResult.subject"></span>
                        </div>
                        <div class="wp-desa-card-row">
                            <span class="wp-desa-card-label">Kategori</span>
                            <span class="wp-desa-card-value" x-text="trackResult.category"></span>
                        </div>
                        <div class="wp-desa-card-row">
                            <span class="wp-desa-card-label">Tanggal</span>
                            <span class="wp-desa-card-value" x-text="formatDate(trackResult.created_at)"></span>
                        </div>
                        <div class="wp-desa-card-row">
                            <span class="wp-desa-card-label">Status</span>
                            <span :class="'wp-desa-badge wp-desa-badge-' + trackResult.status" x-text="formatStatus(trackResult.status)"></span>
                        </div>

                        <template x-if="trackResult.response">
                            <div style="margin-top: 1rem; background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                                <strong style="display: block; margin-bottom: 0.5rem; color: #374151;">Tanggapan Admin:</strong>
                                <p style="margin: 0; color: #4b5563;" x-text="trackResult.response"></p>
                            </div>
                        </template>
                    </div>

                    <div x-show="trackError" class="wp-desa-alert wp-desa-alert-error" x-text="trackError"></div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('aduanWarga', () => ({
                    tab: 'form',
                    form: {
                        reporter_name: '',
                        reporter_contact: '',
                        category: '',
                        subject: '',
                        description: '',
                        photo: null
                    },
                    message: {
                        type: '',
                        content: ''
                    },
                    trackingCode: null,
                    submitting: false,

                    trackCode: '',
                    trackResult: null,
                    trackError: null,
                    tracking: false,

                    handleFileUpload(event) {
                        this.form.photo = event.target.files[0];
                    },

                    submitComplaint() {
                        this.submitting = true;
                        this.message = {
                            type: '',
                            content: ''
                        };
                        this.trackingCode = null;

                        const formData = new FormData();
                        formData.append('reporter_name', this.form.reporter_name);
                        formData.append('reporter_contact', this.form.reporter_contact);
                        formData.append('category', this.form.category);
                        formData.append('subject', this.form.subject);
                        formData.append('description', this.form.description);
                        if (this.form.photo) {
                            formData.append('photo', this.form.photo);
                        }

                        fetch('/wp-json/wp-desa/v1/complaints/submit', {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.submitting = false;
                                if (data.success) {
                                    this.message = {
                                        type: 'success',
                                        content: data.message
                                    };
                                    this.trackingCode = data.tracking_code;
                                    this.form = {
                                        reporter_name: '',
                                        reporter_contact: '',
                                        category: '',
                                        subject: '',
                                        description: '',
                                        photo: null
                                    }; // Reset
                                    // Reset file input manually if needed
                                } else {
                                    this.message = {
                                        type: 'error',
                                        content: data.message || 'Terjadi kesalahan.'
                                    };
                                }
                            })
                            .catch(err => {
                                this.submitting = false;
                                this.message = {
                                    type: 'error',
                                    content: 'Gagal menghubungi server.'
                                };
                            });
                    },

                    checkStatus() {
                        this.tracking = true;
                        this.trackResult = null;
                        this.trackError = null;

                        fetch('/wp-json/wp-desa/v1/complaints/track?code=' + this.trackCode)
                            .then(res => res.json())
                            .then(data => {
                                this.tracking = false;
                                if (data.id) {
                                    this.trackResult = data;
                                } else {
                                    this.trackError = data.message || 'Data tidak ditemukan.';
                                }
                            })
                            .catch(err => {
                                this.tracking = false;
                                this.trackError = 'Gagal menghubungi server.';
                            });
                    },

                    formatDate(dateString) {
                        if (!dateString) return '-';
                        const date = new Date(dateString);
                        return date.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                    },

                    formatStatus(status) {
                        const map = {
                            'pending': 'Pending',
                            'in_progress': 'Diproses',
                            'resolved': 'Selesai',
                            'rejected': 'Ditolak'
                        };
                        return map[status] || status;
                    }
                }));
            });
        </script>
    <?php
        return ob_get_clean();
    }

    public function enqueue_scripts()
    {
        // Enqueue Alpine.js for frontend
        wp_enqueue_script('alpinejs', 'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js', [], '3.0.0', true);

        // Enqueue Frontend Styles
        wp_enqueue_style('wp-desa-frontend', WP_DESA_URL . 'assets/css/frontend/style.css', [], '1.0.0');
    }

    public function render_layanan()
    {
        ob_start();
    ?>
        <div id="wp-desa-layanan" class="wp-desa-wrapper" x-data="layananSurat()">

            <!-- Tabs -->
            <div class="wp-desa-tabs">
                <button @click="tab = 'request'" :class="{'active': tab === 'request'}" class="wp-desa-tab-btn">Ajukan Surat</button>
                <button @click="tab = 'track'" :class="{'active': tab === 'track'}" class="wp-desa-tab-btn">Cek Status</button>
            </div>

            <div class="wp-desa-content">
                <!-- Request Form -->
                <div x-show="tab === 'request'">
                    <h3 class="wp-desa-title">Form Permohonan Surat</h3>

                    <div x-show="message.content"
                        :class="message.type === 'success' ? 'wp-desa-alert wp-desa-alert-success' : 'wp-desa-alert wp-desa-alert-error'">
                        <span x-text="message.content"></span>
                        <template x-if="trackingCode">
                            <div style="margin-top: 10px;">
                                <div style="font-size: 0.9em; margin-bottom: 5px;">Kode Tracking Anda:</div>
                                <div class="wp-desa-tracking-code" x-text="trackingCode"></div>
                                <p class="wp-desa-helper">Simpan kode ini untuk mengecek status surat.</p>
                            </div>
                        </template>
                    </div>

                    <form @submit.prevent="submitRequest">
                        <div class="wp-desa-form-group">
                            <label class="wp-desa-label">NIK</label>
                            <input type="text" x-model="form.nik" required class="wp-desa-input" placeholder="Masukkan 16 digit NIK">
                            <small class="wp-desa-helper">Pastikan NIK sudah terdaftar di data desa.</small>
                        </div>

                        <div class="wp-desa-form-group">
                            <label class="wp-desa-label">Nama Lengkap (Sesuai KTP)</label>
                            <input type="text" x-model="form.name" required class="wp-desa-input" placeholder="Nama Lengkap">
                        </div>

                        <div class="wp-desa-form-group">
                            <label class="wp-desa-label">No. HP / WhatsApp</label>
                            <input type="text" x-model="form.phone" required class="wp-desa-input" placeholder="Contoh: 08123456789">
                        </div>

                        <div class="wp-desa-form-group">
                            <label class="wp-desa-label">Jenis Surat</label>
                            <select x-model="form.letter_type_id" required class="wp-desa-select">
                                <option value="">-- Pilih Jenis Surat --</option>
                                <template x-for="type in types" :key="type.id">
                                    <option :value="type.id" x-text="type.name"></option>
                                </template>
                            </select>
                            <template x-if="selectedTypeDescription">
                                <p class="wp-desa-helper" x-text="selectedTypeDescription"></p>
                            </template>
                        </div>

                        <div class="wp-desa-form-group">
                            <label class="wp-desa-label">Detail Keperluan</label>
                            <textarea x-model="form.details" rows="4" class="wp-desa-textarea" placeholder="Contoh: Untuk persyaratan melamar pekerjaan"></textarea>
                        </div>

                        <button type="submit" :disabled="submitting" class="wp-desa-btn wp-desa-btn-primary">
                            <span x-show="!submitting">Kirim Permohonan</span>
                            <span x-show="submitting">Mengirim...</span>
                        </button>
                    </form>
                </div>

                <!-- Tracking Form -->
                <div x-show="tab === 'track'">
                    <h3 class="wp-desa-title">Cek Status Surat</h3>

                    <form @submit.prevent="checkStatus" style="margin-bottom: 1.5rem;">
                        <div style="display: flex; gap: 0.75rem;">
                            <input type="text" x-model="trackCode" placeholder="Masukkan Kode Tracking" required class="wp-desa-input" style="flex: 1;">
                            <button type="submit" :disabled="tracking" class="wp-desa-btn wp-desa-btn-primary" style="width: auto;">
                                <span x-show="!tracking">Cek</span>
                                <span x-show="tracking">...</span>
                            </button>
                        </div>
                    </form>

                    <div x-show="trackResult" class="wp-desa-card">
                        <h4 style="margin-top: 0; margin-bottom: 1rem; font-size: 1.1rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem;">Status Permohonan</h4>

                        <div class="wp-desa-card-row">
                            <span class="wp-desa-card-label">Jenis Surat</span>
                            <span class="wp-desa-card-value" x-text="trackResult.type_name"></span>
                        </div>
                        <div class="wp-desa-card-row">
                            <span class="wp-desa-card-label">Pemohon</span>
                            <span class="wp-desa-card-value" x-text="trackResult.name"></span>
                        </div>
                        <div class="wp-desa-card-row">
                            <span class="wp-desa-card-label">Tanggal</span>
                            <span class="wp-desa-card-value" x-text="formatDate(trackResult.created_at)"></span>
                        </div>
                        <div class="wp-desa-card-row">
                            <span class="wp-desa-card-label">Status</span>
                            <span :class="'wp-desa-badge wp-desa-badge-' + trackResult.status" x-text="trackResult.status"></span>
                        </div>
                    </div>

                    <div x-show="trackError" class="wp-desa-alert wp-desa-alert-error" x-text="trackError"></div>
                </div>
            </div>

        </div>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('layananSurat', () => ({
                    tab: 'request',
                    types: [],
                    form: {
                        nik: '',
                        name: '',
                        phone: '',
                        letter_type_id: '',
                        details: ''
                    },
                    message: {
                        type: '',
                        content: ''
                    },
                    trackingCode: null,
                    submitting: false,

                    trackCode: '',
                    trackResult: null,
                    trackError: null,
                    tracking: false,

                    init() {
                        this.fetchTypes();
                    },

                    fetchTypes() {
                        fetch('/wp-json/wp-desa/v1/letters/types')
                            .then(res => res.json())
                            .then(data => this.types = data);
                    },

                    get selectedTypeDescription() {
                        const type = this.types.find(t => t.id == this.form.letter_type_id);
                        return type ? type.description : '';
                    },

                    submitRequest() {
                        this.submitting = true;
                        this.message = {
                            type: '',
                            content: ''
                        };
                        this.trackingCode = null;

                        fetch('/wp-json/wp-desa/v1/letters/request', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(this.form)
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.submitting = false;
                                if (data.success) {
                                    this.message = {
                                        type: 'success',
                                        content: data.message
                                    };
                                    this.trackingCode = data.tracking_code;
                                    this.form = {
                                        nik: '',
                                        name: '',
                                        phone: '',
                                        letter_type_id: '',
                                        details: ''
                                    }; // Reset
                                } else {
                                    this.message = {
                                        type: 'error',
                                        content: data.message || 'Terjadi kesalahan.'
                                    };
                                }
                            })
                            .catch(err => {
                                this.submitting = false;
                                this.message = {
                                    type: 'error',
                                    content: 'Gagal menghubungi server.'
                                };
                            });
                    },

                    checkStatus() {
                        this.tracking = true;
                        this.trackResult = null;
                        this.trackError = null;

                        fetch('/wp-json/wp-desa/v1/letters/track?code=' + this.trackCode)
                            .then(res => res.json())
                            .then(data => {
                                this.tracking = false;
                                if (data.id) {
                                    this.trackResult = data;
                                } else {
                                    this.trackError = data.message || 'Data tidak ditemukan.';
                                }
                            })
                            .catch(err => {
                                this.tracking = false;
                                this.trackError = 'Gagal menghubungi server.';
                            });
                    },

                    formatDate(dateString) {
                        if (!dateString) return '-';
                        const date = new Date(dateString);
                        return date.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                    }
                }));
            });
        </script>
<?php
        return ob_get_clean();
    }
}
