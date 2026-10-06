<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use yii\widgets\Menu;
?>

<?php
$parentTemplate = static function (string $icon): string {
    return '<span class="submenu-button"></span>'
        . '<a href="javascript:void(0)" class="mobile-menu-link mobile-menu-link--parent href_class">'
        . '<span class="mobile-menu-icon" aria-hidden="true"><i class="bi ' . $icon . '"></i></span>'
        . '<span class="mobile-menu-label">{label}</span>'
        . '<i class="bi bi-chevron-down mobile-menu-chevron" aria-hidden="true"></i>'
        . '</a>';
};

$parentLinkTemplate = static function (string $icon): string {
    return '<span class="submenu-button"></span>'
        . '<a href={url} class="mobile-menu-link mobile-menu-link--parent">'
        . '<span class="mobile-menu-icon" aria-hidden="true"><i class="bi ' . $icon . '"></i></span>'
        . '<span class="mobile-menu-label">{label}</span>'
        . '<i class="bi bi-chevron-down mobile-menu-chevron" aria-hidden="true"></i>'
        . '</a>';
};

$linkTemplate = static function (string $icon): string {
    return '<a href="{url}" class="mobile-menu-link">'
        . '<span class="mobile-menu-icon" aria-hidden="true"><i class="bi ' . $icon . '"></i></span>'
        . '<span class="mobile-menu-label">{label}</span>'
        . '</a>';
};

$srikandiItems = array_merge(
    array_map(static function (\common\models\DocumentType $t) {
        return [
            'label' => ucwords(strtolower($t->name)),
            'url' => Url::to(['/dokumen-pembentukan-puu/' . $t->slug]),
        ];
    }, \common\models\DocumentType::findByGroup(
        \common\components\DocumentGroup::LEGISLATION_FORMATION
    )),
    [
        [
            'label' => '<strong style="color: #0d9488;"><i class="bi bi-send-check me-1"></i> Ajukan Produk Hukum (SRIKANDI)</strong>',
            'url' => 'https://srikandi.arsip.go.id/',
            'options' => ['class' => 'border-top pt-2 mt-1'],
            'template' => '<a href="{url}" target="_blank" rel="noopener noreferrer" style="font-weight:700; color:#0d9488;">{label}</a>'
        ]
    ]
);

$menuItems = [
    [
        'label' => 'Beranda',
        'url' => ['/site/index'],
        'options' => ['class' => 'mobile-menu-item'],
        'template' => $linkTemplate('bi-house-door'),
    ],

    [
        'label' => 'Tentang Kami',
        'url' => '#',
        'options' => ['class' => 'dropdown mobile-menu-item'],
        'template' => $parentTemplate('bi-info-circle'),
        'items' => [
            ['label' => 'Sekilas Sejarah', 'url' => ['site/sekilas-sejarah']],
            ['label' => 'Dasar Hukum', 'url' => ['site/dasar-hukum']],
            ['label' => 'Visi ', 'url' => ['site/visi']],
            ['label' => 'Misi', 'url' => ['site/misi']],
            [
                'label' => 'Struktur Organisasi',
                'options' => ['class' => 'dropdown'],
                'template' => '<a href="javascript:void(0)" class="mobile-menu-link mobile-menu-link--parent href_class">'
                    . '<span class="mobile-menu-label">{label}</span>'
                    . '<i class="bi bi-chevron-down mobile-menu-chevron" aria-hidden="true"></i>'
                    . '</a>',
                'items' => [
                    ['label' => 'JDIH Instansi', 'url' => ['site/sto']],
                    ['label' => 'Biro/Bagian Hukum', 'url' => ['site/stoinstansi']],
                ]
            ],
            ['label' => 'SK Tim Pengelola', 'url' => ['site/pengelola']],
            ['label' => 'SOP', 'url' => ['site/sop']],
            ['label' => 'Statistik Dokumen Hukum', 'url' => ['/statistik']],
            ['label' => 'Survei Kepuasan (IKM)', 'url' => ['/site/survey']],
        ]
    ],

    [
        'label' => 'Jenis Dokumen',
        'url' => '#',
        'options' => ['class' => 'dropdown mobile-menu-item'],
        'activateItems' => true,
        'activeCssClass' => 'active',
        'template' => $parentLinkTemplate('bi-folder2-open'),

        'items' => [
            ['label' => 'Peraturan', 'url' => ['dokumen/peraturan']],
            ['label' => 'Monografi', 'url' => ['dokumen/monografi']],
            ['label' => 'Artikel/Majalah Hukum', 'url' => ['dokumen/artikel']],
            ['label' => 'Putusan', 'url' => ['dokumen/putusan']],
        ]
    ],
    [
        'label' => \common\components\DocumentGroup::label(
            \common\components\DocumentGroup::LEGISLATION_FORMATION
        ),
        'url' => Url::to(['/dokumen-pembentukan-puu']),
        'options' => ['class' => 'dropdown mobile-menu-item'],
        'activateItems' => true,
        'activeCssClass' => 'active',
        'template' => $parentLinkTemplate('bi-journal-text'),
        'items' => $srikandiItems,
    ],

    [
        'label' => 'Berita',
        'url' => ['berita/index'],
        'options' => ['class' => 'mobile-menu-item'],
        'template' => $linkTemplate('bi-newspaper'),
    ],
    [
        'label' => 'Link Terkait',
        'url' => '#',
        'options' => ['class' => 'dropdown mobile-menu-item'],
        'activateItems' => true,
        'activeCssClass' => 'active',
        'template' => $parentLinkTemplate('bi-link-45deg'),

        'items' => [
            ['label' => 'jdihn.go.id', 'url' => Url::to('https://jdihn.go.id/')],
            ['label' => 'bphn.go.id', 'url' => Url::to('https://bphn.go.id/')],
            ['label' => 'SRIKANDI Web', 'url' => Url::to('https://srikandi.arsip.go.id/')],
        ]
    ],


];

if (Yii::$app->user->isGuest) {
    $menuItems[] = [
        'label' => '',
        'url' => ['site/login'],
        'options' => ['class' => 'mobile-menu-item--hidden'],
    ];
} else {
    $menuItems[] = [
        'label' => 'Profile User',
        'url' => ['/profile/index'],
        'options' => ['class' => 'mobile-menu-item mobile-menu-auth'],
        'template' => $linkTemplate('bi-person-circle'),
    ];
    $menuItems[] = [
        'label' => 'Sign out',
        'url' => ['/site/logout'],
        'options' => ['class' => 'mobile-menu-item mobile-menu-auth'],
        'template' => $linkTemplate('bi-box-arrow-right'),
        'linkOptions' => ['data' => ['method' => 'post']],
    ];
}

echo Menu::widget([
    'items' => $menuItems,
    'options' => [],
    'encodeLabels' => false,
    'activateParents' => true,
    'activeCssClass' => 'current',
]);
?>
