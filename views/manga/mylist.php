<?php
/** @var app\models\ReadingProgress[] $progressList */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Мой список чтения';
?>

<div class="container my-4">
    <h1>Мой список чтения</h1>

    <?php if (empty($progressList)): ?>
        <p class="text-muted mt-3">Ваш список чтения пока пуст.</p>
    <?php else: ?>
        <table class="table table-hover align-middle mt-4">
            <thead class="table-dark">
                <tr>
                    <th>Обложка</th>
                    <th>Название</th>
                    <th>Тип</th>
                    <th>Текущий том</th>
                    <th>Статус</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($progressList as $item): ?>
                    <?php 
                        $manga = $item->manga;
                        $coverName = basename(str_replace('\\', '/', $manga->cover));
                        $imgUrl = Yii::getAlias('@web/images/') . $coverName;
                    ?>
                    <tr>
                        <td style="width: 70px;">
                            <img src="<?= Html::encode($imgUrl) ?>" 
                                 style="height: 60px; object-fit: cover;" 
                                 class="rounded" 
                                 alt="">
                        </td>
                        <td><strong><?= Html::encode($manga->title) ?></strong></td>
                        <td><span class="badge bg-secondary"><?= Html::encode($manga->type) ?></span></td>
                        <td><?= Html::encode($item->current_volume) ?></td>
                        <td>
                            <span class="badge bg-<?= $item->status === 'Прочитано' ? 'success' : ($item->status === 'Читаю' ? 'info' : 'warning') ?>">
                                <?= Html::encode($item->status) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= Url::to(['manga/view', 'id' => $manga->ID_manga]) ?>" 
                               class="btn btn-sm btn-outline-primary">
                                Открыть
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>