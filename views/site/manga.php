<?php
/** @var yii\web\View $this */
/** @var app\models\Manga $manga */
/** @var app\models\ReadingProgress|null $progress */

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = $manga->title;
$total = $manga->totalVolumes;
$read = $progress ? (int)$progress->current_volume : 0;
$status = $progress ? $progress->status : 'Читаю';
$pct = $total > 0 ? min(100, round(($read / $total) * 100)) : 0;
?>

<nav class="crumbs" aria-label="Хлебные крошки">
    <a href="<?= Url::to(['/site/index']) ?>">Главная</a> / 
    <a href="<?= Url::to(['/site/progress']) ?>">Мой прогресс</a> / 
    <span aria-current="page"><?= Html::encode($manga->title) ?></span>
</nav>

<div class="mg">
    <div class="mg__side">
        <img src="<?= Html::encode($manga->getCoverUrl()) ?>" 
             alt="Обложка «<?= Html::encode($manga->title) ?>»" 
             width="400" height="600"
             onerror="this.src='<?= Url::to('@web/images/logo.svg') ?>'">

        <?php if (!Yii::$app->user->isGuest): ?>
            <form method="post" action="<?= Url::to(['/site/manga', 'id' => $manga->ID_manga]) ?>" style="margin-top: 12px; display: flex; flex-direction: column; gap: 8px;">
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                <label for="st" style="font-size: 13px; color: var(--tx2);">Статус чтения в БД:</label>
                <select class="inp" id="st" name="status" onchange="this.form.submit()">
                    <option value="Читаю" <?= $status === 'Читаю' ? 'selected' : '' ?>>Читаю</option>
                    <option value="Прочитано" <?= $status === 'Прочитано' ? 'selected' : '' ?>>Прочитано</option>
                    <option value="Отложено" <?= $status === 'Отложено' ? 'selected' : '' ?>>Отложено</option>
                    <option value="Буду читать" <?= $status === 'Буду читать' ? 'selected' : '' ?>>Буду читать</option>
                </select>
            </form>

            <div style="margin-top: 12px; display: flex; flex-direction: column; gap: 8px;">
                <?php if ($read < $total): ?>
                    <a class="btn btn--wide" href="<?= Url::to(['/site/inc-progress', 'id' => $manga->ID_manga]) ?>">
                        + Отметить том <?= $read + 1 ?>
                    </a>
                <?php else: ?>
                    <button class="btn btn--wide" disabled>Все тома прочитаны!</button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <a class="btn btn--wide" href="<?= Url::to(['/site/login']) ?>" style="margin-top: 12px;">Войдите, чтобы сохранять прогресс</a>
        <?php endif; ?>

        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()): ?>
            <div style="margin-top: 16px;">
                <a href="<?= Url::to(['/admin/update', 'id' => $manga->ID_manga]) ?>" class="btn btn--sm btn--wide btn--secondary">
                    Редактировать в админке
                </a>
            </div>
        <?php endif; ?>
    </div>

    <article>
        <h1 class="mg__t"><?= Html::encode($manga->title) ?></h1>
        <p class="mg__m" style="color: var(--tx2); margin: 6px 0 16px;">
            <span class="tag" style="background:#2b2b31; padding: 4px 10px; border-radius: 4px;"><?= Html::encode($manga->type) ?></span>
            · Всего томов: <strong><?= $total ?></strong>
        </p>

        <!-- Прогресс чтения -->
        <div style="background: var(--sf); border: 1px solid var(--bd); border-radius: 8px; padding: 16px; margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span style="font-weight: 500;">Прогресс: прочитано <strong><?= $read ?></strong> из <strong><?= $total ?></strong> томов</span>
                <span style="color: var(--ac); font-weight: 700;"><?= $pct ?>%</span>
            </div>
            <div class="bar" role="progressbar" aria-valuenow="<?= $read ?>" aria-valuemin="0" aria-valuemax="<?= $total ?>">
                <span style="width: <?= $pct ?>%"></span>
            </div>
        </div>

        <div class="tabs" role="tablist" aria-label="Разделы">
            <button class="tab" role="tab" id="t-d" type="button" aria-selected="true" aria-controls="p-d">Описание</button>
            <button class="tab" role="tab" id="t-v" type="button" aria-selected="false" aria-controls="p-v">Список томов (<?= $total ?>)</button>
        </div>

        <section id="p-d" role="tabpanel" aria-labelledby="t-d" style="margin-top: 16px;">
            <h2 class="visually-hidden">Описание сюжета</h2>
            <div class="mg__d" style="line-height: 1.7; white-space: pre-line; color: #d0d0d5;">
                <?= Html::encode($manga->description) ?>
            </div>
        </section>

        <section id="p-v" role="tabpanel" aria-labelledby="t-v" hidden style="margin-top: 16px;">
            <p class="mg__m">Список всех томов манги «<?= Html::encode($manga->title) ?>»:</p>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(70px, 1fr)); gap: 10px; margin-top: 14px;">
                <?php for ($i = 1; $i <= $total; $i++): ?>
                    <div style="padding: 12px 6px; text-align: center; border-radius: 6px; border: 1px solid var(--bd); background: <?= $i <= $read ? 'rgba(46, 125, 50, 0.3)' : 'var(--sf)' ?>;">
                        <div style="font-weight: 700; font-size: 16px; color: <?= $i <= $read ? '#81c784' : 'var(--tx)' ?>;">
                            <?= $i ?>
                        </div>
                        <div style="font-size: 11px; color: var(--tx2); margin-top: 2px;">
                            <?= $i <= $read ? 'Прочитан' : 'Том' ?>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </section>
    </article>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabD = document.getElementById('t-d');
        const tabV = document.getElementById('t-v');
        const panD = document.getElementById('p-d');
        const panV = document.getElementById('p-v');

        if(tabD && tabV) {
            tabD.addEventListener('click', function() {
                tabD.setAttribute('aria-selected', 'true');
                tabV.setAttribute('aria-selected', 'false');
                panD.hidden = false;
                panV.hidden = true;
            });
            tabV.addEventListener('click', function() {
                tabD.setAttribute('aria-selected', 'false');
                tabV.setAttribute('aria-selected', 'true');
                panD.hidden = true;
                panV.hidden = false;
            });
        }
    });
</script>
