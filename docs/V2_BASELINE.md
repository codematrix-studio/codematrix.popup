# Аудит исходной архитектуры CodeMatrix Popup V1

## Назначение

Документ фиксирует фактическую архитектуру и внешние контракты V1 перед миграцией на D7. Это контрольный перечень для проверки функциональной эквивалентности V2.

## Источники аудита

- Репозиторий V1: `codematrix-studio/codematrix.popup`.
- Базовый commit V1: `dcd0c5d4864e4a87c747ca86a4ca66dd246976f7`.
- Исторический исходный репозиторий: `ASDAFF/fly.popup`, зафиксированный commit `0c0e9e7b1ae8c286174219bd13bb227cdb5c06c9`.
- Аудит выполнен по содержимому GitHub. Локальный Bitrix-стенд и реальная база данных на этом этапе не проверялись.

## Точки входа

| Область | Файл / точка входа | Текущее состояние |
|---|---|---|
| Автозагрузка | `include.php` | `CModule::AddAutoloadClasses`, классы `popup` и `popupEdit` |
| Установка | `install/index.php` | Класс установщика наследуется от `CModule` |
| Основная логика | `classes/general/popup.php` | Глобальный класс `popup`, около 1 835 строк |
| Редактирование | `classes/general/popup_edit.php` | Глобальный класс `popupEdit`, наследуется от `popup` |
| ORM/вспомогательные операции | `lib/tools.php` | Пространство имён `CodeMatrix\\Popup`, класс `Tools`; часть методов использует старые API |
| Публичный компонент | `install/components/codematrix/popup.pro/class.php` | `CodeMatrixPopup extends CBitrixComponent` |
| Компонент таймера | `install/components/codematrix/popup.pro.timer/class.php` | `CodeMatrixPopupTimer extends CBitrixComponent` |
| Административный интерфейс | `admin/codematrix_popup.php` | Большая страница управления; смешаны HTML, обработка запросов и операции с БД |
| AJAX | `admin/codematrix_popup_ajax.php` | Ручная маршрутизация параметра `type` |
| Статистика | `admin/codematrix_popup_stat.php` | Страница выводит заголовок и текст-заглушку; расширять её в рамках миграции нельзя |
| Публичная вставка | событие `main:OnBeforeEndBufferContent` → `popup::insertPopups` | Зарегистрировано установщиком |
| JS/CSS | `install/js/codematrix.popup/`, `install/themes/.default/` | Публичные и административные ресурсы |
| Шаблоны | `install/components/codematrix/popup.pro/templates/` | PHP-шаблоны, CSS, цветовые темы, AJAX-файлы и языковые файлы |

## Состав основного класса

Публичные и значимые методы `popup`:

- `getConsentList()`, `getAgreements()` — согласия пользователей.
- `getTypes()` — список типов окон и их параметров.
- `getTemplates()` — список шаблонов и цветовых схем.
- `getConditions()` — набор условий показа.
- `searchinMailList()`, `insertToMailList()` — работа со списком рассылки.
- `getSetting()` — чтение настроек окна.
- `getCoupon()` — получение купона.
- `convertTimeFromSecond()` — форматирование времени.
- `insertPopups()` — вставка окон в публичную страницу.
- `GetBasketInfo()` — сведения о корзине.
- `getAvailablePopups()` — подбор окон по условиям показа.
- `getComponentResult()`, `getHTMLByPopup()`, `getComponentPath()` — подготовка публичного компонента и HTML.
- `setStatistic()` — изменение счётчиков статистики.

Значимые методы `popupEdit`:

- `editPopup()` — создание и изменение окна.
- `editFromTableList()` — изменение данных из списка.
- `getAllPopups()` — получение списка окон.
- `setTemplate()` — создание пользовательского шаблона.
- `setColorTheme()` — создание пользовательской цветовой темы.
- `CopyPopup()` — копирование окна.

Эти методы — отправная точка инвентаризации. До переноса необходимо дополнительно найти все места вызова, включая шаблоны и JS/AJAX.

## Собственные таблицы и операции

### `codematrix_popup`

Поля по установщику V1:

- `id` — первичный ключ;
- `sort`, `active`, `name`, `type`;
- `stat_show`, `stat_time`, `stat_action`;
- `settings` — сериализованный массив настроек;
- `cont_iblock`, `cont_posttemplate`.

Используется для хранения окон, условий и настроек, а также счётчиков статистики.

