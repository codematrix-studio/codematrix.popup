<?php
$MESS['codematrix.popup_MAIN_TITLE'] = 'Всплывающие окна CODEMATRIX';

$MESS['codematrix.popup_LIST_TITLE'] = 'Всплывающие окна CODEMATRIX';
$MESS['codematrix.popup_LIST_CREATE_NEW_POPUP'] = 'Создать всплывающее окно';

$MESS['codematrix.popup_TAB_CONDITION_NAME'] = 'Условия показа';
$MESS['codematrix.popup_TAB_CONDITION_DESC'] = 'Условия показа всплывающего окна';
$MESS['codematrix.popup_TAB_SETTING_NAME'] = 'Оформление';
$MESS['codematrix.popup_TAB_SETTING_DESC'] = 'Оформление всплывающего окна';
$MESS['codematrix.popup_TAB_CONDITION_CONTACT'] = 'Сборщик контактов';
$MESS['codematrix.popup_TAB_CONTACT_DESC'] = 'Настройка сборщика контактов';
$MESS['codematrix.popup_TAB_BUTTON_SAVE'] = 'Сохранить';
$MESS['codematrix.popup_TAB_BUTTON_APPLY'] = 'Применить';
$MESS['codematrix.popup_TAB_BUTTON_ADD'] = 'Добавить окно';
$MESS['codematrix.popup_TAB_BUTTON_CANCEL'] = 'Отмена';
$MESS['codematrix.popup_COND_service_section'] = 'Служебные настройки';
$MESS['codematrix.popup_COND_main_section'] = 'Основные условия';

$MESS['codematrix.popup_VIEWS'] = 'Выберите шаблон';
$MESS['codematrix.popup_VIEW_video'] = 'Видео';
$MESS['codematrix.popup_VIEW_image'] = 'Баннер (картинка слева)';
$MESS['codematrix.popup_VIEWS_STEP_1'] = 'Шаг 1. Выберите тип всплывающего окна';
$MESS['codematrix.popup_VIEWS_STEP_2'] = 'Шаг 2. Выберите шаблон и цветовую схему';
$MESS['codematrix.popup_VIEWS_STEP_DEMO'] = 'Демонстрация окна';
$MESS['codematrix.popup_VIEWS_STEP_3'] = 'Шаг 3. Заполните необходимые поля и настройки';

$MESS['codematrix.popup_IMG_BLOCK_TITLE'] = 'Заменить картинку';
$MESS['codematrix.popup_IMG_BLOCK_ALTIMG'] = 'Кликните на картинку для замены';
$MESS['codematrix.popup_IMG_SHOWALL'] = 'Показать все';
$MESS['codematrix.popup_IMG_HIDEALL'] = 'Скрыть';
$MESS['codematrix.popup_IMG_BLOCK_DELIMG'] = 'Удалить картинку';
$MESS['codematrix.popup_IMG_BLOCK_UPLOADIMG'] = 'Загрузите картинку';

