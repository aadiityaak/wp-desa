<div class="wrap" x-data="complaintsManager()">
    <h1 class="wp-heading-inline">Aspirasi & Pengaduan Warga</h1>
    <button @click="generateDummy" class="page-title-action">Generate Dummy</button>
    <hr class="wp-header-end">

    <!-- Filters -->
    <ul class="subsubsub">
        <li class="all"><a href="#" @click.prevent="filterStatus('')" :class="{'current': currentStatus === ''}">Semua <span class="count" x-text="'(' + complaints.length + ')'"></span></a> |</li>
        <li class="pending"><a href="#" @click.prevent="filterStatus('pending')" :class="{'current': currentStatus === 'pending'}">Pending</a> |</li>
        <li class="in_progress"><a href="#" @click.prevent="filterStatus('in_progress')" :class="{'current': currentStatus === 'in_progress'}">Diproses</a> |</li>
        <li class="resolved"><a href="#" @click.prevent="filterStatus('resolved')" :class="{'current': currentStatus === 'resolved'}">Selesai</a> |</li>
        <li class="rejected"><a href="#" @click.prevent="filterStatus('rejected')" :class="{'current': currentStatus === 'rejected'}">Ditolak</a></li>
    </ul>

    <!-- Table -->
    <table class="wp-list-table widefat fixed striped table-view-list posts" style="margin-top: 20px;">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kode Tracking</th>
                <th>Kategori</th>
                <th>Pelapor</th>
                <th>Judul</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <template x-if="loading">
                <tr>
                    <td colspan="7">Memuat data...</td>
                </tr>
            </template>
            <template x-if="!loading && filteredComplaints.length === 0">
                <tr>
                    <td colspan="7">Tidak ada aduan.</td>
                </tr>
            </template>
            <template x-for="item in filteredComplaints" :key="item.id">
                <tr>
                    <td x-text="formatDate(item.created_at)"></td>
                    <td>
                        <strong x-text="item.tracking_code"></strong>
                    </td>
                    <td x-text="item.category"></td>
                    <td>
                        <div x-text="item.reporter_name"></div>
                        <small x-text="item.reporter_contact"></small>
                    </td>
                    <td x-text="item.subject"></td>
                    <td>
                        <span :class="'badge badge-' + item.status" x-text="formatStatus(item.status)"></span>
                    </td>
                    <td>
                        <button @click="openDetail(item)" class="button button-small">Lihat Detail</button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>

    <!-- Detail Modal -->
    <div x-show="isModalOpen"
        class="wp-desa-modal-overlay"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 99999; justify-content: center; align-items: center;"
        :style="isModalOpen ? 'display: flex' : 'display: none'">
        
        <div class="wp-desa-modal" style="background: white; padding: 20px; width: 600px; max-width: 90%; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-height: 90vh; overflow-y: auto;">
            <h2 style="margin-top: 0;">Detail Aduan</h2>
            <template x-if="selectedItem">
                <div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                        <div>
                            <p><strong>Pelapor:</strong> <span x-text="selectedItem.reporter_name"></span></p>
                            <p><strong>Kontak:</strong> <span x-text="selectedItem.reporter_contact || '-'"></span></p>
                        </div>
                        <div>
                            <p><strong>Kategori:</strong> <span x-text="selectedItem.category"></span></p>
                            <p><strong>Tanggal:</strong> <span x-text="formatDate(selectedItem.created_at)"></span></p>
                        </div>
                    </div>

                    <p><strong>Judul:</strong> <span x-text="selectedItem.subject"></span></p>
                    <p><strong>Isi Laporan:</strong></p>
                    <div style="background: #f0f0f1; padding: 10px; border-radius: 4px; margin-bottom: 15px; white-space: pre-wrap;" x-text="selectedItem.description"></div>

                    <template x-if="selectedItem.photo_url">
                        <div style="margin-bottom: 15px;">
                            <p><strong>Foto Lampiran:</strong></p>
                            <a :href="selectedItem.photo_url" target="_blank">
                                <img :src="selectedItem.photo_url" style="max-width: 100%; max-height: 200px; border-radius: 4px; border: 1px solid #ddd;">
                            </a>
                        </div>
                    </template>
                    
                    <hr>

                    <div style="margin-bottom: 15px;">
                        <label><strong>Update Status:</strong></label>
                        <select x-model="selectedItem.status" style="width: 100%; margin-bottom: 10px;">
                            <option value="pending">Pending</option>
                            <option value="in_progress">Diproses</option>
                            <option value="resolved">Selesai</option>
                            <option value="rejected">Ditolak</option>
                        </select>

                        <label><strong>Tanggapan Admin:</strong></label>
                        <textarea x-model="selectedItem.response" rows="3" style="width: 100%;" placeholder="Tulis tanggapan..."></textarea>
                    </div>

                    <div style="text-align: right; margin-top: 20px;">
                        <button @click="updateStatus()" class="button button-primary">Simpan Perubahan</button>
                        <button @click="isModalOpen = false" class="button">Tutup</button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<style>
    .badge {
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .badge-pending { background: #f0f0f1; color: #646970; }
    .badge-in_progress { background: #fff8e5; color: #996800; }
    .badge-resolved { background: #e7f5ea; color: #00a32a; }
    .badge-rejected { background: #fbeaea; color: #d63638; }
</style>

<script>
function complaintsManager() {
    return {
        complaints: [],
        loading: true,
        currentStatus: '',
        isModalOpen: false,
        selectedItem: null,
        
        init() {
            this.fetchComplaints();
        },
        
        fetchComplaints() {
            this.loading = true;
            fetch('/wp-json/wp-desa/v1/complaints', {
                headers: {
                    'X-WP-Nonce': '<?php echo wp_create_nonce("wp_rest"); ?>'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.complaints = data;
                this.loading = false;
            });
        },
        
        get filteredComplaints() {
            if (this.currentStatus === '') return this.complaints;
            return this.complaints.filter(c => c.status === this.currentStatus);
        },
        
        filterStatus(status) {
            this.currentStatus = status;
        },
        
        openDetail(item) {
            this.selectedItem = {...item}; // Copy object
            this.isModalOpen = true;
        },
        
        updateStatus() {
            const id = this.selectedItem.id;
            const newStatus = this.selectedItem.status;
            const response = this.selectedItem.response;

            fetch('/wp-json/wp-desa/v1/complaints/' + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': '<?php echo wp_create_nonce("wp_rest"); ?>'
                },
                body: JSON.stringify({ 
                    status: newStatus,
                    response: response
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update local data
                    const index = this.complaints.findIndex(c => c.id === id);
                    if (index !== -1) {
                        this.complaints[index].status = newStatus;
                        this.complaints[index].response = response;
                    }
                    alert('Status berhasil diperbarui');
                    this.isModalOpen = false;
                } else {
                    alert('Gagal update status');
                }
            });
        },

        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        },

        formatStatus(status) {
            const map = {
                'pending': 'Pending',
                'in_progress': 'Diproses',
                'resolved': 'Selesai',
                'rejected': 'Ditolak'
            };
            return map[status] || status;
        },

        generateDummy() {
            if (!confirm('Buat 20 data aduan dummy?')) return;
            
            fetch('/wp-json/wp-desa/v1/complaints/seed', {
                method: 'POST',
                headers: {
                    'X-WP-Nonce': '<?php echo wp_create_nonce("wp_rest"); ?>'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    this.fetchComplaints();
                } else {
                    alert('Gagal generate dummy');
                }
            });
        }
    }
}
</script>
