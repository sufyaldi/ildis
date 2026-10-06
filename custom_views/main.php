<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\Url;
use yii\widgets\Menu;

AppAsset::register($this);

use backend\models\FrontendConfig;

$logo = FrontendConfig::findOne(1);
$siteName = 'JDIH - Jaringan Dokumentasi dan Informasi Hukum';
$canonicalUrl = Url::canonical();

if (empty($this->params['description'])) {
    $this->registerMetaTag(['name' => 'description', 'content' => 'Jaringan Dokumentasi dan Informasi Hukum - Portal hukum terlengkap untuk peraturan, monografi, putusan, dan artikel hukum.']);
}

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> - <?= Html::encode($siteName) ?></title>
    <link rel="canonical" href="<?= Html::encode($canonicalUrl) ?>" />
    <?php $this->head() ?>
    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <?= $this->render('_google_analytics') ?>


    <style>
      .hero-section,
      .hero-section.inner-page,
      .page-header,
      .page-title-section,
      header.fixed-top + div,
      section[class*="hero"],
      div[class*="hero"] {
        background: linear-gradient(135deg, #0f766e 0%, #0d9488 50%, #14b8a6 100%) !important;
        background-color: #0d9488 !important;
      }
    </style>
<link href="/frontend/assets/css/jdih-theme-override.css" rel="stylesheet">
</head>


<body>

    <?php $this->beginBody() ?>

    <a class="visually-hidden-focusable skip-link" href="#main-content">Lewati ke konten utama</a>

    <!-- start main-wrapper section -->

    <header id="header" class="fixed-top d-flex align-items-center">
        <div class="container d-flex justify-content-between align-items-center">

            <div class="logo d-flex align-items-center">
              <a href="<?= Url::to(['/']) ?>" class="d-flex align-items-center text-decoration-none">
                <img src="/frontend/assets/img/logo_iainpare.png" alt="Logo IAIN Parepare" style="height: 54px; width: auto; margin-right: 10px; object-fit: contain;">
                <?= \common\components\LazyImage::img('@web/common/dokumen/' . $logo->isi_konfig, [
                    'id' => 'logo',
                    'alt' => Html::encode($siteName),
                    'style' => 'height: 48px; width: auto;'
                ], false); ?>
                <span class="fw-black text-dark ms-2" style="font-size: 2.2rem; font-family: 'Inter', 'Montserrat', sans-serif; font-weight: 900; letter-spacing: -1px;">JDIH</span>
                <div class="d-none d-md-inline-block" style="width: 3px; height: 42px; background-color: #1e293b; margin: 0 14px;"></div>
                <div class="d-none d-md-flex flex-column justify-content-center" style="line-height: 1.1; font-family: 'Inter', sans-serif;">
                  <div class="d-flex gap-1">
                    <span class="fw-black text-dark" style="font-size: 1.15rem; font-weight: 900; letter-spacing: 0.5px;">IAIN</span>
                    <span class="fw-black text-dark" style="font-size: 1.15rem; font-weight: 900; letter-spacing: 0.5px;">PAREPARE</span>
                  </div>
                  <span class="text-muted" style="font-size: 0.72rem; color: #64748b; font-weight: 600;">Jaringan Dokumentasi &amp; Informasi Hukum</span>
                </div>
              </a>
            </div>

          <nav id="navbar" class="navbar" aria-label="Navigasi utama">
            <div class="navbar-menu-desktop">
              <?= $this->render('menu.php') ?>
            </div>
            <button type="button" class="mobile-nav-toggle bi bi-list border-0 bg-transparent" aria-label="Buka menu" aria-expanded="false" aria-controls="mobile-nav"></button>
          </nav><!-- .navbar -->

        </div>
    </header><!-- End Header -->

    <div id="mobile-nav" class="mobile-nav" aria-hidden="true">
      <div class="mobile-nav-backdrop" aria-hidden="true"></div>
      <aside class="mobile-nav-drawer" role="dialog" aria-modal="false" aria-label="Menu navigasi">
        <div class="mobile-nav-header">
          <div class="mobile-nav-header__brand">
            <a href="<?= Url::to(['/']) ?>" class="mobile-nav-header__logo-link">
              <?= \common\components\LazyImage::img('@web/common/dokumen/' . $logo->isi_konfig, [
                  'class' => 'mobile-nav-header__logo',
                  'alt' => Html::encode($siteName),
              ], false) ?>
            </a>
            <span class="mobile-nav-header__title">Menu</span>
          </div>
          <button type="button" class="mobile-nav-close" aria-label="Tutup menu">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
          </button>
        </div>
        <form class="mobile-nav-search" action="<?= Url::to(['dokumen/index']) ?>" method="get" role="search">
          <i class="bi bi-search mobile-nav-search__icon" aria-hidden="true"></i>
          <input
            type="search"
            name="DokumenSearch[judul]"
            class="mobile-nav-search__input"
            placeholder="Cari dokumen..."
            autocomplete="off"
            aria-label="Cari dokumen"
          >
        </form>
        <div class="mobile-nav-body">
          <?= $this->render('menu.php') ?>
        </div>
      </aside>
    </div>

    <main id="main-content" role="main">
    <?= Alert::widget() ?>
    <?= $content ?>
    </main>

        <?= $this->render('footer.php') ?>

    <div id="a11y-widget" class="a11y-widget" aria-label="Widget aksesibilitas">
        <button
            type="button"
            id="a11y-widget-toggle"
            class="a11y-widget__toggle"
            aria-expanded="false"
            aria-controls="a11y-widget-panel"
            aria-label="Buka menu aksesibilitas"
            title="Menu aksesibilitas"
        >
            <i class="bi bi-universal-access-circle" aria-hidden="true"></i>
        </button>
        <div id="a11y-widget-panel" class="a11y-widget__panel" hidden role="region" aria-label="Menu aksesibilitas">
            <div class="a11y-widget__header">
                <h2 class="a11y-widget__title">Aksesibilitas</h2>
                <button type="button" id="a11y-widget-close" class="a11y-widget__close" aria-label="Tutup menu aksesibilitas">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                </button>
            </div>
            <ul class="a11y-widget__menu">
                <li class="a11y-widget__item">
                    <button type="button" class="a11y-widget__action" data-a11y-action="font-increase">
                        <i class="bi bi-zoom-in" aria-hidden="true"></i>
                        <span>Perbesar teks</span>
                    </button>
                </li>
                <li class="a11y-widget__item">
                    <button type="button" class="a11y-widget__action" data-a11y-action="font-decrease">
                        <i class="bi bi-zoom-out" aria-hidden="true"></i>
                        <span>Perkecil teks</span>
                    </button>
                </li>
                <li class="a11y-widget__divider" aria-hidden="true"></li>
                <li class="a11y-widget__item">
                    <button type="button" class="a11y-widget__action" data-a11y-action="a11y-high-contrast">
                        <i class="bi bi-circle-half" aria-hidden="true"></i>
                        <span>Kontras tinggi</span>
                    </button>
                </li>
                <li class="a11y-widget__item">
                    <button type="button" class="a11y-widget__action" data-a11y-action="a11y-grayscale">
                        <i class="bi bi-palette" aria-hidden="true"></i>
                        <span>Mode abu-abu</span>
                    </button>
                </li>
                <li class="a11y-widget__item">
                    <button type="button" class="a11y-widget__action" data-a11y-action="a11y-highlight-links">
                        <i class="bi bi-link-45deg" aria-hidden="true"></i>
                        <span>Sorot tautan</span>
                    </button>
                </li>
                <li class="a11y-widget__item">
                    <button type="button" class="a11y-widget__action" data-a11y-action="a11y-readable-font">
                        <i class="bi bi-type" aria-hidden="true"></i>
                        <span>Font mudah dibaca</span>
                    </button>
                </li>
                <li class="a11y-widget__divider" aria-hidden="true"></li>
                <li class="a11y-widget__item">
                    <button type="button" class="a11y-widget__action" data-a11y-action="read-aloud">
                        <i class="bi bi-volume-up" aria-hidden="true"></i>
                        <span>Baca layar</span>
                    </button>
                </li>
                <li class="a11y-widget__item">
                    <button type="button" class="a11y-widget__action" data-a11y-action="stop-read">
                        <i class="bi bi-stop-circle" aria-hidden="true"></i>
                        <span>Hentikan bacaan</span>
                    </button>
                </li>
                <li class="a11y-widget__divider" aria-hidden="true"></li>
                <li class="a11y-widget__item">
                    <button type="button" class="a11y-widget__action" data-a11y-action="reset">
                        <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        <span>Atur ulang</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- end main-wrapper section -->

    <!-- start scroll to top -->
    <a href="#" class="back-to-top" aria-label="Kembali ke atas"><i class="bi bi-chevron-up" aria-hidden="true"></i></a>
    <!-- end scroll to top -->

    <!-- all js include start -->

    <!-- jQuery -->


    <!-- all js include end -->

    <!-- Modal Pop-up Survey Kepuasan Pengguna (Auto Pop-up) -->
    <div id="jdih-survey-modal" class="jdih-survey-modal-overlay" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="survey-modal-title">
        <div class="jdih-survey-modal-card">
            <button type="button" class="jdih-survey-modal-close" id="jdih-survey-modal-close-btn" aria-label="Tutup modal">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
            <div class="jdih-survey-modal-icon-wrapper">
                <div class="jdih-survey-modal-icon">
                    <i class="bi bi-patch-question-fill" aria-hidden="true"></i>
                </div>
            </div>
            <h2 id="survey-modal-title" class="jdih-survey-modal-title">Survey Kepuasan Pengguna</h2>
            <p class="jdih-survey-modal-text">
                Terima kasih sudah berkunjung! Mohon isi survey kepuasan singkat kami untuk membantu meningkatkan layanan. Survey hanya akan memakan waktu 1-2 menit.
            </p>
            <div class="jdih-survey-modal-actions">
                <a href="<?= Url::to(['/site/survey']) ?>" id="jdih-survey-btn" class="jdih-survey-modal-btn">
                    Isi Survey
                </a>
            </div>
        </div>
    </div>

    <style>
    .jdih-survey-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }
    .jdih-survey-modal-overlay.active {
        opacity: 1;
    }
    .jdih-survey-modal-card {
        background: #ffffff;
        border-radius: 20px;
        max-width: 460px;
        width: 100%;
        padding: 36px 28px 32px;
        text-align: center;
        position: relative;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        transform: scale(0.9);
        transition: transform 0.3s ease-in-out;
    }
    .jdih-survey-modal-overlay.active .jdih-survey-modal-card {
        transform: scale(1);
    }
    .jdih-survey-modal-close {
        position: absolute;
        top: 16px;
        right: 16px;
        background: #f1f5f9;
        border: none;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .jdih-survey-modal-close:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .jdih-survey-modal-icon-wrapper {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }
    .jdih-survey-modal-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: #ccfbf1;
        color: #0d9488;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.8rem;
    }
    .jdih-survey-modal-title {
        font-family: 'Inter', sans-serif;
        font-size: 1.45rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 12px;
    }
    .jdih-survey-modal-text {
        font-family: 'Inter', sans-serif;
        font-size: 0.95rem;
        line-height: 1.6;
        color: #475569;
        margin-bottom: 24px;
    }
    .jdih-survey-modal-actions {
        display: flex;
        justify-content: center;
    }
    .jdih-survey-modal-btn {
        display: inline-block;
        background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
        color: #ffffff !important;
        font-family: 'Inter', sans-serif;
        font-weight: 600;
        font-size: 1rem;
        padding: 12px 36px;
        border-radius: 10px;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
        transition: all 0.2s ease;
    }
    .jdih-survey-modal-btn:hover {
        background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(13, 148, 136, 0.4);
    }
    </style>

    <script>
    (function () {
        const STORAGE_KEY = 'jdih_survey_modal_last_shown';
        const COOLDOWN_MS = 24 * 60 * 60 * 1000; // 24 jam

        function shouldShowModal() {
            const lastShown = localStorage.getItem(STORAGE_KEY);
            if (!lastShown) return true;
            return (Date.now() - parseInt(lastShown, 10)) > COOLDOWN_MS;
        }

        function closeModal() {
            const overlay = document.getElementById('jdih-survey-modal');
            if (!overlay) return;
            overlay.classList.remove('active');
            setTimeout(function () {
                overlay.style.display = 'none';
            }, 300);
            localStorage.setItem(STORAGE_KEY, Date.now().toString());
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (!shouldShowModal()) return;

            const overlay = document.getElementById('jdih-survey-modal');
            const closeBtn = document.getElementById('jdih-survey-modal-close-btn');
            const surveyBtn = document.getElementById('jdih-survey-btn');

            if (!overlay) return;

            setTimeout(function () {
                overlay.style.display = 'flex';
                void overlay.offsetWidth;
                overlay.classList.add('active');
            }, 2500);

            if (closeBtn) {
                closeBtn.addEventListener('click', closeModal);
            }
            if (surveyBtn) {
                surveyBtn.addEventListener('click', closeModal);
            }

            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) {
                    closeModal();
                }
            });
        });
    })();
    </script>

    <?php $this->endBody() ?>

</body>

</html>
<?php $this->endPage() ?>