$MESS['codematrix.popup_TABCOND_TITLE_MAIN']='Общие условия';
$MESS['codematrix.popup_TABCOND_TITLE_EVENTS']='Показ по событиям';
$MESS['codematrix.popup_TABCOND_TITLE_ADDITIONAL']='Дополнительные условия';
$MESS['codematrix.popup_TABCOND_TITLE_SALE']='Для интернет-магазина';
$MESS['codematrix.popup_TABCOND_ACTIVE']='Активность';
$MESS['codematrix.popup_TABCOND_ACTIVE_HINT']='Служит для <b>включения/выключения</b> работы всплывающего окна';
$MESS['codematrix.popup_TABCOND_SERVICE_NAME']='Служебное имя';
$MESS['codematrix.popup_TABCOND_SERVICE_NAME_HINT']='Служит <b>для удобства работы</b> с модулем.<hr>Данное имя отображается только в админке';
$MESS['codematrix.popup_TABCOND_SORT']='Приоритет';
$MESS['codematrix.popup_TABCOND_SORT_HINT']='Служит для определения <b>какое окно показывать</b> при совпадении остальных условий показа.<hr>По умолчанию равен 500, чем ниже значение, тем выше приоритет срабатывания';
$MESS['codematrix.popup_TABCOND_SITE']='Показывать на сайтах';
$MESS['codematrix.popup_TABCOND_SITE_HINT']='Позволяет выбрать <b>на каком сайте</b> будет показываться всплывающее окно';
$MESS['codematrix.popup_TABCOND_GROUPS']='Группа пользователей';
$MESS['codematrix.popup_TABCOND_GROUPS_HINT']='Позволяет указать <b>каким группам пользователей</b> показывать всплывающее окно';
$MESS['codematrix.popup_TABCOND_ACTIVE_FROM']='Дата начала показа';
$MESS['codematrix.popup_TABCOND_ACTIVE_FROM_HINT']='Позволяет указать <b>дату с которой</b> начнет показываться всплывающее окно';
$MESS['codematrix.popup_TABCOND_ACTIVE_TO']='Дата окончания показа';
$MESS['codematrix.popup_TABCOND_ACTIVE_TO_HINT']='Позволяет указать <b>дату до которой</b> будет показываться всплывающее окно';
$MESS['codematrix.popup_TABCOND_WINDOW_POSITION']='Позиция окна';
$MESS['codematrix.popup_TABCOND_WINDOW_POSITION_VERTICAL']='Вертикальная';
$MESS['codematrix.popup_TABCOND_WINDOW_POSITION_VERTICAL_UP']='Сверху';
$MESS['codematrix.popup_TABCOND_WINDOW_POSITION_VERTICAL_DOWN']='Снизу';
$MESS['codematrix.popup_TABCOND_WINDOW_POSITION_HORIZONTAL']='Горизонтальная';
$MESS['codematrix.popup_TABCOND_WINDOW_POSITION_HORIZONTAL_LEFT']='Слева';
$MESS['codematrix.popup_TABCOND_WINDOW_POSITION_HORIZONTAL_RIGHT']='Справа';
$MESS['codematrix.popup_TABCOND_SHOWONLYPATH']='Маска включения';
$MESS['codematrix.popup_TABCOND_SHOWONLYPATH_HINT']='Служит для указания адресов разделов или страниц, на которых <b>будет показываться</b> всплывающее окно.<hr>Адрес страницы необходимо указывать без протокола (http) и без домена (мойсайт.рф), т.е. просто /contacts/ - адрес страницы контактов.<br>Также в обработке адреса используется знак *, который подразумевает любой символ в адресе.<br>Например /catalog/* - будет отображать всплывающее окно на всех страницах каталога.<br>Например, *?utm_source=vk* - для всех переходов по UTM меткам с ВК.<hr>Более подробная информация по настройке данного параметра доступна в <a href="https://codematrix.ru/documentation/popup/lesson61/" target="_blank">документации</a>.';
$MESS['codematrix.popup_TABCOND_SHOWONLYPATH_EXAMPLE']='Например, /catalog/';
$MESS['codematrix.popup_TABCOND_HIDEONLYPATH']='Маска исключения';
//$MESS['codematrix.popup_TABCOND_HIDEONLYPATH_HINT']='Служит для указания адресов разделов или страниц, на которых <b>НЕ будет показываться</b> всплывающее окно.<br><br>Адрес страницы необходимо указывать без протокола (http) и без домена (мойсайт.рф), т.е. просто /contacts/ - адрес страницы контактов.<hr>Более подробную информацию по настройке данного параметра вы можете найти в <a href="https://codematrix.ru/documentation/popup/" target="_blank">документации модуля</a>';
$MESS['codematrix.popup_TABCOND_HIDEONLYPATH_HINT']='Служит для указания адресов разделов или страниц, на которых <b>НЕ будет показываться</b> всплывающее окно.<hr>Адрес страницы необходимо указывать без протокола (http) и без домена (мойсайт.рф), т.е. просто /contacts/ - адрес страницы контактов.<br>Также в обработке адреса используется знак *, который подразумевает любой символ в адресе.<br>Например /catalog/* - будет отображать всплывающее окно на всех страницах каталога.<br>Например, *?utm_source=vk* - для всех переходов по UTM меткам с ВК.<hr>Более подробная информация по настройке данного параметра доступна в <a href="https://codematrix.ru/documentation/popup/lesson62/" target="_blank">документации</a>.';
$MESS['codematrix.popup_TABCOND_HIDEONLYPATH_EXAMPLE']='Например, /order/make/';
$MESS['codematrix.popup_TABCOND_PRIORITY_NOTSHOW']='Сначала маска исключения';
$MESS['codematrix.popup_TABCOND_PRIORITY_SHOW']='Сначала маска включения';
$MESS['codematrix.popup_TABCOND_AFTERSHOWCOUNTPAGES']='После просмотра N страниц';
$MESS['codematrix.popup_TABCOND_AFTERSHOWCOUNTPAGES_HINT']='Позволяет настроить отображение всплывающего окна только <b>после просмотра указанного числа страниц</b> сайта';
$MESS['codematrix.popup_TABCOND_AFTERTIMESECOND']='После S секунд на сайте';
$MESS['codematrix.popup_TABCOND_AFTERTIMESECOND_HINT']='Позволяет настроить отображение всплывающего окна <b>после пребывания</b> пользователя на страницах сайта <b>свыше указанного числа секунд</b>';
$MESS['codematrix.popup_TABCOND_TIMEINTERVAL']='Интервал показа';
$MESS['codematrix.popup_TABCOND_TIMEINTERVAL_HINT']='Служит для <b>указания времени работы</b> всплывающего окна.<br><br>Например, с 20:00 до 22:00';
$MESS['codematrix.popup_TABCOND_TIMEINTERVAL_FROM']='От';
$MESS['codematrix.popup_TABCOND_TIMEINTERVAL_TO']='До';
$MESS['codematrix.popup_TABCOND_ANCHORVISIBLE']='Имя якоря (a name=)';
//$MESS['codematrix.popup_TABCOND_ANCHORVISIBLE_HINT']='Поместите якорь на любую страницу сайта. Как только он <b>попадет в зону видимости</b> браузера, появится всплывающее окно.<br><br>Например, вы можете поместить якорь в конце какой-то статьи и как только пользователь дочитает статью до данного якоря, появится всплывающее окно.<hr>Более подробную информацию по настройке данного параметра вы можете найти в <a href="https://codematrix.ru/documentation/popup/" target="_blank">документации модуля</a>';
$MESS['codematrix.popup_TABCOND_ANCHORVISIBLE_HINT']='Поместите якорь на любую страницу сайта. Как только он <b>попадет в зону видимости</b> браузера, появится всплывающее окно.<br><br>Например, вы можете поместить якорь в конце какой-то статьи и как только пользователь дочитает статью до данного якоря, появится всплывающее окно.<hr>Более подробная информация по настройке данного параметра будет представлена в документации модуля в ближайшее время.';
$MESS['codematrix.popup_TABCOND_ONCLICKCLASSLINK']='Класс ссылки (a class=)';
//$MESS['codematrix.popup_TABCOND_ONCLICKCLASSLINK_HINT']='Добавьте ссылку с уникальным классом в любое место на вашем сайте. При нажатии на нее появится всплывающее окно.<br><br>Например, разместите ссылку на детальной странице товара, при нажатии на нее появится всплывающее окно.<hr>Более подробную информацию по настройке данного параметра вы можете найти в <a href="https://codematrix.ru/documentation/popup/" target="_blank">документации модуля</a>';
$MESS['codematrix.popup_TABCOND_ONCLICKCLASSLINK_HINT']='Добавьте ссылку с уникальным классом в любое место на вашем сайте. При нажатии на нее появится всплывающее окно.<br><br>Например, разместите ссылку на детальной странице товара, при нажатии на нее появится всплывающее окно.<hr>Более подробная информация по настройке данного параметра будет представлена в документации модуля в ближайшее время.';
$MESS['codematrix.popup_TABCOND_ALREADYGOING']='При попытке уйти с сайта';
$MESS['codematrix.popup_TABCOND_ALREADYGOING_HINT']='Всплывающее окно будет показываться только <b>при попытке пользователя уйти с сайта</b> (перемещение курсора мыши вверх окна браузера).<br><br>Данная функция <b>работает только на компьютерах!</b><hr>Более подробная информация по настройке данного параметра доступна в <a href="https://codematrix.ru/documentation/popup/lesson68/" target="_blank">документации</a>.';
$MESS['codematrix.popup_TABCOND_REPEATTIME']='Повторить показ пользователю через';
$MESS['codematrix.popup_TABCOND_REPEATTIME_HINT']='Служит для указания <b>через который промежуток</b> всплывающее окно <b>вновь будет показано</b> одному пользователю.<hr>Более подробная информация по настройке данного параметра доступна в <a href="https://codematrix.ru/documentation/popup/lesson67/" target="_blank">документации</a>.';
$MESS['codematrix.popup_TABCOND_REPEATTYPE_HOUR']='Час';
$MESS['codematrix.popup_TABCOND_REPEATTYPE_DAY']='День';
$MESS['codematrix.popup_TABCOND_REPEATTYPE_WEEK']='Неделя';
$MESS['codematrix.popup_TABCOND_REPEATTYPE_MONTH']='Месяц';
$MESS['codematrix.popup_TABCOND_REPEATTYPE_YEAR']='Год';
$MESS['codematrix.popup_TABCOND_SALECOUNTPRODUCT']='Если в корзине N товаров';
$MESS['codematrix.popup_TABCOND_SALECOUNTPRODUCT_HINT']='Всплывающее окно будет отображаться пользователю только если у него <b>в корзине столько товаров</b> (или более) сколько вы указали';
$MESS['codematrix.popup_TABCOND_SALEADDPRODUCT']='Добавить';
$MESS['codematrix.popup_TABCOND_SALESUMMBASKET']='Если сумма корзины выше N рублей';
$MESS['codematrix.popup_TABCOND_SALESUMMBASKET_HINT']='Всплывающее окно будет отображаться пользователю только если <b>сумма товаров в корзине равна</b> (или выше) суммы, которую вы указали';
$MESS['codematrix.popup_TABCOND_SALEIDPRODINBASKET']='Если в корзине есть указанный товар';
$MESS['codematrix.popup_TABCOND_SALEIDPRODINBASKET_HINT']='Всплывающее окно будет показано пользователю только если <b>в его корзине есть указанный товар</b> или товары';

