<?php

/** @var string $content */
/** @var yii\web\View $this */

use yii\helpers\Url;
use yii\helpers\Html;
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title ? $this->title . ' — Panely' : 'Panely — трекер прочитанной манги') ?></title>
    <meta name="description" content="Panely — трекер прочитанной манги и комиксов">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= Url::to("@web/css/style.css") ?>">
</head>

<body>
    <svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
        <defs>
            <symbol id="i-home" viewBox="0 0 24 24">
                <path d="M3 11l9-8 9 8v10h-6v-6H9v6H3z" />
            </symbol>
            <symbol id="i-flag" viewBox="0 0 24 24">
                <path d="M6 21V4M6 4h11l-2 4 2 4H6" />
            </symbol>
            <symbol id="i-search" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="7" />
                <path d="M20 20l-4-4" />
            </symbol>
            <symbol id="i-user" viewBox="0 0 24 24">
                <circle cx="12" cy="8" r="4" />
                <path d="M4 21a8 8 0 0116 0" />
            </symbol>
            <symbol id="i-settings" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3" />
                <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" />
            </symbol>
            <symbol id="i-close" viewBox="0 0 24 24">
                <path d="M6 6l12 12M18 6L6 18" />
            </symbol>
            <symbol id="i-filter" viewBox="0 0 24 24">
                <path d="M4 6h16M7 12h10M10 18h4" />
            </symbol>
            <symbol id="i-check" viewBox="0 0 24 24">
                <path d="M5 12l5 5 9-10" />
            </symbol>
        </defs>
    </svg>
    <header class="hdr">
        <div class="wrap hdr__in">
            <a class="logo" href="<?= Url::to(["/site/index"]) ?>">
                <img src="<?= Url::to("@web/images/logo.svg") ?>" alt="" width="26" height="24">
                <span>Panely</span>
            </a>
            <nav class="nav" aria-label="Основное меню">
                <a href="<?= Url::to(["/site/index"]) ?>">Главная</a>
                <a href="<?= Url::to(["/site/progress"]) ?>">Прогресс</a>
                <a href="<?= Url::to(["/site/search"]) ?>">Поиск</a>
                <a href="<?= Url::to(["/site/profile"]) ?>">Профиль</a>
                <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()): ?>
                    <a href="<?= Url::to(["/admin/index"]) ?>" style="color: #ff5252; font-weight: 500;">
                        Админ-панель
                    </a>
                <?php endif; ?>
            </nav>
            <div style="display: flex; align-items: center; gap: 10px;">
                <?php if (Yii::$app->user->isGuest): ?>
                    <a class="btn btn--sm hdr__login" href="<?= Url::to(["/site/login"]) ?>">Войти</a>
                    <a class="btn btn--sm btn--ghost hdr__login" href="<?= Url::to(["/site/register"]) ?>">Регистрация</a>
                <?php else: ?>
                    <span style="font-size: 14px; color: var(--tx2); display: none; @media(min-width:768px){display:inline;}">
                        <?= Html::encode(Yii::$app->user->identity->username) ?>
                        <?php if (Yii::$app->user->identity->isAdmin()): ?>
                            <span class="tag tag--admin" style="font-size: 10px; padding: 2px 6px; border-radius: 3px; margin-left: 4px;">Admin</span>
                        <?php endif; ?>
                    </span>
                    <a class="btn btn--sm btn--ghost hdr__login" href="<?= Url::to(["/site/exit"]) ?>">Выйти</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="wrap main" id="main">
        <?php foreach (Yii::$app->session->getAllFlashes() as $key => $message): ?>
            <div class="alert alert-<?= $key === 'error' ? 'danger' : ($key === 'success' ? 'success' : 'info') ?>" style="margin-top: 16px;">
                <?= Html::encode($message) ?>
            </div>
        <?php endforeach; ?>

        <?= $content ?>
    </main>

    <footer class="ftr">
        <div class="wrap">© 2026 Panely · трекер прочитанной манги</div>
    </footer>

    <nav class="tabbar" aria-label="Мобильное меню">
        <a href="<?= Url::to(["/site/index"]) ?>">
            <svg class="ic" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-home"></use></svg>
            <span>Главная</span>
        </a>
        <a href="<?= Url::to(["/site/progress"]) ?>">
            <svg class="ic" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-flag"></use></svg>
            <span>Прогресс</span>
        </a>
        <a href="<?= Url::to(["/site/search"]) ?>">
            <svg class="ic" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-search"></use></svg>
            <span>Поиск</span>
        </a>
        <a href="<?= Url::to(["/site/profile"]) ?>">
            <svg class="ic" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-user"></use></svg>
            <span>Профиль</span>
        </a>
        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()): ?>
            <a href="<?= Url::to(["/admin/index"]) ?>" style="color: #ff5252;">
                <svg class="ic" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-settings"></use></svg>
                <span>Админка</span>
            </a>
        <?php endif; ?>
    </nav>
    <script src="<?= Url::to('@web/js/main.js') ?>"></script>
</body>
</html>
