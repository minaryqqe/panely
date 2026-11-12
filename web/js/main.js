// Panely - интерактивные элементы
document.addEventListener('DOMContentLoaded', function () {
    // Интерактивное увеличение прочитанных томов (+1 том) через AJAX
    document.body.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-inc]');
        if (!btn || btn.tagName !== 'A') return;

        // Если нажали на ссылку +1 том, пробуем сделать AJAX запрос
        e.preventDefault();
        const url = btn.getAttribute('href');
        if (!url) return;

        btn.style.opacity = '0.5';
        btn.style.pointerEvents = 'none';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                const card = btn.closest('.card') || btn.closest('.res') || btn.closest('article');
                if (card) {
                    // Обновляем текст текущего тома
                    const readSpan = card.querySelector('[data-read-out]');
                    if (readSpan) {
                        readSpan.textContent = data.current_volume;
                    }
                    // Обновляем прогресс-бар
                    const barFill = card.querySelector('.bar span');
                    if (barFill) {
                        barFill.style.width = data.percent + '%';
                    }
                    // Если все тома прочитаны
                    if (data.status === 'Прочитано') {
                        const tag = card.querySelector('.tag');
                        if (tag) {
                            tag.textContent = 'Прочитано';
                            tag.className = 'tag tag--done';
                        }
                        btn.outerHTML = '<button class="btn btn--sm" type="button" disabled>Завершено</button>';
                    }
                }
            } else {
                // Если требуется вход или произошла ошибка
                window.location.href = url;
            }
        })
        .catch(() => {
            // Фолбэк на стандартный переход
            window.location.href = url;
        })
        .finally(() => {
            btn.style.opacity = '1';
            btn.style.pointerEvents = '';
        });
    });

    // Живой фильтр по названию на главной странице (data-f="q")
    const homeSearch = document.getElementById('hq');
    if (homeSearch) {
        homeSearch.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            const cards = document.querySelectorAll('article[data-item]');
            cards.forEach(card => {
                const title = (card.getAttribute('data-title') || '').toLowerCase();
                const li = card.closest('li');
                if (li) {
                    li.style.display = (!query || title.includes(query)) ? '' : 'none';
                }
            });
        });
    }

    // Сортировка на главной странице (data-f="sort")
    const sortSelect = document.getElementById('hs');
    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            const val = this.value;
            const grid = document.querySelector('.grid');
            if (!grid) return;
            const items = Array.from(grid.querySelectorAll('li'));

            items.sort((a, b) => {
                const cardA = a.querySelector('[data-item]');
                const cardB = b.querySelector('[data-item]');
                if (!cardA || !cardB) return 0;

                if (val === 'name') {
                    const titleA = cardA.getAttribute('data-title') || '';
                    const titleB = cardB.getAttribute('data-title') || '';
                    return titleA.localeCompare(titleB, 'ru');
                } else if (val === 'progress') {
                    const readA = parseInt(cardA.getAttribute('data-read') || '0', 10);
                    const readB = parseInt(cardB.getAttribute('data-read') || '0', 10);
                    return readB - readA;
                }
                return 0; // default order
            });

            items.forEach(li => grid.appendChild(li));
        });
    }
});
