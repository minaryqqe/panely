<div align="center">

# 📚 Zhivnost Panely

### Персональный трекер манги, манхвы и манхуа

[![PHP](https://img.shields.io/badge/PHP-%3E%3D5.6-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Yii2](https://img.shields.io/badge/Yii2-Framework-00AEEF?style=for-the-badge)](https://www.yiiframework.com/)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-4-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)

*Веб-приложение для отслеживания прочитанной манги. Ведите личную коллекцию тайтлов, отмечайте прогресс по томам и управляйте статусами чтения.*

</div>

---

## ✨ Возможности

- 📖 **Трекинг прогресса** — отмечай прочитанные тома кнопкой «+1 том»
- 🔖 **Статусы чтения** — Читаю / Прочитано / Отложено / Буду читать
- 🔍 **Поиск и фильтрация** — по названию, типу и статусу
- 👤 **Профиль пользователя** — личная коллекция и статистика
- 🛡️ **Панель администратора** — CRUD тайтлов, управление пользователями
- 🖼️ **Загрузка обложек** — поддержка PNG, JPG, WEBP, SVG

---

## 🛠️ Технологии

| Компонент | Технология |
|-----------|-----------|
| Backend | PHP ≥ 5.6, Yii2 Framework |
| Frontend | Bootstrap 4, jQuery |
| База данных | MySQL (utf8mb4) |
| Шаблонизатор | Yii2 Views (PHP) |
| Зависимости | Composer |

---

## 🗄️ Структура базы данных

```
panely_site
├── manga              — тайтлы (название, описание, обложка, тип)
├── volume             — количество томов для каждого тайтла
├── reading_progress   — прогресс чтения пользователя (том + статус)
├── user               — зарегистрированные пользователи
├── role               — роли (1 = admin, остальные = user)
└── wishlist           — список желаемого пользователя
```

---

## 🚀 Установка

### 1. Требования

- OpenServer / XAMPP / WAMP с PHP и MySQL
- [Composer](https://getcomposer.org/)

### 2. Установка зависимостей

```bash
cd C:\OpenServer\domains\zhivnostpanely
composer install
```

### 3. Настройка базы данных

Откройте `config/db.php` и укажите параметры подключения:

```php
return [
    'class'    => 'yii\db\Connection',
    'dsn'      => 'mysql:host=localhost;dbname=panely_site',
    'username' => 'root',
    'password' => 'root',
    'charset'  => 'utf8mb4',
];
```

Создайте базу данных `panely_site` в MySQL и импортируйте дамп.

### 4. Права доступа к папкам

```bash
chmod 777 runtime/
chmod 777 web/assets/
chmod 777 web/images/
```

### 5. Открыть в браузере

```
http://zhivnostpanely/
```

> [!NOTE]
> В `config/web.php` включён `enablePrettyUrl`. Убедитесь, что сервер поддерживает `mod_rewrite` (Apache). Файл `web/.htaccess` уже настроен.

---

## 🗺️ Маршруты приложения

### Публичные страницы

| Маршрут | Описание |
|---------|----------|
| `/` | Главная — статистика, список тайтлов, «продолжить чтение» |
| `/site/manga?id=X` | Страница тайтла, выбор статуса чтения |
| `/site/progress` | Список всего прогресса чтения |
| `/site/search` | Поиск и фильтрация манги |
| `/site/profile` | Профиль пользователя с коллекцией |
| `/site/login` | Вход в систему |
| `/site/register` | Регистрация нового пользователя |
| `/site/inc-progress?id=X` | +1 том к прогрессу (AJAX) |

### Панель администратора `/admin/*`

| Маршрут | Описание |
|---------|----------|
| `/admin` | Дашборд — статистика и список манги |
| `/admin/create` | Добавить новый тайтл |
| `/admin/update?id=X` | Редактировать тайтл |
| `/admin/delete` | Удалить тайтл (POST) |
| `/admin/users` | Управление пользователями |
| `/admin/change-role` | Сменить роль пользователя |
| `/admin/delete-user` | Удалить пользователя |

---

## 👥 Роли пользователей

| Роль | `ID_role` | Возможности |
|------|-----------|-------------|
| 👑 Администратор | `1` | CRUD манги, управление пользователями, просмотр статистики |
| 👤 Пользователь | Остальные | Просмотр, отслеживание прогресса, список желаний |

> [!IMPORTANT]
> Нельзя снять права администратора с самого себя и удалить свою учётную запись.

---

## 📖 Прогресс чтения

### Статусы

```
📗 Читаю          — тайтл в процессе
✅ Прочитано      — завершено (устанавливается автоматически)
⏸️  Отложено       — на паузе
📌 Буду читать    — запланировано
```

### Логика кнопки «+1 том»

```
Нажатие → AJAX → /site/inc-progress?id=X
  ├── Если запись отсутствует → создаётся новая (статус «Читаю»)
  ├── current_volume++
  └── Если current_volume >= total_volumes → статус меняется на «Прочитано»
```

---

## 🖼️ Загрузка обложек

При создании/редактировании тайтла в админ-панели:

- Поддерживаемые форматы: **PNG, JPG, JPEG, WEBP, SVG**
- Файл сохраняется в `web/images/` с именем `manga_<uniqid>.ext`
- Если обложка не загружена — используется логотип сайта (`logo.svg`)
- Поддерживаются внешние URL (начинающиеся на `http://` или `https://`)

---

## 📁 Структура проекта

```
zhivnostpanely/
├── 📂 assets/           # Пакеты ресурсов (AppAsset)
├── 📂 commands/         # CLI-команды Yii2
├── 📂 config/           # Конфигурация приложения
│   ├── web.php          # Основной конфиг
│   ├── db.php           # Подключение к БД
│   └── params.php       # Параметры
├── 📂 controllers/      # Контроллеры
│   ├── SiteController.php
│   ├── AdminController.php
│   └── MangaController.php
├── 📂 models/           # Модели ActiveRecord
│   ├── Manga.php
│   ├── Volume.php
│   ├── ReadingProgress.php
│   ├── User.php
│   ├── Role.php
│   └── ...
├── 📂 views/            # Шаблоны
│   ├── admin/
│   ├── manga/
│   ├── site/
│   └── layouts/
├── 📂 web/              # Публичная папка (точка входа)
│   ├── index.php
│   ├── css/
│   ├── js/
│   └── images/
├── 📂 widgets/          # Виджеты (Alert)
├── composer.json
└── yii                  # CLI Yii2
```

---

## ⚠️ Важные замечания

> [!WARNING]
> Пароли хранятся в **открытом виде**. Перед деплоем в продакшн добавьте хеширование через `password_hash()` / `Yii::$app->security->generatePasswordHash()`.

> [!NOTE]
> Почта настроена в файловый транспорт (`useFileTransport = true`). Реальные письма не отправляются без дополнительной настройки SMTP.

> [!TIP]
> Дебаг-панель Yii2 и генератор кода **Gii** доступны только в режиме `YII_ENV_DEV` (локальная разработка).

---

<div align="center">

Сделано с ❤️ на **Yii2**

</div>