### `codematrix_popup_add_colors`

Поля: `id`, `template`, `name`. Используется для пользовательских цветовых тем.

### `codematrix_popup_add_templates`

Поля: `id`, `name`, `type`, `template`. Используется для пользовательских шаблонов.

### Найденные группы операций

- Основной класс: чтение настроек, шаблонов и тем; выборка активных окон; получение HTML/данных компонента; обновление счётчиков.
- Класс редактирования: добавление, изменение, выборка и копирование окон; создание пользовательских шаблонов и цветовых тем.
- Административная страница: отдельные выборки и операции изменения/удаления строк таблицы окон.
- Установщик: создание и удаление трёх таблиц.

Точный перечень условий каждого запроса и соответствие методов ORM будут оформлены отдельной таблицей перед реализацией ORM.

## Используемые API и зависимости

В V1 уже используются отдельные D7 API:

- `Bitrix\\Main\\Loader`;
- `Application`, `Context`, `Request`;
- `Web\\Cookie`;
- `Localization\\Loc`;
- `Page\\Asset`;
- `UserConsent`;
- ORM отдельных модулей Битрикс, в частности `Bitrix\\Sale\\Internals`.

Одновременно остаются старые API:

- `$DB` и прямые SQL-запросы к таблицам модуля;
- `CUser`, `CIBlockElement`, `CIBlockProperty`, `CGroup`;
- `CEvent`, `CEventType`, `CEventMessage`;
- `CModule::AddAutoloadClasses` и `CModule::IncludeModule`;
- `RegisterModuleDependences` / `UnRegisterModuleDependences`;
- `CJSCore`, `CUtil`, глобальные `$APPLICATION` и `$USER`.

Наличие старого API само по себе не означает, что его нужно механически удалить. Для каждой замены проверяется актуальный D7-аналог, совместимость с целевой версией Битрикс и сохранение результата/побочных эффектов.

## Внешние контракты, которые нельзя менять

- Идентификатор модуля: `codematrix.popup`.
- Компоненты: `codematrix:popup.pro`, `codematrix:popup.pro.timer`.
- Параметр публичного компонента: `ID_POPUP`; дополнительные параметры и режимы должны быть инвентаризированы.
- Событие вставки: `main:OnBeforeEndBufferContent`.
- Типы окон и их коды: `banner`, `video`, `action`, `social`, `contact`, `share`, `html`, `age`, `coupon`, `roulette`, `discount` и другие значения, найденные в конфигурации типов.
- Ключи массива `settings`, значения полей таблиц и коды почтовых событий.
- AJAX-параметр маршрутизации `type` и существующие значения действий: `getPopups`, `getBasket`, `getTemplatePath`, `getHTML`, `statisticShow`, `statisticTime`, `statisticAction`.
- Путь и имя поставляемой библиотеки jQuery: `install/js/codematrix.popup/jquery-3.8.0.min.js`. Файл принадлежит пользовательскому набору ресурсов и не подлежит замене в рамках миграции.
- Пути шаблонов, CSS-классы и JS-селекторы.

## Важные ограничения миграции

1. Не менять таблицы и сериализованные данные до завершения инвентаризации всех читателей/писателей.
2. Не переписывать компонент на другой механизм только ради формального «D7»: штатный компонентный API Bitrix остаётся действующим.
3. Не переписывать административный HTML и клиентские скрипты, если это не требуется для переноса серверного слоя.
4. Не менять AJAX-ответы и порядок/тип значений в них.
5. Не менять имена событий, почтовых типов и шаблонов.
6. Не удалять старые классы до проверки внешнего использования; при необходимости оставить слой совместимости.
7. Не объединять функциональные исправления с миграцией. Найденные дефекты фиксировать отдельно и принимать решение после подтверждения влияния на V1.
8. Не менять исходный репозиторий.

## Открытые вопросы, требующие проверки на стенде

- Фактическая схема трёх таблиц на установленном V1 и соответствие SQL установщика.
- Версия главного модуля `main`, PHP и доступность D7-классов/методов на целевом окружении.
- Поведение обновления поверх установленной V1 и сохранение пользовательских шаблонов/тем.
- Точные форматы AJAX-ответов и потребители в JS.
- Фактическая регистрация автозагрузки класса `CodeMatrix\\Popup\\Tools`.
- Зависимость функций купонов/корзины от установленных версий `sale` и `catalog`.
- Работа событий и почтовых шаблонов на нескольких сайтах.

