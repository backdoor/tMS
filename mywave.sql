SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_accounts`
--

CREATE TABLE IF NOT EXISTS `wave_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID аккаунта',
  `avatar` varchar(72) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Аватарка пользователя',
  `username` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Логин',
  `fullname` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Полное имя (ФИО)',
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Пароль пользователя',
  `email` varchar(120) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Почтовый ящик пользователя',
  `uoid` int(2) NOT NULL DEFAULT '0' COMMENT 'Регистрация через OpenID: 0-нет, 1-OpenID, 2-myOpenID, 3-Google, 4-Facebook, 5-Twitter, 6-Yahoo, 7-Vkontakte, 8-Yandex, 9-Mail.ru, 10-Rambler, 11-Loginza',
  `tbid` int(11) NOT NULL DEFAULT '0' COMMENT 'Тип или ID бота, если 0 - то это человек',
  `lastlogin` int(11) NOT NULL DEFAULT '0' COMMENT 'Дата последнего входа (обновляется AJAX)',
  `dateReg` int(11) NOT NULL DEFAULT '0' COMMENT 'Дата регистрации',
  `autoLoginToken` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT 'Код Token для автологина',
  `blocked` int(1) NOT NULL DEFAULT '0' COMMENT 'Пользователь заблокирован',
  PRIMARY KEY (`id`),
  UNIQUE KEY `img` (`avatar`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Аккаунты' AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_bots`
--

CREATE TABLE IF NOT EXISTS `wave_bots` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID бота',
  `uid` int(11) NOT NULL COMMENT 'ID бота в аккаунтах',
  `botname` varchar(64) NOT NULL COMMENT 'Название бота',
  `description` varchar(256) NOT NULL COMMENT 'Описание',
  `url` varchar(256) NOT NULL COMMENT 'Путь к боту',
  `events` varchar(256) NOT NULL DEFAULT '["1"]' COMMENT 'События (можно через ","), для "незагрузки" роботов',
  `created` int(12) NOT NULL COMMENT 'Дата внесения',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Боты системы' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_comments`
--

CREATE TABLE IF NOT EXISTS `wave_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL DEFAULT '0' COMMENT 'Уровень сообщения волны',
  `id_usr` int(11) NOT NULL COMMENT 'ID пользователя',
  `id_wave` int(11) NOT NULL COMMENT 'ID волны',
  `id_com` int(11) NOT NULL DEFAULT '0' COMMENT 'ID комментария в волне',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT 'Статус комента: 0-показывать, 1-спам, 2-пометка удаления, 3-удален',
  `comment` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Сообщение в волне, комментарий',
  `created` int(11) NOT NULL DEFAULT '0' COMMENT 'Дата создания сообщения',
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`,`id`),
  FULLTEXT KEY `comment` (`comment`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Содаржание волн' AUTO_INCREMENT=217 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_edu_institutes`
--

CREATE TABLE IF NOT EXISTS `wave_edu_institutes` (
  `id` mediumint(5) NOT NULL DEFAULT '0' COMMENT 'Идентификатор ВУЗа на портале edu.ru.',
  `p_id` mediumint(5) NOT NULL COMMENT 'Если ВУЗ является филиалом, то в этом поле содержится идентификатор головного ВУЗа.',
  `name` text NOT NULL COMMENT 'Название ВУЗа.',
  `acronym` varchar(20) NOT NULL COMMENT 'Аббревиатура, везде пусто (будет заполняться своими силами).',
  `type` varchar(100) NOT NULL COMMENT 'Тип учебного заведения.',
  `okpo` varchar(12) NOT NULL COMMENT 'ОКПО код.',
  `town` varchar(50) NOT NULL COMMENT 'Город, в котором ВУЗ располагается.',
  `address` text NOT NULL COMMENT 'Почтовый адрес ВУЗа.',
  `phone` text NOT NULL COMMENT 'Контактные телефоны. Формат: (код_города) телефон1, телефон2, ..., телефонN.',
  `fax` text NOT NULL COMMENT 'Факс.',
  `email` varchar(150) NOT NULL COMMENT 'Email. Если несколько — разделены запятыми.',
  `url` varchar(150) NOT NULL COMMENT 'Сайт ВУЗа.',
  `ownership` varchar(200) NOT NULL COMMENT 'Форма собственности.',
  `legalform` varchar(255) NOT NULL COMMENT 'Организационно-правовая форма.',
  `founder` text NOT NULL COMMENT 'Учредитель (если ВУЗ негосударственный).',
  `license_no` varchar(20) NOT NULL COMMENT 'Номер лицензии на осуществление образовательной деятельности (если ВУЗ негосударственный).',
  `license_date` date NOT NULL COMMENT 'Дата получения лицензии (если ВУЗ негосударственный).',
  `accred_from` date NOT NULL COMMENT 'Дата приказа Минобразования России о получении ВУЗом государственной аккредитации.',
  `accred_till` date NOT NULL COMMENT 'Дата, по которую действительная аккредитация ВУЗа.',
  PRIMARY KEY (`id`),
  KEY `town` (`town`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Институты';

-- --------------------------------------------------------

--
-- Структура таблицы `wave_edu_sciences`
--

CREATE TABLE IF NOT EXISTS `wave_edu_sciences` (
  `code` varchar(6) NOT NULL COMMENT 'Код группы наук. Каждая группа наук включает в себя множество специальностей.',
  `name` text NOT NULL COMMENT 'Название группы наук.',
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Типы наук  (по ОКСО от 2005г)';

-- --------------------------------------------------------

--
-- Структура таблицы `wave_edu_specialities`
--

CREATE TABLE IF NOT EXISTS `wave_edu_specialities` (
  `id` smallint(3) NOT NULL AUTO_INCREMENT COMMENT 'Автоинкрементный идентификатор (для дальнейшей привязки к ВУЗам по типу один-ко-многим).',
  `code` varchar(6) NOT NULL COMMENT 'Код специальности.',
  `science` varchar(6) NOT NULL COMMENT 'Код группы наук (из таблицы cms_sciences), к которой относится специальность.',
  `name` text NOT NULL COMMENT 'Название специальности.',
  `qcode` varchar(2) DEFAULT NULL COMMENT 'Код квалификации.',
  `qualification` text NOT NULL COMMENT 'Наименование квалификации.',
  PRIMARY KEY (`id`),
  KEY `code` (`code`,`science`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Типы специальностей (по ОКСО от 2005г)' AUTO_INCREMENT=785 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_friends`
--

CREATE TABLE IF NOT EXISTS `wave_friends` (
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT 'Кто дружит',
  `fid` int(11) NOT NULL DEFAULT '0' COMMENT 'С кем дружит',
  `created` int(11) NOT NULL DEFAULT '0' COMMENT 'Дата дружбы',
  `fgid` int(11) NOT NULL DEFAULT '0' COMMENT 'К какой группе относится аккаунт',
  KEY `uid` (`uid`,`fid`),
  KEY `fid` (`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Друзья - связывает 2 акканта';

-- --------------------------------------------------------

--
-- Структура таблицы `wave_friend_groups`
--

CREATE TABLE IF NOT EXISTS `wave_friend_groups` (
  `id_group` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID группы',
  `id_account` int(11) NOT NULL COMMENT 'ID аккаунта чья группа',
  `name_group` varchar(255) NOT NULL COMMENT 'название группы',
  UNIQUE KEY `id_group` (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Группы друзей (не путать с Группой)' AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_friend_reqs`
--

CREATE TABLE IF NOT EXISTS `wave_friend_reqs` (
  `uid` int(11) NOT NULL DEFAULT '0',
  `fid` int(11) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL DEFAULT '0',
  `msg` varchar(200) NOT NULL,
  KEY `uid` (`uid`,`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Запросы на дружбу';

-- --------------------------------------------------------

--
-- Структура таблицы `wave_logs`
--

CREATE TABLE IF NOT EXISTS `wave_logs` (
  `id` int(14) NOT NULL AUTO_INCREMENT COMMENT 'ID сообжения',
  `type` varchar(16) NOT NULL COMMENT 'Тип сообщения',
  `message` varchar(512) NOT NULL COMMENT 'Текст сообщения',
  `created` int(11) NOT NULL COMMENT 'Дата',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Журнал событий' AUTO_INCREMENT=35887 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_place_city`
--

CREATE TABLE IF NOT EXISTS `wave_place_city` (
  `id_city` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_region` int(10) unsigned NOT NULL,
  `id_country` mediumint(8) unsigned NOT NULL,
  `oid` int(10) unsigned NOT NULL,
  `city_name_ru` varchar(255) DEFAULT NULL,
  `city_name_en` varchar(255) NOT NULL,
  PRIMARY KEY (`id_city`),
  KEY `id_region` (`id_region`),
  KEY `id_country` (`id_country`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Города мира' AUTO_INCREMENT=17590 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_place_country`
--

CREATE TABLE IF NOT EXISTS `wave_place_country` (
  `id_country` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `oid` int(10) unsigned NOT NULL,
  `country_name_ru` varchar(50) NOT NULL,
  `country_name_en` varchar(50) NOT NULL,
  PRIMARY KEY (`id_country`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Страны мира' AUTO_INCREMENT=219 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_place_region`
--

CREATE TABLE IF NOT EXISTS `wave_place_region` (
  `id_region` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_country` mediumint(8) unsigned NOT NULL,
  `oid` int(10) unsigned NOT NULL,
  `region_name_ru` varchar(255) DEFAULT NULL,
  `region_name_en` varchar(255) NOT NULL,
  PRIMARY KEY (`id_region`),
  KEY `id_country` (`id_country`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Регионы мира' AUTO_INCREMENT=1612 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_session`
--

CREATE TABLE IF NOT EXISTS `wave_session` (
  `id` varchar(100) NOT NULL,
  `lestUpdate` varchar(15) NOT NULL,
  `timeOut` varchar(15) NOT NULL,
  `ip` varchar(100) NOT NULL,
  `varsess` varchar(512) NOT NULL DEFAULT '[]'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_session_start`
--

CREATE TABLE IF NOT EXISTS `wave_session_start` (
  `key` varchar(100) NOT NULL,
  `ip` varchar(100) NOT NULL,
  `timeOut` varchar(15) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_stories_view`
--

CREATE TABLE IF NOT EXISTS `wave_stories_view` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT 'ID пользователя',
  `wid` int(11) NOT NULL COMMENT 'ID волны',
  `last_amcom` int(11) NOT NULL COMMENT 'Последнее количество прочтенных комментариев в волне',
  `star_selector` int(2) NOT NULL DEFAULT '0' COMMENT 'Отмеченные потоки Звездочкой',
  `dateview` int(11) NOT NULL COMMENT 'Дата последнего просмотра',
  `readYComments` text NOT NULL COMMENT 'Массив JSON прочитанных комментариев',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='История просмотра волн пользователем' AUTO_INCREMENT=108 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_streams`
--

CREATE TABLE IF NOT EXISTS `wave_streams` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID события',
  `message` text NOT NULL COMMENT 'Текст события',
  `wall_id` int(11) NOT NULL COMMENT 'Стена пользователя (если своя то id совпадают)',
  `uid` int(11) NOT NULL COMMENT 'ID пользователя, чье это событие',
  `attachment` text NOT NULL COMMENT 'Привязанность события (путь)',
  `created` int(11) NOT NULL COMMENT 'Дата создания',
  `type` tinyint(1) NOT NULL,
  `app` varchar(20) NOT NULL COMMENT 'Типизация события (blogs, photo,events)',
  `aid` int(11) NOT NULL COMMENT 'ID типа события "app" (типа ссылка по id)',
  `hide` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Видимость',
  `likes` int(11) NOT NULL COMMENT 'Количество кому нравится',
  PRIMARY KEY (`id`),
  KEY `app` (`app`),
  KEY `aid` (`aid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Лента событий' AUTO_INCREMENT=42 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_users_info`
--

CREATE TABLE IF NOT EXISTS `wave_users_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID записи',
  `uid` int(11) NOT NULL COMMENT 'ID пользователя в аккаунте',
  `city` int(16) NOT NULL COMMENT 'Город',
  `hometown` int(16) NOT NULL COMMENT 'Родной город',
  `sex` int(1) NOT NULL COMMENT 'Пол',
  `birthday` int(11) NOT NULL COMMENT 'День рождения',
  `preferenceSex` int(1) NOT NULL COMMENT 'предпочтения',
  `aboutMe` varchar(1024) NOT NULL COMMENT 'О себе',
  `avatarInfo` varchar(1024) NOT NULL DEFAULT '{"name":"default.png","x":"0","y":"0","w":"512","h":"512"}' COMMENT 'Информация об аватарке',
  `hei` varchar(1024) NOT NULL DEFAULT '{"id":"0","spec":"0","begin":"1900","end":"1900"}' COMMENT 'ВУЗы',
  `work` varchar(1024) NOT NULL DEFAULT '{"id":"0","job":"0","begin":"1900","end":"1900"}' COMMENT 'Работа',
  `privacy` varchar(1024) NOT NULL DEFAULT '{"lstfr":"0","usrstr":"0","usrinf":"0","heiwrk":"0"}' COMMENT 'Конфиденциальность (Все-0, Сеть-1, Друзья друзей-2, Друзья-3,  Никто-4)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Информация об пользователях (расширенная)' AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_users_invite`
--

CREATE TABLE IF NOT EXISTS `wave_users_invite` (
  `uid` int(11) NOT NULL COMMENT 'ID пользователя в аккаунте',
  `fremail` varchar(128) NOT NULL COMMENT 'eMail друга',
  `frname` varchar(128) NOT NULL COMMENT 'Имя друга',
  `ikey` varchar(64) NOT NULL COMMENT 'Ключ(код) инвайта, индивидуальный',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT 'Статус приглашения: 0-в ожидание, 1-отказ(время вышло), 2-зарегистрировался',
  `fid` int(11) NOT NULL DEFAULT '0' COMMENT 'ID пользователя, когда он зарегистрировался',
  `created` int(11) NOT NULL DEFAULT '0' COMMENT 'Дата отсылки предложения',
  PRIMARY KEY (`uid`,`fremail`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Список приглашений пользователей';

-- --------------------------------------------------------

--
-- Структура таблицы `wave_users_invite_mail_all`
--

CREATE TABLE IF NOT EXISTS `wave_users_invite_mail_all` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID записи',
  `fremail` varchar(128) NOT NULL COMMENT 'Почта пользователя',
  `frname` varchar(128) NOT NULL COMMENT 'Имя пользователя',
  `uid` int(11) NOT NULL COMMENT 'ID пользователя, чей контакт',
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Список всех почтовых ящиков пользователей получаемый через и' AUTO_INCREMENT=306 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_users_timer`
--

CREATE TABLE IF NOT EXISTS `wave_users_timer` (
  `uid` int(11) NOT NULL COMMENT 'ID пользователя',
  `usertimer` int(11) NOT NULL DEFAULT '0' COMMENT 'Время проведенное на сайте пользователем (сек)',
  `lastlogin` int(11) NOT NULL DEFAULT '0' COMMENT 'Дата последнего входа',
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Общее время проведенное пользователем в сети';

-- --------------------------------------------------------

--
-- Структура таблицы `wave_waves`
--

CREATE TABLE IF NOT EXISTS `wave_waves` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID волны',
  `name` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT 'Название волны',
  `type` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '{"acb":"0","eub":"0","auw":"0"}' COMMENT 'Тип волны (редактируемая, просмотр, разрешить другим добавлять читателей)',
  `public` int(1) NOT NULL DEFAULT '0' COMMENT 'Публичная волна(1-да,0-нет)',
  `id_usr` int(11) NOT NULL COMMENT 'ID пользователя, который создал волну',
  `amountcom` int(11) NOT NULL DEFAULT '0' COMMENT 'Количество комментарий',
  `tags` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Теги волны, через запятую',
  `created` int(11) NOT NULL COMMENT 'Дата создания',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `tags` (`tags`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Список всех волн' AUTO_INCREMENT=60 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_waves_users`
--

CREATE TABLE IF NOT EXISTS `wave_waves_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_wave` int(11) NOT NULL COMMENT 'ID волны',
  `id_usr` int(11) NOT NULL COMMENT 'ID пользователя',
  `type` int(2) NOT NULL DEFAULT '0' COMMENT 'Тип пользователя в волне (0-состоит,1-основатель)',
  `type_stream` int(2) NOT NULL DEFAULT '0' COMMENT 'Состояние потока: 0-основной, 1-слежу, 2-архив, 3-спам, 4-корзина',
  `created` int(11) NOT NULL COMMENT 'Дата создания',
  PRIMARY KEY (`id_wave`,`id_usr`),
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Связь пользователей с волнами' AUTO_INCREMENT=171 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_work_firms`
--

CREATE TABLE IF NOT EXISTS `wave_work_firms` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id фирмы',
  `name` varchar(120) NOT NULL COMMENT 'Название фирмы',
  `city` int(11) NOT NULL COMMENT 'Город в котором фирма',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Список фирм работ' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_work_jobs`
--

CREATE TABLE IF NOT EXISTS `wave_work_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id специальности',
  `name` varchar(120) NOT NULL COMMENT 'Должность',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Рабочие должности' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_wstour`
--

CREATE TABLE IF NOT EXISTS `wave_wstour` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID экскурсии',
  `config` text NOT NULL COMMENT 'Конфигурация экскурсии',
  `autoplay` tinyint(1) NOT NULL COMMENT 'define if steps should change automatically',
  `showtime` int(2) NOT NULL COMMENT 'timeout for the step',
  `total_steps` int(2) NOT NULL COMMENT 'total number of steps',
  `comment` varchar(256) NOT NULL COMMENT 'Пояснение о экскурсии',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Экскурсии по сайту' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Структура таблицы `wave_wstour_users`
--

CREATE TABLE IF NOT EXISTS `wave_wstour_users` (
  `tid` int(11) NOT NULL COMMENT 'ID тура',
  `uid` int(11) NOT NULL COMMENT 'ID пользователя',
  `status` int(1) NOT NULL COMMENT 'Статус тура (0 - не пройден, 1- пройден)',
  `created` int(12) NOT NULL COMMENT 'Дата прохождения',
  PRIMARY KEY (`tid`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Туры у пользователей';