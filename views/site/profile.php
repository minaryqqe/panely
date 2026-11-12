<?php
/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\ReadingProgress[] $nowReading */
/** @var app\models\ReadingProgress[] $collection */

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Профиль пользователя — ' . $user->username;
?>

<form class="msearch" role="search" action="<?= Url::to(['/site/search']) ?>" method="get">
    <svg class="ic" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-search"></use></svg>
    <label class="visually-hidden" for="q-prof">Найти тайтл</label>
    <input id="q-prof" type="search" name="q" placeholder="Найти тайтл...">
</form>

<section class="prof" aria-labelledby="nick">
    <div class="avatar">
        <svg class="ic" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-user"></use></svg>
    </div>
    <div>
        <h1 id="nick"><?= Html::encode($user->username) ?></h1>
        <p style="color: var(--tx2); margin: 4px 0 12px; font-size: 14px;">
            <?= Html::encode($user->email) ?> · Роль: 
            <strong style="color: <?= $user->isAdmin() ? '#ff5252' : 'var(--tx)' ?>;">
                <?= $user->role ? Html::encode($user->role->name) : 'Пользователь' ?>
            </strong>
        </p>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <?php if (!Yii::$app->user->isGuest && $user->isAdmin()): ?>
                <a class="btn btn--sm" href="<?= Url::to(['/admin/index']) ?>">Перейти в Админ-панель</a>
            <?php endif; ?>
            <?php if (!Yii::$app->user->isGuest): ?>
                <a class="btn btn--sm btn--ghost" href="<?= Url::to(['/site/exit']) ?>">Выйти из аккаунта</a>
            <?php else: ?>
                <a class="btn btn--sm" href="<?= Url::to(['/site/login']) ?>">Войти в свой аккаунт</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="panel" aria-labelledby="h-now">
    <div class="panel__h">
        <h2 class="h2" id="h-now" style="margin:0">Сейчас читает</h2>
        <span class="badge"><?= count($nowReading) ?></span>
    </div>
    <?php if (empty($nowReading)): ?>
        <p style="color: var(--tx2); padding: 16px 0;">Сейчас ничего не читается.</p>
    <?php else: ?>
        <ul class="hscroll">
            <?php foreach ($nowReading as $item): ?>
                <?php
                    $m = $item->manga;
                    if (!$m) continue;
                    $total = $m->totalVolumes;
                    $read = (int)$item->current_volume;
                    $pct = $total > 0 ? min(100, round(($read / $total) * 100)) : 0;
                ?>
                <li>
                    <article class="card">
                        <div class="card__cover">
                            <a href="<?= Url::to(['/site/manga', 'id' => $m->ID_manga]) ?>">
                                <img src="<?= Html::encode($m->getCoverUrl()) ?>" alt="" width="400" height="600" loading="lazy" onerror="this.src='<?= Url::to('@web/images/logo.svg') ?>'">
                            </a>
                        </div>
                        <div class="card__body">
                            <h3 class="card__title">
                                <a href="<?= Url::to(['/site/manga', 'id' => $m->ID_manga]) ?>">
                                    <?= Html::encode($m->title) ?>
                                </a>
                            </h3>
                            <div class="bar" role="progressbar" aria-valuenow="<?= $read ?>" aria-valuemin="0" aria-valuemax="<?= $total ?>">
                                <span style="width:<?= $pct ?>%"></span>
                            </div>
                            <p class="card__meta"><?= $read ?>/<?= $total ?> томов</p>
                        </div>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<section class="panel" aria-labelledby="h-col" data-scope>
    <div class="panel__h">
        <h2 class="h2" id="h-col" style="margin:0">Вся коллекция</h2>
        <span class="badge"><?= count($collection) ?></span>
    </div>

    <ul class="grid">
        <?php if (empty($collection)): ?>
            <li style="grid-column: 1 / -1; color: var(--tx2); padding: 20px 0;">Коллекция пуста.</li>
        <?php else: ?>
            <?php foreach ($collection as $item): ?>
                <?php
                    $m = $item->manga;
                    if (!$m) continue;
                    $total = $m->totalVolumes;
                    $read = (int)$item->current_volume;
                    $pct = $total > 0 ? min(100, round(($read / $total) * 100)) : 0;
                ?>
                <li>
                    <article class="card">
                        <a class="card__cover" href="<?= Url::to(['/site/manga', 'id' => $m->ID_manga]) ?>">
                            <img src="<?= Html::encode($m->getCoverUrl()) ?>" alt="" width="400" height="600" loading="lazy" onerror="this.src='<?= Url::to('@web/images/logo.svg') ?>'">
                        </a>
                        <div class="card__body">
                            <h3 class="card__title">
                                <a href="<?= Url::to(['/site/manga', 'id' => $m->ID_manga]) ?>">
                                    <?= Html::encode($m->title) ?>
                                </a>
                            </h3>
                            <p class="card__meta"><?= Html::encode($m->type) ?> · том <?= $read ?> из <?= $total ?></p>
                            <div class="bar" role="progressbar" aria-valuenow="<?= $read ?>" aria-valuemin="0" aria-valuemax="<?= $total ?>">
                                <span style="width:<?= $pct ?>%"></span>
                            </div>
                            <div class="card__foot">
                                <span class="tag"><?= Html::encode($item->status) ?></span>
                            </div>
                        </div>
                    </article>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</section>
