<?php
/** @var yii\web\View $this */
/** @var app\models\Manga $model */
/** @var app\models\Volume $volume */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Редактировать тайтл: ' . $model->title;
?>

<div class="wrap" style="padding-top: 16px;">
    <div class="adm-top">
        <h1 class="h2" style="margin: 0;">Редактирование: <?= Html::encode($model->title) ?></h1>
        <a href="<?= Url::to(['admin/index']) ?>" class="btn btn--sm btn--ghost">&larr; Назад в админку</a>
    </div>

    <div style="display: grid; grid-template-columns: 240px 1fr; gap: 24px; align-items: start;">
        <!-- Превью текущей обложки -->
        <div style="background: var(--sf); border: 1px solid var(--bd); border-radius: 10px; padding: 16px; text-align: center;">
            <p style="font-size: 13px; color: var(--tx2); margin-bottom: 12px; font-weight: 500;">Текущая обложка</p>
            <img src="<?= Html::encode($model->getCoverUrl()) ?>" 
                 alt="" 
                 style="width: 100%; max-height: 280px; object-fit: cover; border-radius: 6px;"
                 onerror="this.src='<?= Url::to('@web/images/logo.svg') ?>'">
            <div style="margin-top: 10px; font-size: 12px; color: var(--tx2); word-break: break-all;">
                <?= Html::encode(basename(str_replace('\\', '/', $model->cover))) ?>
            </div>
            <div style="margin-top: 14px;">
                <a href="<?= Url::to(['site/manga', 'id' => $model->ID_manga]) ?>" target="_blank" class="btn btn--sm btn--wide btn--ghost">
                    Открыть на сайте
                </a>
            </div>
        </div>

        <!-- Форма редактирования -->
        <div class="adm-form" style="max-width: 100%;">
            <?php $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
                'fieldConfig' => [
                    'options' => ['class' => 'form-group'],
                    'inputOptions' => ['class' => 'inp'],
                    'labelOptions' => ['style' => 'display:block; margin-bottom:6px; font-weight:500;'],
                    'errorOptions' => ['class' => 'invalid-feedback'],
                ],
            ]); ?>

            <?= $form->field($model, 'title')->textInput() ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <?= $form->field($model, 'type')->dropDownList([
                    'Манга' => 'Манга',
                    'Манхва' => 'Манхва',
                    'Маньхуа' => 'Маньхуа',
                    'Руманга' => 'Руманга',
                    'Комикс' => 'Комикс',
                ], ['class' => 'inp']) ?>

                <?= $form->field($model, 'total_volumes_input')->input('number', ['min' => 1])->label('Количество томов (всего)') ?>
            </div>

            <div style="background: rgba(255,255,255,0.03); padding: 14px; border-radius: 6px; margin-bottom: 18px; border: 1px dashed var(--bd);">
                <?= $form->field($model, 'imageFile')->fileInput(['class' => 'inp', 'style' => 'padding: 8px;'])->label('Загрузить новую обложку (опционально)') ?>
                <div style="margin-top: 10px;">
                    <?= $form->field($model, 'cover')->textInput()->label('Либо вручную укажите путь к обложке') ?>
                </div>
            </div>

            <?= $form->field($model, 'description')->textarea(['rows' => 8]) ?>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <?= Html::submitButton('Сохранить изменения', ['class' => 'btn']) ?>
                <a href="<?= Url::to(['admin/index']) ?>" class="btn btn--ghost">Отмена</a>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
