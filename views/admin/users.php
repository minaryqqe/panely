<?php
/** @var yii\web\View $this */
/** @var app\models\User[] $users */
/** @var app\models\Role[] $roles */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Управление пользователями';
?>

<div class="wrap" style="padding-top: 16px;">
    <div class="adm-top">
        <h1 class="h2" style="margin: 0;">Управление пользователями</h1>
        <a href="<?= Url::to(['admin/index']) ?>" class="btn btn--sm btn--ghost">&larr; В панель управления</a>
    </div>

    <!-- Вкладки управления -->
    <div class="adm-tabs">
        <a href="<?= Url::to(['admin/index']) ?>" class="adm-tab-link">Управление мангой</a>
        <a href="<?= Url::to(['admin/users']) ?>" class="adm-tab-link active">Пользователи и роли (<?= count($users) ?>)</a>
    </div>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Логин</th>
                    <th>Email</th>
                    <th>Текущая роль</th>
                    <th>Изменить роль</th>
                    <th style="text-align: right; width: 140px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <?php 
                        $isCurrent = $u->ID_user === Yii::$app->user->id;
                        $isAdmin = $u->isAdmin();
                    ?>
                    <tr>
                        <td><strong><?= $u->ID_user ?></strong></td>
                        <td>
                            <strong><?= Html::encode($u->username) ?></strong>
                            <?php if ($isCurrent): ?>
                                <span style="font-size: 11px; color: var(--ac); margin-left: 6px;">(Вы)</span>
                            <?php endif; ?>
                        </td>
                        <td><?= Html::encode($u->email) ?></td>
                        <td>
                            <span class="tag <?= $isAdmin ? 'tag--admin' : 'tag--user' ?>" style="padding: 4px 10px; border-radius: 4px;">
                                <?= $u->role ? Html::encode($u->role->name) : 'Не назначена' ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!$isCurrent): ?>
                                <form method="post" action="<?= Url::to(['admin/change-role', 'id' => $u->ID_user]) ?>" style="display: flex; gap: 8px; align-items: center;">
                                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                                    <select name="role_id" class="inp" style="min-height: 36px; padding: 0 10px; font-size: 13px; width: 130px;">
                                        <?php foreach ($roles as $r): ?>
                                            <option value="<?= $r->ID_role ?>" <?= $u->ID_role == $r->ID_role ? 'selected' : '' ?>>
                                                <?= Html::encode($r->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn--sm btn--secondary" style="min-height: 36px; padding: 0 12px; font-size: 13px;">
                                        Сменить
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="color: var(--tx2); font-size: 13px;">Текущий аккаунт</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right;">
                            <?php if (!$isCurrent): ?>
                                <?= Html::a('Удалить', ['admin/delete-user', 'id' => $u->ID_user], [
                                    'class' => 'btn btn--sm btn--danger',
                                    'style' => 'padding: 0 10px;',
                                    'data' => [
                                        'confirm' => "Вы действительно хотите удалить пользователя «{$u->username}»?",
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            <?php else: ?>
                                <span style="color: var(--tx2); font-size: 13px;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
