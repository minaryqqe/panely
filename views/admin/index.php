<?php
/** @var yii\web\View $this */
/** @var app\models\MangaSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var int $totalManga */
/** @var int $totalUsers */
/** @var int $totalVolumes */
/** @var int $totalProgress */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Панель администратора';
$mangas = $dataProvider->getModels();
?>

<div class="wrap" style="padding-top: 16px;">
    <div class="adm-top">
        <h1 class="h2" style="margin: 0;">Панель администратора</h1>
        <div style="display: flex; gap: 10px;">
            <a href="<?= Url::to(['admin/create']) ?>" class="btn btn--sm">+ Добавить тайтл</a>
            <a href="<?= Url::to(['site/index']) ?>" class="btn btn--sm btn--ghost">На сайт</a>
        </div>
    </div>

    <!-- Статистика БД -->
    <div class="adm-stats">
        <div class="adm-stat">
            <div class="adm-stat__num"><?= $totalManga ?></div>
            <div class="adm-stat__lbl">Тайтлов в БД</div>
        </div>
        <div class="adm-stat">
            <div class="adm-stat__num"><?= $totalVolumes ?></div>
            <div class="adm-stat__lbl">Всего томов</div>
        </div>
        <div class="adm-stat">
            <div class="adm-stat__num"><?= $totalUsers ?></div>
            <div class="adm-stat__lbl">Пользователей</div>
        </div>
        <div class="adm-stat">
            <div class="adm-stat__num"><?= $totalProgress ?></div>
            <div class="adm-stat__lbl">Записей чтения</div>
        </div>
    </div>

    <!-- Вкладки управления -->
    <div class="adm-tabs">
        <a href="<?= Url::to(['admin/index']) ?>" class="adm-tab-link active">Управление мангой (<?= $totalManga ?>)</a>
        <a href="<?= Url::to(['admin/users']) ?>" class="adm-tab-link">Пользователи и роли (<?= $totalUsers ?>)</a>
    </div>

    <!-- Поиск по каталогу в админке -->
    <form method="get" action="<?= Url::to(['admin/index']) ?>" style="display: flex; gap: 10px; margin-bottom: 20px; max-width: 500px;">
        <input type="search" name="MangaSearch[searchQuery]" value="<?= Html::encode($searchModel->searchQuery) ?>" 
               class="inp" placeholder="Поиск по названию или описанию...">
        <button type="submit" class="btn btn--sm">Найти</button>
        <?php if (!empty($searchModel->searchQuery)): ?>
            <a href="<?= Url::to(['admin/index']) ?>" class="btn btn--sm btn--ghost">Сброс</a>
        <?php endif; ?>
    </form>

    <!-- Таблица тайтлов из БД -->
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th style="width: 60px;">Обложка</th>
                    <th>Название</th>
                    <th>Тип</th>
                    <th>Томов</th>
                    <th>Описание</th>
                    <th style="text-align: right; width: 220px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($mangas)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 24px; color: var(--tx2);">
                            В базе данных пока нет тайтлов или ничего не найдено.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($mangas as $manga): ?>
                        <tr>
                            <td><strong><?= $manga->ID_manga ?></strong></td>
                            <td>
                                <img src="<?= Html::encode($manga->getCoverUrl()) ?>" 
                                     alt="" 
                                     class="adm-thumb"
                                     onerror="this.src='<?= Url::to('@web/images/logo.svg') ?>'">
                            </td>
                            <td>
                                <strong>
                                    <a href="<?= Url::to(['site/manga', 'id' => $manga->ID_manga]) ?>" 
                                       target="_blank" style="text-decoration: underline;">
                                        <?= Html::encode($manga->title) ?>
                                    </a>
                                </strong>
                            </td>
                            <td>
                                <span class="tag" style="background:#2b2b31; padding: 4px 10px; border-radius: 4px;">
                                    <?= Html::encode($manga->type) ?>
                                </span>
                            </td>
                            <td><?= $manga->totalVolumes ?> томов</td>
                            <td style="max-width: 320px; color: var(--tx2);">
                                <?= Html::encode(mb_strimwidth($manga->description, 0, 90, '...')) ?>
                            </td>
                            <td style="text-align: right;">
                                <div class="adm-actions" style="justify-content: flex-end;">
                                    <a href="<?= Url::to(['site/manga', 'id' => $manga->ID_manga]) ?>" 
                                       target="_blank" class="btn btn--sm btn--ghost" style="padding: 0 10px;" title="Открыть на сайте">
                                        Сайт
                                    </a>
                                    <a href="<?= Url::to(['admin/update', 'id' => $manga->ID_manga]) ?>" 
                                       class="btn btn--sm btn--secondary" style="padding: 0 10px;">
                                        Ред.
                                    </a>
                                    <?= Html::a('Удалить', ['admin/delete', 'id' => $manga->ID_manga], [
                                        'class' => 'btn btn--sm btn--danger',
                                        'style' => 'padding: 0 10px;',
                                        'data' => [
                                            'confirm' => "Вы уверены, что хотите удалить тайтл «{$manga->title}» и всю связанную историю чтения?",
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Пагинация -->
    <div style="margin-top: 16px; margin-bottom: 30px;">
        <?= LinkPager::widget([
            'pagination' => $dataProvider->pagination,
            'options' => ['class' => 'pg', 'style' => 'justify-content: center;'],
        ]) ?>
    </div>
</div>
