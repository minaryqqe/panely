<?php
/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Вход в аккаунт';
?>

<div class="wrap" style="padding-top: 24px; padding-bottom: 40px;">
    <div style="max-width: 440px; margin: 0 auto; background: var(--sf); border: 1px solid var(--bd); border-radius: 12px; padding: 32px 28px; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
        
        <div style="text-align: center; margin-bottom: 24px;">
            <a href="<?= Url::to(['/site/index']) ?>" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; margin-bottom: 12px;">
                <img src="<?= Url::to('@web/images/logo.svg') ?>" alt="Panely" width="36" height="36">
                <span style="font-size: 24px; font-weight: 500; color: var(--tx);">Panely</span>
            </a>
            <h1 class="h2" style="margin: 0; font-size: 22px;">Вход в аккаунт</h1>
            <p style="color: var(--tx2); font-size: 14px; margin-top: 6px;">Войдите, чтобы сохранять прогресс чтения манги</p>
        </div>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'fieldConfig' => [
                'options' => ['style' => 'margin-bottom: 18px;'],
                'inputOptions' => ['class' => 'inp', 'style' => 'width: 100%;'],
                'labelOptions' => ['style' => 'display: block; font-weight: 500; font-size: 14px; margin-bottom: 6px; color: var(--tx);'],
                'errorOptions' => ['style' => 'color: #ff8a80; font-size: 13px; margin-top: 4px;'],
            ],
        ]); ?>

        <?= $form->field($model, 'username')->textInput([
            'placeholder' => 'Логин или Email',
            'autofocus' => true,
        ]) ?>

        <?= $form->field($model, 'password')->passwordInput([
            'placeholder' => 'Введите пароль',
        ]) ?>

        <div style="margin-bottom: 20px;">
            <?= $form->field($model, 'rememberMe')->checkbox([
                'template' => "<label class=\"ck\" style=\"display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; color: var(--tx2);\">{input} {label}</label>\n{error}",
                'labelOptions' => ['style' => 'font-weight: 400; color: var(--tx2); cursor: pointer;'],
            ]) ?>
        </div>

        <div>
            <?= Html::submitButton('Войти в систему', [
                'class' => 'btn btn--wide',
                'name' => 'login-button',
                'style' => 'font-size: 16px; font-weight: 500;',
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <!-- Подсказка с аккаунтами -->
        <div style="margin-top: 24px; padding: 12px 14px; background: rgba(255,255,255,0.03); border: 1px dashed var(--bd); border-radius: 8px; font-size: 12px; color: var(--tx2); line-height: 1.5;">
            <div style="font-weight: 500; color: var(--tx); margin-bottom: 4px;">Аккаунты для входа:</div>
            <div><strong>Администратор:</strong> <code>SeraLox</code> / <code>Main20022007</code></div>
            <div><strong>Пользователь:</strong> <code>ZombeDrug</code> / <code>123456</code></div>
        </div>

        <div style="text-align: center; margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--bd); font-size: 14px; color: var(--tx2);">
            Нет учётной записи? 
            <a href="<?= Url::to(['/site/register']) ?>" style="color: var(--ac); font-weight: 500; text-decoration: underline;">
                Зарегистрироваться
            </a>
        </div>
    </div>
</div>
