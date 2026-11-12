<?php
/** @var yii\web\View $this */
/** @var app\models\SignupForm $model */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Регистрация нового пользователя';
?>

<div class="wrap" style="padding-top: 24px; padding-bottom: 40px;">
    <div style="max-width: 440px; margin: 0 auto; background: var(--sf); border: 1px solid var(--bd); border-radius: 12px; padding: 32px 28px; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
        
        <div style="text-align: center; margin-bottom: 24px;">
            <a href="<?= Url::to(['/site/index']) ?>" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; margin-bottom: 12px;">
                <img src="<?= Url::to('@web/images/logo.svg') ?>" alt="Panely" width="36" height="36">
                <span style="font-size: 24px; font-weight: 500; color: var(--tx);">Panely</span>
            </a>
            <h1 class="h2" style="margin: 0; font-size: 22px;">Создать аккаунт</h1>
            <p style="color: var(--tx2); font-size: 14px; margin-top: 6px;">Отслеживайте прогресс чтения и составляйте вишлист</p>
        </div>

        <?php $form = ActiveForm::begin([
            'id' => 'register-form',
            'fieldConfig' => [
                'options' => ['style' => 'margin-bottom: 18px;'],
                'inputOptions' => ['class' => 'inp', 'style' => 'width: 100%;'],
                'labelOptions' => ['style' => 'display: block; font-weight: 500; font-size: 14px; margin-bottom: 6px; color: var(--tx);'],
                'errorOptions' => ['style' => 'color: #ff8a80; font-size: 13px; margin-top: 4px;'],
            ],
        ]); ?>

        <?= $form->field($model, 'username')->textInput([
            'placeholder' => 'Придумайте логин',
            'autofocus' => true,
        ]) ?>

        <?= $form->field($model, 'email')->input('email', [
            'placeholder' => 'name@example.com',
        ]) ?>

        <?= $form->field($model, 'password')->passwordInput([
            'placeholder' => 'Не менее 6 символов',
        ]) ?>

        <div style="margin-top: 24px;">
            <?= Html::submitButton('Зарегистрироваться', [
                'class' => 'btn btn--wide',
                'name' => 'signup-button',
                'style' => 'font-size: 16px; font-weight: 500;',
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <div style="text-align: center; margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--bd); font-size: 14px; color: var(--tx2);">
            Уже зарегистрированы? 
            <a href="<?= Url::to(['/site/login']) ?>" style="color: var(--ac); font-weight: 500; text-decoration: underline;">
                Войти в аккаунт
            </a>
        </div>
    </div>
</div>
