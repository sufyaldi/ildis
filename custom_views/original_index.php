<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\components\LazyImage;
use frontend\models\Dokumen;
use backend\models\FrontendConfig;

/* Set meta tags */

$this->title = 'JDIH - Jaringan Dokumentasi dan Informasi Hukum';
$this->description = 'Jaringan Dokumentasi dan Informasi Hukum';
$this->keywords = ['Jaringan', 'Dokumentasi', 'Informasi', 'Hukum'];

$heroWebp = Url::to('@web/images/hero-bg.webp');
$heroPng = Url::to('@web/images/hero-bg.png');
$this->registerLinkTag(['rel' => 'preload', 'as' => 'image', 'href' => $heroWebp, 'type' => 'image/webp']);

$instansi = FrontendConfig::findOne(2);
$rawInstansi = $instansi ? $instansi->isi_konfig : '';
$instansiText = trim(strip_tags(str_ireplace(['<br>', '<br/>', '<br />'], ' ', $rawInstansi)));
$instansiText = preg_replace('/^JDIH[\s\-–—]*/iu', '', $instansiText);
if ($instansiText === '' || strcasecmp($instansiText, 'JDIH') === 0) {
    $instansiText = '';
}

// Get totals using the existing helper method
$totalPeraturan = Dokumen::find()->total(1);
$totalMonografi = Dokumen::find()->total(2);
$totalArtikel   = Dokumen::find()->total(3);
$totalPutusan   = Dokumen::find()->total(4);

$totalBerlaku       = Dokumen::find()->where(['status' => 'Berlaku', 'is_publish' => 1, 'tipe_dokumen' => Dokumen::TYPE_PERATURAN])->count();
$totalTidakBerlaku  = Dokumen::find()->where(['status' => 'Tidak Berlaku', 'is_publish' => 1, 'tipe_dokumen' => Dokumen::TYPE_PERATURAN])->count();

?>

