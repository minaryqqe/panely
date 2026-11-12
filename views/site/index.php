<?php
/** @var yii\web\View $this */
/** @var app\models\Manga[] $mangas */
/** @var int $statInProgress */
/** @var int $statReadVolumes */
/** @var int $statWishlist */
/** @var app\models\ReadingProgress[] $continueProgress */

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Panely — трекер манги и комиксов';
?>
<h1 class="visually-hidden">Panely — трекер манги и комиксов</h1>

<form class="msearch" role="search" action="<?= Url::to(['/site/search']) ?>" method="get">
    <svg class="ic" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-search"></use></svg>
    <label class="visually-hidden" for="q-home">Найти тайтл</label>
    <input id="q-home" type="search" name="q" placeholder="Найти тайтл в базе...">
</form>

<section class="hero" aria-label="Приветствие">
    <p class="hero__t">Вся твоя манга<br>на одной полке</p>
    <ul class="stats">
        <li class="stat"><b><?= $statInProgress ?></b><span>тайтлов в прогрессе</span></li>
        <li class="stat"><b><?= $statReadVolumes ?></b><span>томов прочитано</span></li>
        <li class="stat"><b><?= $statWishlist ?></b><span>в вишлисте</span></li>
    </ul>
    <p class="hero__p">Отмечай прочитанные тома, следи за прогрессом по тайтлам и копи вишлист того, что хочешь прочитать.</p>
    <div class="hero__act">
        <a class="btn" href="<?= Url::to(['/site/progress']) ?>">Мой прогресс</a>
        <a class="btn btn--ghost" href="<?= Url::to(['/site/search']) ?>">Каталог манги</a>
    </div>
</section>

<section aria-labelledby="h-prog" data-scope>
    <h2 class="h2" id="h-prog">Прогресс чтения</h2>
    <div class="tools">
        <label class="visually-hidden" for="hq">Поиск по названию</label>
        <input class="inp" id="hq" type="search" placeholder="Поиск по названию" data-f="q">
        <label class="visually-hidden" for="hs">Сортировка</label>
        <select class="inp" id="hs" data-f="sort">
            <option value="new">Сначала новые</option>
            <option value="name">По названию</option>
            <option value="progress">По прогрессу</option>
        </select>
    </div>

    <ul class="grid">
        <?php if (empty($mangas)): ?>
            <li style="grid-column: 1 / -1; text-align: center; color: var(--tx2); padding: 40px 0;">
                В базе данных пока нет тайтлов.
            </li>
        <?php else: ?>
            <?php foreach ($mangas as $manga): ?>
                <?php
                    $total = $manga->totalVolumes;
                    $userProg = $manga->getUserProgress();
                    $read = $userProg ? (int)$userProg->current_volume : 0;
                    $status = $userProg ? $userProg->status : 'Читаю';
                    $pct = $total > 0 ? min(100, round(($read / $total) * 100)) : 0;
                    $isDone = ($read >= $total && $total > 0) || ($status === 'Прочитано');

                    $statusKey = 'reading';
                    if ($status === 'Прочитано') $statusKey = 'done';
                    elseif ($status === 'Отложено') $statusKey = 'paused';
                    elseif ($status === 'Буду читать') $statusKey = 'planned';
                ?>
                <li>
                    <article class="card" data-item data-title="<?= Html::encode($manga->title) ?>" data-type="<?= Html::encode($manga->type) ?>"
                        data-status="<?= $statusKey ?>" data-read="<?= $read ?>" data-total="<?= $total ?>">
                        <a class="card__cover" href="<?= Url::to(['/site/manga', 'id' => $manga->ID_manga]) ?>" tabindex="-1" aria-hidden="true">
                            <img src="<?= Html::encode($manga->getCoverUrl()) ?>" alt="<?= Html::encode($manga->title) ?>" 
                                 width="400" height="600" loading="lazy" onerror="this.src='<?= Url::to('@web/images/logo.svg') ?>'">
                        </a>
                        <div class="card__body">
                            <h3 class="card__title">
                                <a href="<?= Url::to(['/site/manga', 'id' => $manga->ID_manga]) ?>"><?= Html::encode($manga->title) ?></a>
                            </h3>
                            <p class="card__meta"><?= Html::encode($manga->type) ?> · том <span data-read-out><?= $read ?></span> из <?= $total ?></p>
                            <div class="bar" role="progressbar" aria-label="Прогресс чтения" aria-valuemin="0" aria-valuemax="<?= $total ?>" aria-valuenow="<?= $read ?>">
                                <span style="width:<?= $pct ?>%"></span>
                            </div>
                            <div class="card__foot">
                                <span class="tag tag--<?= $statusKey ?>"><?= Html::encode($status) ?></span>
                                <?php if (!$isDone && $total > 0): ?>
                                    <a class="btn btn--sm" href="<?= Url::to(['/site/inc-progress', 'id' => $manga->ID_manga]) ?>" data-inc>+1 том</a>
                                <?php else: ?>
                                    <button class="btn btn--sm" type="button" disabled style="opacity: 0.65;">Прочитано</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
    <p class="empty" data-empty hidden>Ничего не найдено</p>
</section>

<section class="sec--cont" aria-labelledby="h-cont">
    <h2 class="h2" id="h-cont">Продолжить чтение</h2>
    <ul class="cont">
        <?php if (!empty($continueProgress)): ?>
            <?php foreach ($continueProgress as $cp): ?>
                <?php
                    $m = $cp->manga;
                    if (!$m) continue;
                    $nextVol = $cp->current_volume + 1;
                ?>
                <li class="cont__item">
                    <img src="<?= Html::encode($m->getCoverUrl()) ?>" alt="" width="400" height="600" loading="lazy" onerror="this.src='<?= Url::to('@web/images/logo.svg') ?>'">
                    <div>
                        <b><?= Html::encode($m->title) ?></b>
                        <span>Следующий том: <?= $nextVol ?></span>
                        <a class="btn btn--sm cont__btn" href="<?= Url::to(['/site/manga', 'id' => $m->ID_manga]) ?>">Продолжить читать</a>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <?php foreach (array_slice($mangas, 0, 3) as $m): ?>
                <li class="cont__item">
                    <img src="<?= Html::encode($m->getCoverUrl()) ?>" alt="" width="400" height="600" loading="lazy" onerror="this.src='<?= Url::to('@web/images/logo.svg') ?>'">
                    <div>
                        <b><?= Html::encode($m->title) ?></b>
                        <span>Всего томов: <?= $m->totalVolumes ?></span>
                        <a class="btn btn--sm cont__btn" href="<?= Url::to(['/site/manga', 'id' => $m->ID_manga]) ?>">Читать</a>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</section>
