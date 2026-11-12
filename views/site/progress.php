<?php
/** @var yii\web\View $this */
/** @var app\models\ReadingProgress[] $progressList */
/** @var string $currentStatus */
/** @var string $currentType */
/** @var string $currentQ */

use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Мой прогресс чтения';
$allStatuses = [
    'all' => 'Все',
    'Читаю' => 'Читаю',
    'Прочитано' => 'Прочитано',
    'Отложено' => 'Отложено',
    'Буду читать' => 'Буду читать',
];
?>

<form class="msearch" role="search" action="<?= Url::to(['/site/search']) ?>" method="get">
    <svg class="ic" viewBox="0 0 24 24" aria-hidden="true"><use href="#i-search"></use></svg>
    <label class="visually-hidden" for="q-prog">Найти тайтл</label>
    <input id="q-prog" type="search" name="q" placeholder="Найти тайтл в базе...">
</form>

<h1 class="h2">Мой прогресс чтения</h1>

<!-- Панель поиска и выбора типа -->
<form method="get" action="<?= Url::to(['/site/progress']) ?>" id="prog-filter-form">
    <input type="hidden" name="status" value="<?= Html::encode($currentStatus) ?>" id="input-status">
    
    <div class="tools">
        <label class="visually-hidden" for="pq">Поиск по названию</label>
        <input class="inp" id="pq" type="search" name="q" value="<?= Html::encode($currentQ) ?>" 
               placeholder="Быстрый поиск по названию..." data-f="q">
               
        <label class="visually-hidden" for="pt">Тип</label>
        <select class="inp" id="pt" name="type" data-f="type" onchange="document.getElementById('prog-filter-form').submit()">
            <option value="all" <?= $currentType === 'all' ? 'selected' : '' ?>>Все типы</option>
            <option value="Манга" <?= $currentType === 'Манга' ? 'selected' : '' ?>>Манга</option>
            <option value="Манхва" <?= $currentType === 'Манхва' ? 'selected' : '' ?>>Манхва</option>
        </select>
        
        <?php if ($currentStatus !== 'all' || $currentType !== 'all' || !empty($currentQ)): ?>
            <a href="<?= Url::to(['/site/progress']) ?>" class="btn btn--sm btn--ghost">Сбросить</a>
        <?php endif; ?>
    </div>
</form>

<!-- Фильтрация по статусам чтения -->
<div class="chips" role="group" aria-label="Фильтр по статусу" style="margin-bottom: 20px;">
    <?php foreach ($allStatuses as $key => $label): ?>
        <?php 
            $isActive = ($currentStatus === $key);
            $url = Url::to(['/site/progress', 'status' => $key, 'type' => $currentType, 'q' => $currentQ]);
        ?>
        <a href="<?= $url ?>" 
           class="chip prog-status-btn <?= $isActive ? 'active' : '' ?>" 
           data-status-val="<?= Html::encode($key) ?>"
           style="text-decoration: none; <?= $isActive ? 'background:#fff; color:#111;' : '' ?>">
            <?= $label ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Список карточек прогресса из БД -->
<ul class="grid grid--list" id="progress-cards-list">
    <?php if (empty($progressList)): ?>
        <li style="grid-column: 1 / -1; text-align: center; color: var(--tx2); padding: 40px 0;">
            По заданным фильтрам тайтлов не найдено.
        </li>
    <?php else: ?>
        <?php foreach ($progressList as $item): ?>
            <?php 
                $manga = $item->manga;
                if (!$manga) continue;
                $total = $manga->totalVolumes;
                $read = (int)$item->current_volume;
                $status = $item->status;
                $pct = $total > 0 ? min(100, round(($read / $total) * 100)) : 0;
                $isDone = ($read >= $total && $total > 0);

                $statusKey = 'reading';
                if ($status === 'Прочитано') $statusKey = 'done';
                elseif ($status === 'Отложено') $statusKey = 'paused';
                elseif ($status === 'Буду читать') $statusKey = 'planned';
            ?>
            <li class="prog-item" 
                data-title="<?= Html::encode(mb_strtolower($manga->title)) ?>" 
                data-status="<?= Html::encode($status) ?>"
                data-type="<?= Html::encode($manga->type) ?>">
                
                <article class="card" data-item data-title="<?= Html::encode($manga->title) ?>" data-type="<?= Html::encode($manga->type) ?>"
                    data-status="<?= $statusKey ?>" data-read="<?= $read ?>" data-total="<?= $total ?>">
                    
                    <a class="card__cover" href="<?= Url::to(['/site/manga', 'id' => $manga->ID_manga]) ?>" tabindex="-1" aria-hidden="true">
                        <img src="<?= Html::encode($manga->getCoverUrl()) ?>" alt="" width="400" height="600" loading="lazy" onerror="this.src='<?= Url::to('@web/images/logo.svg') ?>'">
                    </a>
                    
                    <div class="card__body">
                        <h2 class="card__title">
                            <a href="<?= Url::to(['/site/manga', 'id' => $manga->ID_manga]) ?>"><?= Html::encode($manga->title) ?></a>
                        </h2>
                        <p class="card__meta"><?= Html::encode($manga->type) ?> · том <span data-read-out><?= $read ?></span> из <?= $total ?></p>
                        
                        <div class="bar" role="progressbar" aria-label="Прогресс чтения" aria-valuemin="0" aria-valuemax="<?= $total ?>" aria-valuenow="<?= $read ?>">
                            <span style="width:<?= $pct ?>%"></span>
                        </div>
                        
                        <div class="card__foot">
                            <span class="tag tag--<?= $statusKey ?>"><?= Html::encode($status) ?></span>
                            <?php if (!$isDone && $total > 0): ?>
                                <a class="btn btn--sm" href="<?= Url::to(['/site/inc-progress', 'id' => $manga->ID_manga]) ?>" data-inc>+1 том</a>
                            <?php else: ?>
                                <button class="btn btn--sm" type="button" disabled>Завершено</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>

<p class="empty" id="prog-empty-msg" style="display:none; text-align:center; padding:30px; color:var(--tx2);">
    Ничего не найдено по вашему фильтру.
</p>

<!-- Мгновенная клиентская фильтрация -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('pq');
    const typeSelect = document.getElementById('pt');
    const items = document.querySelectorAll('.prog-item');
    const emptyMsg = document.getElementById('prog-empty-msg');

    function filterItems() {
        const qVal = searchInput ? searchInput.value.trim().toLowerCase() : '';
        const tVal = typeSelect ? typeSelect.value : 'all';
        let visibleCount = 0;

        items.forEach(function(item) {
            const title = item.getAttribute('data-title') || '';
            const type = item.getAttribute('data-type') || '';

            const matchQ = !qVal || title.includes(qVal);
            const matchT = tVal === 'all' || type === tVal;

            if (matchQ && matchT) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (emptyMsg) {
            emptyMsg.style.display = (visibleCount === 0 && items.length > 0) ? 'block' : 'none';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterItems);
    }
});
</script>