$MESS['codematrix.popup_SUCCESS_UPDATE']='Окно ID успешно изменёно.';
$MESS['codematrix.popup_SUCCESS_ADD']='Окно ID успешно добавлено.';
$MESS['codematrix.popup_ERROR_SAVE']='Ошибка сохранения';

$MESS['codematrix.popup_TABLELIST_PAGINATOR']='Страница';
$MESS['codematrix.popup_TABLELIST_NAME']='Служебное имя';
$MESS['codematrix.popup_TABLELIST_SORT']='Приоритет';
$MESS['codematrix.popup_TABLELIST_TYPE']='Тип контента';
$MESS['codematrix.popup_TABLELIST_STAT_SHOW']='Окно показано';
$MESS['codematrix.popup_TABLELIST_STAT_TIME']='Общее время показа';
$MESS['codematrix.popup_TABLELIST_STAT_ACTION']='Целевое действие';
$MESS['codematrix.popup_TABLELIST_ACTIVE']='Активность';
$MESS['codematrix.popup_TABLELIST_SITES']='Сайты';
$MESS['codematrix.popup_TABLELIST_MASK_IN']='Маска включения';
$MESS['codematrix.popup_TABLELIST_MASK_OUT']='Маска исключения';

$MESS['codematrix.popup_NO']='Нет';
$MESS['codematrix.popup_YES']='Да';
$MESS['codematrix.popup_LOGIC_INFO']='ВНИМАНИЕ! У всех параметров работает логика - "И". Т.е. всплывающее окно будет показано только если выполнены ВСЕ отмеченные условия!<br>Исключение составляют параметры блока "Показ по событиям"';
$MESS['codematrix.popup_JSADM_FILES']='Добавить файлы';

