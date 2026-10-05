<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\FrontendConfig;

$pengelola = FrontendConfig::findOne(3); 

$this->title = 'Pengelola';
?>

<style>
    .page-header-bg {
        background-color: #0d9488;
        padding: 4rem 0 3rem;
        margin-bottom: -3rem;
    }
    .page-title {
        color: #ffffff;
        font-weight: 700;
        font-size: 2.2rem;
        letter-spacing: -0.5px;
    }
    .main-content-card {
        background: #ffffff;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        padding: 3rem;
        margin-bottom: 4rem;
        position: relative;
        z-index: 10;
        border: 1px solid rgba(0,0,0,0.03);
    }
    .breadcrumb-custom .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        list-style: none;
    }
    .breadcrumb-custom .breadcrumb-item {
        display: flex;
        align-items: center;
    }
    .breadcrumb-custom .breadcrumb-item a {
        color: rgba(255,255,255,0.7);
        text-decoration: none;
        transition: color 0.2s ease;
    }
    .breadcrumb-custom .breadcrumb-item a:hover {
        color: #ffffff;
    }
    .breadcrumb-custom .breadcrumb-item.active {
        color: rgba(255,255,255,0.9);
    }
    .breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: rgba(255,255,255,0.5);
        padding: 0 0.5rem;
        font-size: 1.2rem;
        line-height: 1;
    }
    
    .content-wrapper {
        color: #475569;
        line-height: 1.8;
        font-size: 1.05rem;
    }
    .content-wrapper h1, .content-wrapper h2, .content-wrapper h3, .content-wrapper h4, .content-wrapper h5 {
        color: #0d9488;
        font-weight: 700;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
    }
    .content-wrapper p {
        margin-bottom: 1.25rem;
    }
    .content-wrapper ul, .content-wrapper ol {
        margin-bottom: 1.25rem;
        padding-left: 1.5rem;
    }
    .content-wrapper li {
        margin-bottom: 0.5rem;
    }
</style>

<div class="site-about" style="background-color: #f8fafc; min-height: 100vh; padding-top: 80px;">
    <div class="page-header-bg">
        <div class="container">
            <nav aria-label="breadcrumb" class="breadcrumb-custom">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= Url::to(['/']) ?>"><i class="bi bi-house-door me-1"></i> Beranda</a></li>
                    <li class="breadcrumb-item"><a href="#">Tentang Kami</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= Html::encode($this->title) ?></li>
                </ol>
            </nav>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <div class="container">
        <div class="main-content-card">
            <div class="content-wrapper">
                <?= $pengelola->isi_konfig ?>
            </div>
        </div>
    </div>
</div>
