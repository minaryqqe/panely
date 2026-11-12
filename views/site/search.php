<?php
/** @var yii\web\View $this */
/** @var app\models\Manga[] $mangas */
/** @var string $q */
/** @var array $selectedTypes */
/** @var array $selectedStatuses */

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Поиск и фильтрация манги';

$allStatuses = ['Читаю', 'Прочитано', 'Отложено', 'Буду читать'];
?>

<h1 class="h2">Поиск тайтлов в каталоге</h1>

<form method="get" action="<?= Url::to(['/site/search']) ?>" id="search-form">
    <div class="tools">
        <label class="visually-hidden" for="s-q">Поиск по названию</label>
        <input class="inp" id="s-q" type="search" name="q" value="<?= Html::encode($q) ?>" placeholder="Название тайтла или описание...">
        <button type="submit" class="btn btn--sm">Найти</button>
        <?php if (!empty($q) || !empty($selectedTypes) || !empty($selectedStatuses)): ?>
            <a href="<?= Url::to(['/site/search']) ?>" class="btn btn--sm btn--ghost">Сбросить всё</a>
        <?php endif; ?>
    </div>

    <!-- Быстрые фильтры по статусу (чипы) -->
    <div class="chips" role="group" aria-label="Быстрый фильтр по статусу" style="margin-bottom: 16px;">
        <a href="<?= Url::to(['/site/search', 'q' => $q, 'type' => $selectedTypes]) ?>" 
           class="chip" style="text-decoration: none; <?= empty($selectedStatuses) ? 'background:#fff; color:#111;' : '' ?>">
            Все статусы
        </a>
        <?php foreach ($allStatuses as $st): ?>
            <?php 
                $isActive = (count($selectedStatuses) === 1 && in_array($st, $selectedStatuses));
                $stUrl = Url::to(['/site/search', 'q' => $q, 'type' => $selectedTypes, 'status' => [$st]]);
            ?>
            <a href="<?= $stUrl ?>" class="chip" 
               style="text-decoration: none; <?= $isActive ? 'background:#fff; color:#111;' : '' ?>">
                <?= $st ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="sl">
        <!-- Боковая панель фильтров -->
        <div class="filters" id="filters" aria-label="Фильтры">
            <div class="filters__h">
                <span>Фильтры</span>
            </div>

            <!-- Фильтр по статусу чтения -->
            <fieldset class="fs" style="margin-bottom: 16px;">
                <legend style="font-weight: 500; margin-bottom: 8px;">Статус чтения</legend>
                <?php foreach ($allStatuses as $st): ?>
                    <label class="ck" style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px; cursor: pointer;">
                        <input type="checkbox" name="status[]" value="<?= $st ?>" 
                               <?= in_array($st, $selectedStatuses) ? 'checked' : '' ?>
                               onchange="document.getElementById('search-form').submit()">
                        <?= $st ?>
                    </label>
                <?php endforeach; ?>
            </fieldset>

            <!-- Фильтр по типу тайтла -->
            <fieldset class="fs">
                <legend style="font-weight: 500; margin-bottom: 8px;">Тип тайтла</legend>
                <label class="ck" style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px; cursor: pointer;">
                    <input type="checkbox" name="type[]" value="Манга" 
                           <?= empty($selectedTypes) || in_array('Манга', $selectedTypes) ? 'checked' : '' ?> 
                           onchange="document.getElementById('search-form').submit()">
                    Манга
                </label>
                <label class="ck" style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px; cursor: pointer;">
                    <input type="checkbox" name="type[]" value="Манхва" 
                           <?= empty($selectedTypes) || in_array('Манхва', $selectedTypes) ? 'checked' : '' ?> 
                           onchange="document.getElementById('search-form').submit()">
                    Манхва
                </label>
            </fieldset>

            <div style="margin-top: 16px;">
                <button type="submit" class="btn btn--sm btn--wide">Применить</button>
            </div>
        </div>

        <!-- Результаты поиска -->
        <section aria-labelledby="s-count">
            <h2 class="h2" id="s-count" style="margin-top:0">Найдено в БД: <?= count($mangas) ?></h2>

            <?php if (empty($mangas)): ?>
                <div style="background: var(--sf); border: 1px solid var(--bd); border-radius: 8px; padding: 32px; text-align: center; color: var(--tx2);">
                    <p style="font-size: 16px; margin-bottom: 8px;">По заданным фильтрам ничего не найдено.</p>
                    <a href="<?= Url::to(['/site/search']) ?>" class="btn btn--sm btn--ghost">Сбросить фильтры</a>
                </div>
            <?php else: ?>
                <ul class="res-list">
                    <?php foreach ($mangas as $manga): ?>
                        <?php 
                            $total = $manga->totalVolumes;
                            $userProg = $manga->getUserProgress();
                            $read = $userProg ? (int)$userProg->current_volume : 0;
                            $st = $userProg ? $userProg->status : 'Читаю';
                            $pct = $total > 0 ? min(100, round(($read / $total) * 100)) : 0;

                            $statusKey = 'reading';
                            if ($st === 'Прочитано') $statusKey = 'done';
                            elseif ($st === 'Отложено') $statusKey = 'paused';
                            elseif ($st === 'Буду читать') $statusKey = 'planned';
                        ?>
                        <li>
                            <article class="res" data-title="<?= Html::encode($manga->title) ?>">
                                <img src="<?= Html::encode($manga->getCoverUrl()) ?>" alt="" width="400" height="600" loading="lazy" onerror="this.src='<?= Url::to('@web/images/logo.svg') ?>'">
                                <div>
                                    <h2 class="res__t">
                                        <a href="<?= Url::to(['/site/manga', 'id' => $manga->ID_manga]) ?>">
                                            <?= Html::encode($manga->title) ?>
                                        </a>
                                    </h2>
                                    <p class="card__meta" style="margin-top: 4px;">
                                        <span class="tag tag--<?= $statusKey ?>" style="padding: 2px 8px; border-radius: 4px; font-size: 12px;">
                                            <?= Html::encode($st) ?>
                                        </span>
                                        · <?= Html::encode($manga->type) ?> 
                                        · <?= Html::encode(mb_strimwidth($manga->description, 0, 75, '...')) ?>
                                    </p>
                                </div>
                                <div class="res__p">
                                    <span>том <?= $read ?> из <?= $total ?></span>
                                    <div class="bar" role="progressbar" aria-valuenow="<?= $read ?>" aria-valuemin="0" aria-valuemax="<?= $total ?>">
                                        <span style="width:<?= $pct ?>%"></span>
                                    </div>
                                    <div style="margin-top: 6px; text-align: right;">
                                        <a href="<?= Url::to(['/site/manga', 'id' => $manga->ID_manga]) ?>" class="btn btn--sm" style="padding: 0 12px; font-size: 13px;">
                                            Открыть
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </div>
</form>
