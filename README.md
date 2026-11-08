# Почин — сервис ремонта ноутбуков

Веб-приложение на Laravel 13 (PHP 8.4, SQLite).

Клиент выбирает неисправность и мастера, оставляет заявку на ремонт, согласует стоимость и следит за историей заявки. Модератор ведёт заявки и мастеров, публикует статьи и проверяет комментарии.

## Возможности

- **Страницы сайта.** Общий Blade-макет с меню и подвалом, страницы «Главная», «О нас» и «Контакты». Контакты передаются в шаблон массивом из контроллера.
- **Данные из JSON.** Главная страница выводит записи из `public/articles.json`. Превью открывает полное изображение на странице `/galery/{index}`.
- **Регистрация и вход.** Валидация форм, CSRF-защита, токены Sanctum, выход с удалением токена и сбросом сессии. Закрытые страницы защищены посредником `auth:sanctum`.
- **Статьи.** Модель, миграция, фабрика на Faker и сидер. Список с пагинацией, поиском и фильтром по теме, создание, редактирование и удаление с проверкой полей и загрузкой обложки.
- **Роли и права.** Роли «читатель» и «модератор». `Gate::before` даёт модератору все права, `ArticlePolicy` ограничивает читателя просмотром, отказ показывается с понятным текстом.
- **Почта.** После создания статьи каждый модератор получает письмо (`App\Mail\ArticleCreated`).
- **Комментарии.** Новый комментарий ждёт проверки. Модератор принимает или отклоняет комментарии по одному или пачкой. На странице статьи видны только одобренные.
- **Заявки на ремонт.** Каталог мастеров, пошаговая форма заявки с фотографиями, личный кабинет, история изменений, согласование цены, рабочее место модератора.

## Запуск

Нужны PHP 8.4 с расширениями `pdo_sqlite`, `sqlite3`, `mbstring`, `fileinfo`, `gd`, `zip`, `openssl`, а также Composer 2 и Node.js 22.12+.

```powershell
Copy-Item .env.example .env
composer install
php artisan key:generate
New-Item database/database.sqlite -ItemType File
php artisan migrate --seed
php artisan storage:link
npm ci
npm run build
php artisan serve --host=127.0.0.1 --port=8010
```

Сайт откроется на <http://127.0.0.1:8010>. В следующий раз хватит последней команды.

В `php.ini` поставьте `upload_max_filesize=8M` и `post_max_size=40M`: на сайт загружаются обложки статей и фотографии к заявкам.

## Демонстрационные аккаунты

Их создаёт `database/seeders/RoleSeeder.php`.

| Роль | Email | Пароль |
| --- | --- | --- |
| Модератор | `moderator@pochin.test` | `RepairDemo!2026` |
| Читатель | `reader@pochin.test` | `ReaderDemo!2026` |

Пароли можно поменять до наполнения базы: переменные `DEMO_MODERATOR_PASSWORD` и `DEMO_READER_PASSWORD` в `.env`.

Чтобы сделать модератором уже зарегистрированного пользователя:

```powershell
php artisan pochin:moderator user@example.com
```

## Почта

По умолчанию `MAIL_MAILER=log`: письма не отправляются, а пишутся в `storage/logs/laravel.log`.

Чтобы отправлять письма через SMTP mail.ru:

1. В mail.ru откройте «Настройки» → «Безопасность» → «Пароли для внешних приложений» и создайте пароль с доступом к SMTP.
2. Заполните `.env`:

   ```dotenv
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mail.ru
   MAIL_PORT=465
   MAIL_USERNAME=адрес@mail.ru
   MAIL_PASSWORD=пароль_для_внешних_приложений
   MAIL_ENCRYPTION=ssl
   MAIL_FROM_ADDRESS="${MAIL_USERNAME}"
   MAIL_FROM_NAME="${APP_NAME}"
   ```

3. Выполните `php artisan config:clear`.

Адрес отправителя должен совпадать с `MAIL_USERNAME`: письма с чужого адреса mail.ru не примет. Значение `MAIL_ENCRYPTION=ssl` в `config/mail.php` превращается в схему `smtps`. Если почта недоступна, статья всё равно сохранится, а ошибка попадёт в журнал.

## Структура

```
app/
  Http/Controllers/   страницы, статьи, комментарии, вход, заявки
  Http/Requests/      правила проверки форм
  Mail/               письмо о новой статье
  Models/             Article, Comment, User, Role, Master, RepairBooking, RepairEvent
  Policies/           ArticlePolicy
  Providers/          AuthServiceProvider: Gate::before и шлюзы
config/site.php       контакты и данные для подвала
database/
  migrations/         таблицы
  factories/          фабрики с Faker
  seeders/            роли, демо-аккаунты, мастера, статьи
public/articles.json  данные для главной страницы и галереи
resources/views/      шаблоны Blade
routes/web.php        маршруты
tests/Feature/        автотесты
```

## Тесты

```powershell
php artisan test
```

Тесты используют базу в памяти и рабочую базу не трогают.

## Настройка

- Контакты и подпись в подвале: `config/site.php` и переменные `SITE_AUTHOR` и `SITE_GROUP` в `.env`.
- Направления ремонта и цены: `config/repair.php`.
- Русские сообщения проверки полей: `lang/ru/validation.php`.

Файл `.env` в репозиторий не попадает, `.env.example` — шаблон без паролей и ключей.