<style>
    /* Custom styling for the Google-like search experience */
    .search-landing-container {
        min-height: 70vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        position: relative;
        overflow: hidden;
        padding: 80px 2rem 2rem;
        /* Added 80px top padding for navbar */
    }

    .search-landing-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(13, 148, 136, 0.85);
        /* Dark navy overlay matching theme */
        z-index: 1;
    }

    .search-landing-container > *:not(.search-landing-media) {
        position: relative;
        z-index: 2;
    }

    .hero-brand {
        font-size: clamp(2.5rem, 9vw, 3.75rem);
        font-weight: 800;
        color: #ffffff;
        margin: 0 0 2rem;
        letter-spacing: 0.02em;
        line-height: 1.05;
        text-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    }

    .hero-brand .hero-instansi {
        display: block;
        margin-top: 0.45em;
        font-size: 0.36em;
        font-weight: 500;
        letter-spacing: 0.01em;
        line-height: 1.35;
        color: rgba(255, 255, 255, 0.9);
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
        max-width: 22em;
        margin-left: auto;
        margin-right: auto;
    }

    @media screen and (max-width: 576px) {
        .search-landing-container {
            min-height: 72svh;
            padding: 5.5rem 1rem 2.5rem;
        }

        .hero-brand {
            font-size: clamp(2.25rem, 11vw, 2.75rem);
            margin-bottom: 1.5rem;
        }

        .hero-brand .hero-instansi {
            font-size: 0.34em;
            max-width: 18em;
        }
    }

    .hero-search-form {
        width: 100%;
        max-width: 650px;
        margin: 0 auto;
    }

    .search-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
        max-width: 650px;
        margin: 0 auto;
        background: #ffffff;
        border: 1px solid #dfe1e5;
        border-radius: 999px;
        box-shadow: 0 2px 12px rgba(32, 33, 36, 0.12);
        padding: 5px;
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .search-input-wrapper:focus-within {
        border-color: #cbd5e1;
        box-shadow: 0 4px 18px rgba(32, 33, 36, 0.16);
    }

    .search-landing-container .search-input {
        flex: 1 1 0;
        min-width: 0;
        width: 0;
        max-width: 100%;
        padding: 0.875rem 0.75rem 0.875rem 2.75rem;
        font-size: 1.05rem;
        border: none;
        border-radius: 0;
        background: transparent;
        box-shadow: none;
        outline: none;
    }

    .search-input:focus,
    .search-input:hover {
        box-shadow: none;
        border-color: transparent;
    }

    .search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1.15rem;
        pointer-events: none;
    }

    .search-btn {
        flex-shrink: 0;
        border-radius: 999px;
        padding: 0.7rem 1.6rem;
        font-size: 0.95rem;
        font-weight: 600;
        letter-spacing: 0.01em;
        color: #ffffff;
        background-color: #0d9488;
        border: none;
        box-shadow: none;
        transition: background-color 0.2s ease;
    }

    .search-btn:hover,
    .search-btn:focus-visible {
        background-color: #0f766e;
        color: #ffffff;
        box-shadow: none;
    }

    .search-btn:active {
        background-color: #094e47;
    }

    @media screen and (max-width: 480px) {
        .search-btn {
            padding: 0.65rem 1.15rem;
            font-size: 0.9rem;
        }

        .search-landing-container .search-input {
            font-size: 1rem;
            padding-left: 2.5rem;
        }
    }

    .quick-links {
        margin-top: 2.5rem;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        gap: 10px;
        width: 100%;
        max-width: 650px;
    }

    .quick-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        width: auto;
        max-width: 100%;
        padding: 8px 16px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.22);
        border-radius: 999px;
        color: #ffffff;
        font-size: 0.9rem;
        font-weight: 500;
        line-height: 1.2;
        text-decoration: none;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .quick-chip:hover,
    .quick-chip:focus-visible {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        text-decoration: none;
        border-color: rgba(255, 193, 7, 0.55);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .quick-chip .badge-count {
        flex-shrink: 0;
        background: rgba(255, 193, 7, 0.92);
        color: #0d9488;
        padding: 2px 7px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        line-height: 1.3;
    }

    @media screen and (max-width: 576px) {
        .quick-links {
            margin-top: 1.5rem;
            gap: 8px;
            max-width: 100%;
        }

        .quick-chip {
            padding: 7px 12px;
            font-size: 0.8rem;
            gap: 5px;
        }

        .quick-chip .badge-count {
            font-size: 0.65rem;
            padding: 1px 6px;
        }
    }

    @media screen and (max-width: 380px) {
        .quick-chip {
            padding: 6px 10px;
            font-size: 0.75rem;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .quick-chip {
            transition: none;
        }

        .quick-chip:hover,
        .quick-chip:focus-visible {
            transform: none;
        }
    }

    .news-strip {
        background: #f8fafc;
        padding: 3.5rem 0;
        margin-top: 4rem;
        border-top: 1px solid #e8edf4;
    }

    .news-strip__title {
        font-size: 2rem;
        font-weight: 700;
        color: #0d9488;
        letter-spacing: -0.5px;
        margin-bottom: 0.75rem;
    }

    .news-strip__subtitle {
        color: #64748b;
        font-size: 1.05rem;
        margin-bottom: 0;
    }

    .news-strip__accent {
        height: 4px;
        width: 60px;
        background-color: #ffc107;
        border-radius: 2px;
        margin: 1rem auto 0;
    }

    .news-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: box-shadow 0.2s ease;
    }

    .news-card:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06) !important;
    }

    .news-card__image {
        height: 200px;
        object-fit: cover;
    }

    .news-card__date {
        font-size: 0.8125rem;
        color: #64748b;
        margin-bottom: 0.5rem;
    }

    .news-card__title {
        font-size: 1.0625rem;
        font-weight: 600;
        line-height: 1.4;
        margin-bottom: 0.625rem;
    }

    .news-card__title a {
        color: #0d9488;
        text-decoration: none;
    }

    .news-card__title a:hover {
        color: #0d9488;
    }

    .news-card__excerpt {
        font-size: 0.9rem;
        line-height: 1.6;
        color: #64748b;
        margin-bottom: 0.75rem;
    }

    .news-read-more {
        font-size: 0.875rem;
        font-weight: 600;
        color: #0d9488;
        text-decoration: none;
    }

    .news-read-more:hover {
        color: #0d9488;
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    /* Koleksi Kami Cards */
    .koleksi-card {
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .koleksi-card__icon-box {
        width: 70px;
        height: 70px;
        background-color: rgba(13, 148, 136, 0.05);
    }

    .koleksi-card__icon {
        font-size: 2rem;
        color: #0d9488;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .koleksi-card__title {
        color: #0d9488;
    }

    .koleksi-card__desc {
        color: #64748b;
        line-height: 1.5;
    }

    .koleksi-card__count {
        color: #0d9488;
        font-size: 1.75rem;
    }

    .koleksi-card__divider {
        border-color: #f1f5f9 !important;
    }

    .koleksi-card-link:hover .koleksi-card,
    .koleksi-card-link:focus-visible .koleksi-card {
        background: #0d9488;
        box-shadow: 0 12px 28px rgba(13, 148, 136, 0.22);
        transform: translateY(-6px);
    }

    .koleksi-card-link:hover .koleksi-card__title,
    .koleksi-card-link:hover .koleksi-card__count {
        color: #ffffff;
    }

    .koleksi-card-link:hover .koleksi-card__desc {
        color: #cbd5e1;
    }

    .koleksi-card-link:hover .koleksi-card__icon-box {
        background-color: rgba(255, 193, 7, 0.15);
    }

    .koleksi-card-link:hover .koleksi-card__icon {
        color: #ffc107;
    }

    .koleksi-card-link:hover .koleksi-card__divider {
        border-color: rgba(255, 255, 255, 0.12) !important;
    }

    .koleksi-status-card {
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .koleksi-status-link:hover .koleksi-status-card,
    .koleksi-status-link:focus-visible .koleksi-status-card {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(13, 148, 136, 0.1);
    }
</style>

<div class="site-index">

        <div class="search-landing-container">
            <picture class="search-landing-media" aria-hidden="true">
                <source srcset="<?= Html::encode($heroWebp) ?>" type="image/webp">
                <img
                    src="<?= Html::encode($heroPng) ?>"
                    alt=""
                    class="search-landing-bg"
                    width="640"
                    height="640"
                    fetchpriority="high"
                    decoding="async"
                >
            </picture>

            <div class="text-center mb-3" data-aos="fade-up">
                <img src="/frontend/assets/img/logo_iainpare.png" alt="Logo IAIN Parepare" style="max-height: 110px; width: auto; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.3));">
            </div>

            <div class="text-center text-white mb-4" data-aos="fade-up" data-aos-delay="50">
                <div style="font-size: 0.95rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #f8fafc; opacity: 0.95; margin-bottom: 6px;">
                    Jaringan Dokumentasi dan Informasi Hukum
                </div>
                <h1 style="font-size: 2.3rem; font-weight: 900; letter-spacing: -0.5px; color: #ffffff; margin: 0; text-shadow: 0 2px 8px rgba(0,0,0,0.4);">
                    JDIH IAIN PAREPARE
                </h1>
            </div>

            <form action="<?= Url::to(['dokumen/index']) ?>" method="GET" class="w-100 hero-search-form" data-aos="fade-up" data-aos-delay="100" role="search">
                <div class="search-input-wrapper">
                    <i class="bi bi-search search-icon" aria-hidden="true"></i>
                    <input type="search" name="DokumenSearch[judul]" class="search-input" placeholder="Cari dokumen hukum, peraturan, putusan..." value="" autocomplete="off" aria-label="Cari dokumen hukum">
                    <button type="submit" class="btn search-btn">Cari</button>
                </div>
            </form>

            <div class="quick-links" data-aos="fade-up" data-aos-delay="200">
                <a href="<?= Url::to(['dokumen/peraturan']) ?>" class="quick-chip">
                    <i class="bi bi-file-earmark-text"></i> Peraturan
                    <span class="badge-count"><?= number_format($totalPeraturan) ?></span>
                </a>
                <a href="<?= Url::to(['dokumen/monografi']) ?>" class="quick-chip">
                    <i class="bi bi-book"></i> Monografi
                    <span class="badge-count"><?= number_format($totalMonografi) ?></span>
                </a>
                <a href="<?= Url::to(['dokumen/artikel']) ?>" class="quick-chip">
                    <i class="bi bi-journal-text"></i> Artikel Hukum
                    <span class="badge-count"><?= number_format($totalArtikel) ?></span>
                </a>
                <a href="<?= Url::to(['dokumen/putusan']) ?>" class="quick-chip">
                    <i class="bi bi-bank"></i> Putusan
                    <span class="badge-count"><?= number_format($totalPutusan) ?></span>
                </a>
            </div>
        </div>

        <!-- Koleksi Kami Cards Section -->
        <section class="koleksi-cards-section py-5" style="background-color: #f8fafc;">
            <div class="container py-4">
                <div class="row text-center mb-5">
                    <div class="col-12">
                        <h2 class="fw-bold mb-3" style="color: #0d9488; font-size: 2rem; letter-spacing: -0.5px;">Koleksi Kami</h2>
                        <p style="color: #64748b; font-size: 1.05rem;">Telusuri berbagai jenis pustaka dan dokumentasi hukum</p>
                        <div class="mx-auto mt-3" style="height: 4px; width: 60px; background-color: #ffc107; border-radius: 2px;"></div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-5 justify-content-center">
                            <!-- Peraturan -->
                            <div class="col">
                                <a href="<?= Url::to(['dokumen/peraturan']) ?>" class="text-decoration-none koleksi-card-link d-block h-100">
                                    <div class="card h-100 border-0 rounded koleksi-card">
                                        <div class="card-body p-4 text-center d-flex flex-column h-100">
                                            <div class="koleksi-card__icon-box mb-4 mx-auto rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bi bi-file-earmark-text koleksi-card__icon"></i>
                                            </div>
                                            <h5 class="koleksi-card__title font-weight-bold mb-3">Peraturan</h5>
                                            <p class="koleksi-card__desc small flex-grow-1">Peraturan Perundang-undangan tingkat pusat hingga daerah.</p>
                                            <div class="mt-3 pt-3 border-top koleksi-card__divider">
                                                <h3 class="koleksi-card__count font-weight-bold mb-0"><?= number_format($totalPeraturan) ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <!-- Monografi -->
                            <div class="col">
                                <a href="<?= Url::to(['dokumen/monografi']) ?>" class="text-decoration-none koleksi-card-link d-block h-100">
                                    <div class="card h-100 border-0 rounded koleksi-card">
                                        <div class="card-body p-4 text-center d-flex flex-column h-100">
                                            <div class="koleksi-card__icon-box mb-4 mx-auto rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bi bi-book koleksi-card__icon"></i>
                                            </div>
                                            <h5 class="koleksi-card__title font-weight-bold mb-3">Monografi Hukum</h5>
                                            <p class="koleksi-card__desc small flex-grow-1">Buku, naskah akademik, dan hasil kajian hukum.</p>
                                            <div class="mt-3 pt-3 border-top koleksi-card__divider">
                                                <h3 class="koleksi-card__count font-weight-bold mb-0"><?= number_format($totalMonografi) ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <!-- Artikel Hukum -->
                            <div class="col">
                                <a href="<?= Url::to(['dokumen/artikel']) ?>" class="text-decoration-none koleksi-card-link d-block h-100">
                                    <div class="card h-100 border-0 rounded koleksi-card">
                                        <div class="card-body p-4 text-center d-flex flex-column h-100">
                                            <div class="koleksi-card__icon-box mb-4 mx-auto rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bi bi-journal-text koleksi-card__icon"></i>
                                            </div>
                                            <h5 class="koleksi-card__title font-weight-bold mb-3">Artikel Hukum</h5>
                                            <p class="koleksi-card__desc small flex-grow-1">Artikel dan jurnal hukum cetak maupun elektronik.</p>
                                            <div class="mt-3 pt-3 border-top koleksi-card__divider">
                                                <h3 class="koleksi-card__count font-weight-bold mb-0"><?= number_format($totalArtikel) ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <!-- Putusan -->
                            <div class="col">
                                <a href="<?= Url::to(['dokumen/putusan']) ?>" class="text-decoration-none koleksi-card-link d-block h-100">
                                    <div class="card h-100 border-0 rounded koleksi-card">
                                        <div class="card-body p-4 text-center d-flex flex-column h-100">
                                            <div class="koleksi-card__icon-box mb-4 mx-auto rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bi bi-bank koleksi-card__icon"></i>
                                            </div>
                                            <h5 class="koleksi-card__title font-weight-bold mb-3">Putusan</h5>
                                            <p class="koleksi-card__desc small flex-grow-1">Dokumen putusan pengadilan yang bernilai yurisprudensi.</p>
                                            <div class="mt-3 pt-3 border-top koleksi-card__divider">
                                                <h3 class="koleksi-card__count font-weight-bold mb-0"><?= number_format($totalPutusan) ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Bottom Row (Status) -->
                        <div class="row justify-content-center g-4">
                            <!-- Peraturan Berlaku -->
                            <div class="col-md-5">
                                <a href="<?= Url::to(['dokumen/berlaku']) ?>" class="text-decoration-none koleksi-status-link d-block h-100">
                                    <div class="card border-0 rounded koleksi-status-card h-100">
                                        <div class="card-body p-4 d-flex align-items-center">
                                            <div class="icon-box rounded-circle d-flex align-items-center justify-content-center mr-4" style="width: 60px; height: 60px; background-color: rgba(16, 185, 129, 0.1); flex-shrink: 0;">
                                                <i class="bi bi-check2-circle" style="font-size: 1.8rem; color: #10b981;"></i>
                                            </div>
                                            <div class="flex-grow-1 text-left">
                                                <h6 class="font-weight-bold text-dark mb-1">Peraturan Berlaku</h6>
                                                <p class="mb-0 small" style="color: #64748b;">Kumpulan peraturan yang masih aktif.</p>
                                            </div>
                                            <div class="text-right pl-3 border-left ml-3">
                                                <h4 class="font-weight-bold mb-0" style="color: #10b981;"><?= number_format($totalBerlaku) ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <!-- Peraturan Tidak Berlaku -->
                            <div class="col-md-5">
                                <a href="<?= Url::to(['dokumen/tberlaku']) ?>" class="text-decoration-none koleksi-status-link d-block h-100">
                                    <div class="card border-0 rounded koleksi-status-card h-100">
                                        <div class="card-body p-4 d-flex align-items-center">
                                            <div class="icon-box rounded-circle d-flex align-items-center justify-content-center mr-4" style="width: 60px; height: 60px; background-color: rgba(239, 68, 68, 0.1); flex-shrink: 0;">
                                                <i class="bi bi-x-circle" style="font-size: 1.8rem; color: #ef4444;"></i>
                                            </div>
                                            <div class="flex-grow-1 text-left">
                                                <h6 class="font-weight-bold text-dark mb-1">Tidak Berlaku</h6>
                                                <p class="mb-0 small" style="color: #64748b;">Kumpulan peraturan yang telah dicabut.</p>
                                            </div>
                                            <div class="text-right pl-3 border-left ml-3">
                                                <h4 class="font-weight-bold mb-0" style="color: #ef4444;"><?= number_format($totalTidakBerlaku) ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SRIKANDI E-Pengajuan CTA Banner -->
        <section class="srikandi-cta-section py-5" style="background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%); color: #ffffff;">
            <div class="container">
                <div class="row align-items-center justify-content-between g-4">
                    <div class="col-lg-8 text-center text-lg-start">
                        <div class="d-inline-flex align-items-center px-3 py-1 mb-3 rounded-pill" style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); font-size: 0.85rem; font-weight: 600;">
                            <i class="bi bi-shield-check text-warning me-2"></i> Layanan Resmi E-Office &amp; Tata Naskah Dinas
                        </div>
                        <h2 class="fw-bold mb-3" style="font-size: 1.85rem; color: #ffffff; letter-spacing: -0.5px;">
                            Pengajuan &amp; Penyusunan Produk Hukum Internal
                        </h2>
                        <p class="mb-0" style="color: #ccfbf1; font-size: 1.05rem; line-height: 1.6; max-width: 680px;">
                            Ingin mengajukan Usulan Peraturan, Keputusan Rektor, Naskah Akademik, atau Produk Hukum lainnya? Gunakan aplikasi <strong>SRIKANDI</strong> untuk pengajuan resmi terintegrasi TTE.
                        </p>
                    </div>
                    <div class="col-lg-4 text-center text-lg-end">
                        <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-end gap-2 mb-2">
                            <a href="https://srikandi.arsip.go.id/" target="_blank" rel="noopener noreferrer" class="btn btn-warning btn-lg fw-bold rounded-pill px-4 py-3 shadow-sm" style="color: #0f766e; background-color: #ffc107; border: none; font-size: 1rem;">
                                <i class="bi bi-box-arrow-up-right me-2"></i> Akses SRIKANDI Web
                            </a>
                        </div>
                        <div class="text-center text-lg-end">
                            <small style="color: #99f6e4; font-size: 0.82rem;">
                                Aplikasi Mobile: 
                                <a href="https://play.google.com/store/apps/details?id=com.anri.srikandi.v3" target="_blank" rel="noopener noreferrer" class="text-white text-decoration-underline fw-semibold ms-1 me-2"><i class="bi bi-google-play me-1"></i>Play Store (Android)</a>
                                &middot;
                                <a href="https://apps.apple.com/us/app/srikandi/id6745420835" target="_blank" rel="noopener noreferrer" class="text-white text-decoration-underline fw-semibold ms-2"><i class="bi bi-apple me-1"></i>App Store (iOS)</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- News Strip (Only on landing) -->
        <?= $this->render('_popular-documents', ['popularDocuments' => $popularDocuments ?? []]) ?>

        <?php if (!empty($berita)): ?>
            <section class="news-strip">
                <div class="container">
                    <div class="row text-center mb-5">
                        <div class="col-12">
                            <h2 class="news-strip__title">Berita Terbaru</h2>
                            <p class="news-strip__subtitle">Informasi dan kegiatan terkini seputar hukum</p>
                            <div class="news-strip__accent"></div>
                        </div>
                    </div>
                    <div class="row">
                        <?php foreach ($berita as $data): ?>
                            <div class="col-lg-4 mb-4">
                                <article class="card h-100 border-0 shadow-sm rounded overflow-hidden news-card">
                                    <?= Html::a(LazyImage::img('@web/common/dokumen/' . $data->image, [
                                        'class' => 'card-img-top news-card__image w-100',
                                        'alt' => $data->judul,
                                    ]), ['berita/view', 'id' => $data->id]); ?>
                                    <div class="card-body p-4 d-flex flex-column">
                                        <time class="news-card__date" datetime="<?= Html::encode($data->tanggal) ?>">
                                            <?= \common\components\DateHelper::formatIndonesian($data->tanggal) ?>
                                        </time>
                                        <h3 class="news-card__title">
                                            <?= Html::a(Html::encode($data->judul), ['berita/view', 'id' => $data->id]) ?>
                                        </h3>
                                        <p class="news-card__excerpt flex-grow-1">
                                            <?= implode(' ', array_slice(explode(' ', strip_tags($data->isi)), 0, 18)) . '…' ?>
                                        </p>
                                        <?= Html::a('Baca selengkapnya →', ['berita/view', 'id' => $data->id], ['class' => 'news-read-more mt-auto']) ?>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-3">
                        <?= Html::a('Lihat semua berita <i class="bi bi-arrow-right"></i>', ['berita/index'], ['class' => 'btn btn-outline-primary rounded-pill px-4']); ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

</div>