## Статус

Первичный просмотр GitHub-исходников завершён. Полная карта вызовов, схема запросов, тесты и проверка на стенде ещё не завершены. До их завершения нельзя считать архитектурный аудит полным и нельзя выполнять массовый перенос.


## Детализация аудита слоя данных (20.11.2026)

### Точная схема по установщику

Поля и SQL-типы, объявленные в `install/index.php`:

| Таблица | Поле | SQL-тип / ограничение |
|---|---|---|
| `codematrix_popup` | `id` | `INT NOT NULL AUTO_INCREMENT PRIMARY KEY` |
| | `sort` | `INT NOT NULL` |
| | `active` | `VARCHAR(1) DEFAULT NULL` |
| | `name` | `VARCHAR(50) DEFAULT NULL` |
| | `type` | `VARCHAR(30) NOT NULL` |
| | `stat_show` | `INT DEFAULT NULL` |
| | `stat_time` | `INT DEFAULT NULL` |
| | `stat_action` | `INT DEFAULT NULL` |
| | `settings` | `TEXT NOT NULL` |
| | `cont_iblock` | `INT DEFAULT NULL` |
| | `cont_posttemplate` | `INT DEFAULT NULL` |
| `codematrix_popup_add_colors` | `id` | `INT NOT NULL AUTO_INCREMENT PRIMARY KEY` |
| | `template` | `VARCHAR(50) NOT NULL` |
| | `name` | `VARCHAR(50) DEFAULT NULL` |
| `codematrix_popup_add_templates` | `id` | `INT NOT NULL AUTO_INCREMENT PRIMARY KEY` |
| | `name` | `VARCHAR(50) NOT NULL` |
| | `type` | `VARCHAR(50) NOT NULL` |
| | `template` | `TEXT NOT NULL` |

Это схема из кода установщика, а не подтверждённая схема работающей базы. ORM-классы нельзя считать готовыми до сверки с БД стенда.

### Найденные обращения к собственным таблицам

| Файл / метод | Таблица | Операции |
|---|---|---|
| `popup::getCustomTemplates()` | `codematrix_popup_add_templates` | SELECT, чтение и `unserialize(template)` |
| `popup::getCustomColors()` | `codematrix_popup_add_colors` | SELECT |
| `popup::getSetting()` | `codematrix_popup` | SELECT по ID, `unserialize(settings)` |
| `popup::getAvailablePopups()` | `codematrix_popup` | SELECT активных записей, сортировка по `sort`, `unserialize(settings)` |
| `popup::getComponentResult()` | `codematrix_popup` | SELECT по ID, чтение сериализованных настроек |
| `popup::setStatistic()` | `codematrix_popup` | SELECT и UPDATE одного из трёх счётчиков |
| `popupEdit::editPopup()` | `codematrix_popup` | INSERT/UPDATE, запись сериализованного массива |
| `popupEdit::editFromTableList()` | `codematrix_popup` | SELECT/UPDATE, изменение полей и сериализованных условий |
| `popupEdit::getAllPopups()` | `codematrix_popup` | SELECT всех записей, чтение сериализованных настроек |
| `popupEdit::setTemplate()` | `codematrix_popup_add_templates` | INSERT с временным значением, затем UPDATE сериализованного шаблона |
| `popupEdit::setColorTheme()` | `codematrix_popup_add_colors` | INSERT пользовательской темы |
| `popupEdit::CopyPopup()` | `codematrix_popup` | SELECT полей и INSERT копии |
| `admin/codematrix_popup.php` | требуется отдельная детализация | Административная страница содержит дополнительные обращения; перечень будет дополнен после полного разбора файла |

### Выводы для ORM

- Имена таблиц и колонок должны остаться прежними.
- `settings` и `template` содержат PHP-сериализацию. Нельзя заменять её JSON или менять формат в рамках D7-миграции.
- `setStatistic()` принимает имя поля, но ограничивает его списком `stat_show`, `stat_time`, `stat_action`. Это ограничение необходимо сохранить.
- Создание пользовательского шаблона состоит из двух последовательных операций INSERT/UPDATE; перенос должен сохранить корректность идентификатора и итогового сериализованного содержимого.
- Копирование окна не должно копировать первичный ключ и должно сохранять прежний набор копируемых полей.
- Установщик и ORM должны согласованно описывать nullable-поля и типы. Фактическая БД стенда остаётся источником окончательной проверки.

