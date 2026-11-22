<?php

// [label, ikon heroicons, nama route]
return [
    'ahli_gizi' => [
        'Utama' => [
            ['Dashboard',            'o-home',                     'gizi.dashboard'],
            ['Pasien Rawat Inap',    'o-user-group',               'pasien.index'],
        ],
        'Asuhan Gizi' => [
            ['Skrining Gizi',        'o-clipboard-document-check', 'gizi.skrining'],
            ['Asesmen ADIME',        'o-document-text',            'gizi.asesmen'],
            ['Order Diet',           'o-queue-list',               'gizi.order-diet'],
            ['Monitoring & Edukasi', 'o-chart-bar',                'gizi.monitoring'],
        ],
        'Produksi' => [
            ['Rekap Porsi (lihat)',  'o-table-cells',              'dapur.rekap'],
        ],
    ],
    'petugas_dapur' => [
        'Produksi' => [
            ['Rekap Porsi',          'o-table-cells',              'dapur.rekap'],
            ['Cetak Etiket',         'o-printer',                  'dapur.etiket'],
            ['Input Sisa Makanan',   'o-pencil-square',            'dapur.sisa'],
        ],
        'Data' => [
            ['Cari Pasien',          'o-magnifying-glass',         'pasien.index'],
        ],
    ],
    'admin' => [
        'Pengaturan' => [
            ['Master Data',          'o-circle-stack',             'admin.master'],
            ['Kelola Akun & Role',   'o-users',                    'admin.akun'],
            ['Audit Log',            'o-shield-check',             'admin.audit'],
        ],
        'Laporan' => [
            ['Laporan Instalasi',    'o-document-chart-bar',       'laporan.index'],
        ],
    ],
    'kepala_instalasi' => [
        'Utama' => [
            ['Dashboard Ringkasan',  'o-presentation-chart-line',  'kepala.dashboard'],
        ],
        'Laporan' => [
            ['Laporan & Ekspor Excel', 'o-document-arrow-down',    'laporan.index'],
        ],
    ],
];