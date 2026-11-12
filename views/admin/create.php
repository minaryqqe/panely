<?php
/** @var yii\web\View $this */
/** @var app\models\Manga $model */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Добавить тайтл в БД';
?>

<div class="wrap" style="padding-top: 16px;">
    <div class="adm-top">
        <h1 class="h2" style="margin: 0;">Добавление нового тайтла</h1>
        <a href="<?= Url::to(['admin/index']) ?>" class="btn btn--sm btn--ghost">&larr; Назад в админку</a>
    </div>

    <div class="adm-form">
        <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
            'fieldConfig' => [
                'options' => ['class' => 'form-group'],
                'inputOptions' => ['class' => 'inp'],
                'labelOptions' => ['style' => 'display:block; margin-bottom:6px; font-weight:500;'],
                'errorOptions' => ['class' => 'invalid-feedback'],
            ],
        ]); ?>

        <?= $form->field($model, 'title')->textInput(['placeholder' => 'Например: Человек-бензопила']) ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <?= $form->field($model, 'type')->dropDownList([
                'Манга' => 'Манга',
                'Манхва' => 'Манхва',
                'Маньхуа' => 'Маньхуа',
                'Руманга' => 'Руманга',
                'Комикс' => 'Комикс',
            ], ['class' => 'inp']) ?>

            <?= $form->field($model, 'total_volumes_input')->input('number', ['min' => 1, 'placeholder' => 'Например: 24'])->label('Количество томов (всего)') ?>
        </div>

        <div style="background: rgba(255,255,255,0.03); padding: 14px; border-radius: 6px; margin-bottom: 18px; border: 1px dashed var(--bd);">
            <?= $form->field($model, 'imageFile')->fileInput(['class' => 'inp', 'style' => 'padding: 8px;'])->label('Загрузить обложку с компьютера') ?>
            <p style="font-size: 13px; color: var(--tx2); margin: 6px 0 0;">Поддерживаются форматы JPG, PNG, WEBP. Файл будет автоматически сохранен в каталог web/images.</p>

            <div style="margin-top: 12px;">
                <?= $form->field($model, 'cover')->textInput(['placeholder' => 'Или имя существующего файла (например: berserk.jpg)'])->label('Либо укажите имя файла / ссылку') ?>
            </div>
        </div>

        <?= $form->field($model, 'description')->textarea(['rows' => 6, 'placeholder' => 'Сюжет и описание тайтла...']) ?>

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <?= Html::submitButton('Сохранить тайтл в БД', ['class' => 'btn']) ?>
            <a href="<?= Url::to(['admin/index']) ?>" class="btn btn--ghost">Отмена</a>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