### Автозагрузка

`include.php` регистрирует только глобальные классы `popup` и `popupEdit` через `CModule::AddAutoloadClasses`. Файл `lib/tools.php` объявляет `CodeMatrix\\Popup\\Tools`, но явной регистрации пространства имён в `include.php` не обнаружено. Необходимо проверить фактическую загрузку этого класса на стенде и затем настроить штатную D7-регистрацию пространства имён. Сам факт расположения файла в `lib/` не принимается за доказательство корректной автозагрузки.

### AJAX-контракт

Обработчик `admin/codematrix_popup_ajax.php` читает параметры `type`, `pageUrl`, `site`, `dateUser`, `popupIds`, `popupId`, `popupTime`. Существующие действия:

- `getPopups`;
- `getBasket`;
- `getTemplatePath`;
- `getHTML`;
- `statisticShow`;
- `statisticTime`;
- `statisticAction`.

Для JSON-ответов сейчас используется `CUtil::PhpToJSObject()` с последующей заменой одинарных кавычек на двойные. Это текущая реализация, а не рекомендуемый формат. При миграции нужно отдельно проверить реальные ответы и JS-потребителей; допустимо исправление только при доказанном сохранении внешнего формата и корректности данных, включая строки с кавычками.

### Параметры целевого окружения

- PHP: `8.2.30`.
- Bitrix main: `26.150.0`.
- Данные предоставлены пользователем с тестового стенда 20.11.2026.
- Доступ к самой файловой системе и БД тестового стенда из текущей проверки не выполнялся.


## Административная страница и компонентные контракты (20.11.2026)

### SQL в административном списке

Файл `admin/codematrix_popup.php` использует таблицу, имя которой возвращает `popup::getTableSetting()` (в V1 это `codematrix_popup`). Обнаружены дополнительные операции:

- SELECT всех записей для действия над всеми элементами;
- DELETE записи по ID;
- SELECT записи по ID перед активацией/деактивацией;
- UPDATE `active` и сериализованного `settings` с синхронизацией `settings['condition']['active']`;
- SELECT списка с сортировкой по выбранному столбцу и направлению, затем постраничная навигация `CAdminResult`.

Эти сценарии являются частью административного контракта и должны быть переведены на D7 без изменения массовых действий, ошибок списка, сортировки и пагинации. Сам административный интерфейс и его HTML не требуется переписывать ради миграции.

### Компоненты

- `codematrix:popup.pro`: параметр `ID_POPUP` (тип STRING); также объявлен параметр `USER_CONSENT`. При пустом `ID_POPUP` код компонента подставляет значение `1`.
- Компонент использует режим `MODE=GET_PATH` внутри выполнения; это фактический внутренний режим, который нельзя потерять при рефакторинге.
- `codematrix:popup.pro.timer`: принимает `TITLE` и `TIME` через `arParams`; отдельный файл `.parameters.php` для него нужно проверить в составе полного дерева компонентов.
- Компонент `popup.pro` вызывает глобальные классы `popup` и использует `CEvent::Send`, Cookie и старые API инфоблоков/пользователей в отдельных сценариях. Сохранить компонентный контракт и результат его шаблонов.

### AJAX и клиентские потребители

Есть два разных AJAX-контекста, их нельзя смешивать:

1. Публичный компонентный адрес `/bitrix/components/codematrix/popup.pro/ajax.php` подключает общий обработчик `admin/codematrix_popup_ajax.php`. Публичный JS `script_public.js` использует действия `getPopups`, `getBasket`, `getTemplatePath`, `getHTML`, `statisticShow`, `statisticTime`, `statisticAction`.
2. Административный JS `script.js` обращается к `/bitrix/admin/codematrix_popup.php?ajax=y&command=...`. Зафиксированы команды `get_img`, `gettemplate`, `gettemplatepath`, `add_custom_template`, `add_custom_colortheme`, `del_img`.

При переносе обработчиков нужно сохранить оба URL и оба набора команд. Подмена их одним новым контроллером без совместимого маршрута не допускается.

### Подключение jQuery

Административная страница регистрирует расширение `codematrix_popup_jquery` из `/bitrix/js/codematrix.popup/jquery-3.8.0.min.js`, а основное расширение `codematrix_popup` указывает зависимость от него. Файл jQuery в репозитории — `install/js/codematrix.popup/jquery-3.8.0.min.js`; его содержимое в рамках миграции не менять.
