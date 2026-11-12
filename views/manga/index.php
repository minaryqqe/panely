<?php
/** @var yii\web\View $this */
/** @var app\models\MangaSearch $searchModel */
/** @var app\models\Manga[] $mangas */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $typesList */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->title = 'Каталог манги';
?>

<div class="wrap" style="padding-top: 16px;">
    <h1 class="h2">Каталог манги (из БД)</h1>

    <!-- Форма фильтрации и поиска -->
    <div style="background: var(--sf); border: 1px solid var(--bd); border-radius: 8px; padding: 16px; margin-bottom: 24px;">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
        ]); ?>

        <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 220px;">
                <?= $form->field($searchModel, 'searchQuery')->textInput([
                    'class' => 'inp',
                    'placeholder' => 'Название или описание...',
                ])->label('Поиск') ?>
            </div>

            <div style="flex: 1; min-width: 160px;">
                <?= $form->field($searchModel, 'type')->dropDownList($typesList, [
                    'class' => 'inp',
                    'prompt' => 'Все типы',
                ])->label('Тип') ?>
            </div>

            <div style="display: flex; gap: 8px; margin-bottom: 16px;">
                <?= Html::submitButton('Искать', ['class' => 'btn btn--sm']) ?>
                <?= Html::a('Сбросить', ['index'], ['class' => 'btn btn--sm btn--ghost']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <!-- Результаты поиска -->
    <?php if (empty($mangas)): ?>
        <p style="text-align: center; color: var(--tx2); padding: 40px 0;">
            По вашему запросу ничего не найдено.
        </p>
    <?php else: ?>
        <ul class="grid">
            <?php foreach ($mangas as $manga): ?>
                <li>
                    <article class="card">
                        <a class="card__cover" href="<?= Url::to(['manga/view', 'id' => $manga->ID_manga]) ?>">
                            <img src="<?= Html::encode($manga->getCoverUrl()) ?>" 
                                 alt="<?= Html::encode($manga->title) ?>"
                                 width="400" height="600"
                                 loading="lazy"
                                 onerror="this.src='<?= Url::to('@web/images/logo.svg') ?>'">
                        </a>
                        <div class="card__body">
                            <span class="tag" style="background: #2b2b31; padding: 2px 8px; border-radius: 4px; align-self: flex-start;">
                                <?= Html::encode($manga->type) ?>
                            </span>
                            <h3 class="card__title">
                                <a href="<?= Url::to(['manga/view', 'id' => $manga->ID_manga]) ?>">
                                    <?= Html::encode($manga->title) ?>
                                </a>
                            </h3>
                            <p class="card__meta">Томов: <?= $manga->totalVolumes ?></p>
                            <div class="card__foot" style="margin-top: 10px;">
                                <a href="<?= Url::to(['manga/view', 'id' => $manga->ID_manga]) ?>" class="btn btn--sm btn--wide">
                                    Подробнее
                                </a>
                            </div>
                        </div>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Пагинация -->
        <div style="margin: 24px 0;">
            <?= LinkPager::widget([
                'pagination' => $dataProvider->pagination,
                'options' => ['class' => 'pg', 'style' => 'justify-content: center;'],
            ]) ?>
        </div>
    <?php endif; ?>
</div>