$MESS['codematrix.popup_TABLE_EDIT']='Редактировать';
$MESS['codematrix.popup_TABLE_ADDITIONAL']='Дополнительно';
$MESS['codematrix.popup_TABLE_DELETE']='Удалить';
$MESS['codematrix.popup_TABLE_DELETE_CONFIRM']='Действительно удалить?';
$MESS['codematrix.popup_TABLE_COPY']='Копировать';
$MESS['codematrix.popup_TABLE_COPY_CONFIRM']='Сделать копию?';
$MESS['codematrix.popup_SAVE_ERROR']='Ошибка обновления';
$MESS['codematrix.popup_POPUP_EMPTY']='Окно не найдено';
$MESS['codematrix.popup_ERROR_CONTACT_TAB_SETTING']='Настройки данного окна нужны только для окна типа "Сборщик контактов"';
$MESS['codematrix.popup_HIDE_BLOCK']='Скрыть';
$MESS['codematrix.popup_SHOW_BLOCK']='Показать';
$MESS['codematrix.popup_CREATE_BLOCK']='Создать';
$MESS['codematrix.popup_APPLY']='Применить';
$MESS['codematrix.popup_NAMEISREQUIRED_BLOCK']='Название обязательно!';
$MESS['codematrix.popup_COLORTHEME_CREATESUCCESS']='Цветовая схема успешно создана';
$MESS['codematrix.popup_CUSTOMTEMPLATE_CREATESUCCESS']='Шаблон успешно создан';


