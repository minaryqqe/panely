<?php
/** @var yii\web\View $this */
/** @var app\models\Manga $manga */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $manga->title;
?>

<div class="wrap" style="padding-top: 20px;">
    <a href="<?= Url::to(['manga/index']) ?>" class="btn btn--sm btn--ghost" style="margin-bottom: 20px;">&larr; Назад в каталог</a>

    <div class="mg">
        <div class="mg__side">
            <img src="<?= Html::encode($manga->getCoverUrl()) ?>" 
                 class="img-fluid rounded shadow" 
                 alt="<?= Html::encode($manga->title) ?>"
                 onerror="this.src='<?= Url::to('@web/images/logo.svg') ?>'">
            
            <div style="margin-top: 14px;">
                <a href="<?= Url::to(['site/manga', 'id' => $manga->ID_manga]) ?>" class="btn btn--wide">
                    Страница тайтла
                </a>
            </div>
        </div>
        <div>
            <span class="tag" style="background: #2b2b31; padding: 4px 10px; border-radius: 4px;">
                <?= Html::encode($manga->type) ?>
            </span>
            <h1 class="h2" style="margin: 10px 0 16px;"><?= Html::encode($manga->title) ?></h1>
            <p style="color: var(--tx2); margin-bottom: 16px;">
                Всего томов: <strong><?= $manga->totalVolumes ?></strong>
            </p>
            <hr style="border: 0; border-top: 1px solid var(--bd); margin: 16px 0;">
            <h3 style="margin-bottom: 10px;">Описание:</h3>
            <p style="white-space: pre-line; line-height: 1.7; color: #d0d0d5;">
                <?= Html::encode($manga->description) ?>
            </p>
        </div>
    </div>
</div>