$MESS['codematrix.popup_CONTACT_NAME']='Сохранение контактов';
$MESS['codematrix.popup_CONTACT_SEND_MAIL']='Отправлять письмо менеджеру';
$MESS['codematrix.popup_CONTACT_SEND_MAIL_HINT']='При успешном заполнении формы менеджеру будет отсылаться письмо с данными пользователя';
$MESS['codematrix.popup_CONTACT_EMAIL_TEMPLATE']='шаблон';
$MESS['codematrix.popup_CONTACT_SAVE_TO_LIST']='Добавлять Email в список адресов';
$MESS['codematrix.popup_CONTACT_SAVE_TO_LIST_HINT']='Email пользователя будет добавлен в список адресов модуля Email-маркетинг<br><br><a href="/bitrix/admin/sender_contact_admin.php?lang=ru" target="_blank">Перейти</a> к списку адресов';
$MESS['codematrix.popup_CONTACT_SAVE_TO_IBLOCK']='Сохранять заявку в инфоблок';
$MESS['codematrix.popup_CONTACT_SAVE_TO_IBLOCK_HINT']='При успешном заполнении формы информация о пользователе будет сохраняться в элемент инфоблока';
$MESS['codematrix.popup_CONTACT_SAVE_LIST_IBLOCK']='Инфоблок';
$MESS['codematrix.popup_CONTACT_REGISTER']="Регистрировать пользователя при отправке контактов";
$MESS['codematrix.popup_CONTACT_REGISTER_HINT']="Регистрация будет происходить только в случае сбора Email пользователя и использует стандартный шаблон автоматической регистрации по Email";
$MESS['codematrix.popup_EVENT_TYPE_NAME']='Заполнена форма codematrix_popup';
$MESS['codematrix.popup_POST_SEND']='Заполнена форма codematrix_popup';
$MESS['codematrix.popup_CONSENT_OUT']='Для активации функционала соглашений необходимо настроить как минимум одно <a href="/bitrix/admin/agreement_admin.php?lang='.LANG.'" target="_blank">соглашение</a>';

$MESS['codematrix.popup_TIMER_NAME']='Таймер';
$MESS['codematrix.popup_TIMER_ENABLE']='Отображать таймер';
$MESS['codematrix.popup_TIMER_ENABLE_HINT']='Таймер обратного отсчета для окна';
$MESS['codematrix.popup_TIMER_TIME']='Время до';
$MESS['codematrix.popup_TIMER_TIME_HINT']='popup_TIMER_NAME';
$MESS['codematrix.popup_TIMER_TIME_TITLE']='Нажмите для выбора даты и времени';
$MESS['codematrix.popup_TIMER_TEXT']='Текст таймера';
$MESS['codematrix.popup_TIMER_TEXT_DEFAULT']='Это предложение закончится через';
$MESS['codematrix.popup_TIMER_TEXT_HINT']='';
$MESS['codematrix.popup_TIMER_LEFT']='Слева';
$MESS['codematrix.popup_TIMER_RIGHT']='Справа';
$MESS['codematrix.popup_TIMER_TOP']='Сверху';
$MESS['codematrix.popup_TIMER_BOTTOM']='Снизу';


$MESS['codematrix.popup_ROULETTE_SORT']='Сортировка';
$MESS['codematrix.popup_ROULETTE_TEXT']='Текст';
$MESS['codematrix.popup_ROULETTE_COLOR']='Цвет';
$MESS['codematrix.popup_ROULETTE_RULE']='Результат';
$MESS['codematrix.popup_ROULETTE_RULE_INFO']='Подробнее';
$MESS['codematrix.popup_ROULETTE_RULE_INFO_BASIC']='В случае выпадение варианта «Выигрыш», пользователю будет отправлено письмо на основе выбранного выше шаблона.<br>В случае выпадения варианта «Ничего», пользователь увидит только оповещение о неудаче';
$MESS['codematrix.popup_ROULETTE_RULE_INFO_SALE']='<br><hr>В случае выпадения варианта из группы «Правила работы с корзиной», пользователю будет отправлено письмо на основе выбранного выше шаблона с купоном выбранного правила работы с корзиной со сроком действия который был введен выше';
$MESS['codematrix.popup_ROULETTE_RULES']='Правила работы с корзиной';
$MESS['codematrix.popup_ROULETTE_CONTROL']='Управление';
$MESS['codematrix.popup_ROULETTE_NOTHING']='Ничего';
$MESS['codematrix.popup_ROULETTE_WIN']='Выигрыш';
$MESS['codematrix.popup_ROULETTE_MINIMUM']='Минимум 4 элемента';
$MESS['codematrix.popup_ROULETTE_ADD']='Добавить';
$MESS['codematrix.popup_ROULETTE_BASIC']='Общее';


$MESS['codematrix.popup_DISCOUNT_IMG_1']='Логотип';
$MESS['codematrix.popup_DISCOUNT_IMG_2']='Картинка';



$MESS['codematrix.popup_JS_SELECT_IMG'] = 'Выберите изображение';
$MESS['codematrix.popup_ADD_COLOR_THEME'] = 'Добавить цветовую схему на основе ';
$MESS['codematrix.popup_ADD_TEMPLATE'] = 'Добавить шаблон на основе ';
$MESS['codematrix.popup_CONFIRM_ADD_COLOR_THEME'] = 'Вы действительно хотите создать дополнительную цветовую схему ';
$MESS['codematrix.popup_CONFIRM_ADD_TEMPLATE_ENTERNAME'] = 'Ввведите название шаблона';
$MESS['codematrix.popup_CONFIRM_ADD_COLOR_ENTERNAME'] = 'Ввведите цветовой схемы';
?>