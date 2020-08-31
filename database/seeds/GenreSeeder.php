<?php

use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$array = [
			[
				'name' => 'Фантастика и Фэнтези',
				'subenre' => [
					['name' => 'Космоопера', 'fb2_code' => 'sf_space_opera', 'age' => '0'],
					['name' => 'Мистика', 'fb2_code' => 'sf_mystic', 'age' => '0'],
					['name' => 'Ненаучная фантастика', 'fb2_code' => 'nsf', 'age' => '0'],
					['name' => 'Боевая фантастика', 'fb2_code' => 'sf_action', 'age' => '0'],
					['name' => 'Технофэнтези', 'fb2_code' => 'sf_technofantasy', 'age' => '0'],
					['name' => 'Фэнтези', 'fb2_code' => 'sf_fantasy', 'age' => '0'],
					['name' => 'Эпическая фантастика', 'fb2_code' => 'sf_epic', 'age' => '0'],
					['name' => 'Ироническое фэнтези', 'fb2_code' => 'sf_fantasy_irony', 'age' => '0'],
					['name' => 'Космическая фантастика', 'fb2_code' => 'sf_space', 'age' => '0'],
					['name' => 'Фантастика: прочее', 'fb2_code' => 'sf_etc', 'age' => '0'],
					['name' => 'Юмористическая фантастика', 'fb2_code' => 'sf_humor', 'age' => '0'],
					['name' => 'Научная фантастика', 'fb2_code' => 'sf', 'age' => '0'],
					['name' => 'Городское фэнтези', 'fb2_code' => 'sf_fantasy_city', 'age' => '0'],
					['name' => 'Юмористическое фэнтези', 'fb2_code' => 'humor_fantasy', 'age' => '0'],
					['name' => 'Стимпанк', 'fb2_code' => 'sf_stimpank', 'age' => '0'],
					['name' => 'Ироническая фантастика', 'fb2_code' => 'sf_irony', 'age' => '0'],
					['name' => 'Сказочная фантастика', 'fb2_code' => 'fairy_fantasy', 'age' => '0'],
					['name' => 'Детективная фантастика', 'fb2_code' => 'sf_detective', 'age' => '0'],
					['name' => 'Социально-философская фантастика', 'fb2_code' => 'sf_social', 'age' => '0'],
					['name' => 'Киберпанк', 'fb2_code' => 'sf_cyberpunk', 'age' => '0'],
					['name' => 'Ужасы и мистика', 'fb2_code' => 'sf_horror', 'age' => '0'],
					['name' => 'Историческое фэнтези', 'fb2_code' => 'historical_fantasy', 'age' => '0'],
					['name' => 'Героическая фантастика', 'fb2_code' => 'sf_heroic', 'age' => '0'],
					['name' => 'Готический роман', 'fb2_code' => 'gothic_novel', 'age' => '0'],
					['name' => 'ЛитРПГ', 'fb2_code' => 'litrpg', 'age' => '0'],
					['name' => 'Постапокалипсис', 'fb2_code' => 'sf_postapocalyptic', 'age' => '0'],
					['name' => 'Попаданцы', 'fb2_code' => 'popadanec', 'age' => '0'],
					['name' => 'Альтернативная история', 'fb2_code' => 'sf_history', 'age' => '0']
				]
			],
			[
				'name' => 'Проза',
				'subenre' => [
					['name' => 'Историческая проза', 'fb2_code' => 'prose_history', 'age' => '0'],
					['name' => 'Классическая проза', 'fb2_code' => 'prose_classic', 'age' => '0'],
					['name' => 'Эссе, очерк, этюд, набросок', 'fb2_code' => 'essay', 'age' => '0'],
					['name' => 'Советская классическая проза', 'fb2_code' => 'prose_su_classics', 'age' => '0'],
					['name' => 'Русская классическая проза', 'fb2_code' => 'prose_rus_classic', 'age' => '0'],
					['name' => 'Рассказ', 'fb2_code' => 'short_story', 'age' => '0'],
					['name' => 'Повесть', 'fb2_code' => 'great_story', 'age' => '0'],
					['name' => 'Сентиментальная проза', 'fb2_code' => 'prose_sentimental', 'age' => '0'],
					['name' => 'Новелла', 'fb2_code' => 'story', 'age' => '0'],
					['name' => 'Проза прочее', 'fb2_code' => 'prose', 'age' => '0'],
					['name' => 'Магический реализм', 'fb2_code' => 'prose_magic', 'age' => '0'],
					['name' => 'Семейный роман/Семейная сага', 'fb2_code' => 'sagas', 'age' => '0'],
					['name' => 'Современная проза', 'fb2_code' => 'prose_contemporary', 'age' => '0'],
					['name' => 'Военная проза', 'fb2_code' => 'prose_military', 'age' => '0'],
					['name' => 'Эпопея', 'fb2_code' => 'prose_epic', 'age' => '0'],
					['name' => 'Контркультура', 'fb2_code' => 'prose_counter', 'age' => '0'],
					['name' => 'Афоризмы', 'fb2_code' => 'aphorisms', 'age' => '0'],
					['name' => 'Феерия', 'fb2_code' => 'extravaganza', 'age' => '0'],
					['name' => 'Роман', 'fb2_code' => 'roman', 'age' => '0'],
					['name' => 'Эпистолярная проза', 'fb2_code' => 'epistolary_fiction', 'age' => '0'],
					['name' => 'Антисоветская литература', 'fb2_code' => 'dissident', 'age' => '0'],
				]
			],
			[
				'name' => 'Любовные романы',
				'subenre' => [
					['name' => 'Эротика', 'fb2_code' => 'love_erotica', 'age' => '18'],
					['name' => 'Любовно-фантастические романы', 'fb2_code' => 'love_sf', 'age' => '0'],
					['name' => 'Остросюжетные любовные романы', 'fb2_code' => 'love_detective', 'age' => '0'],
					['name' => 'Слеш', 'fb2_code' => 'slash', 'age' => '18'],
					['name' => 'Современные любовные романы', 'fb2_code' => 'love_contemporary', 'age' => '0'],
					['name' => 'Короткие любовные романы', 'fb2_code' => 'love_short', 'age' => '0'],
					['name' => 'Исторические любовные романы', 'fb2_code' => 'love_history', 'age' => '0'],
					['name' => 'Фемслеш', 'fb2_code' => 'femslash', 'age' => '18'],
					['name' => 'Порно', 'fb2_code' => 'love_hard', 'age' => '18'],
					['name' => 'Прочие любовные романы', 'fb2_code' => 'love', 'age' => '0'],
				]
			],
			[
				'name' => 'Научно-образовательная',
				'subenre' => [
					['name' => 'Геология и география', 'fb2_code' => 'sci_geo', 'age' => '0'],
					['name' => 'Альтернативная медицина', 'fb2_code' => 'sci_medicine_alternative', 'age' => '0'],
					['name' => 'Литературоведение', 'fb2_code' => 'sci_philology', 'age' => '0'],
					['name' => 'Педагогика', 'fb2_code' => 'sci_pedagogy', 'age' => '0'],
					['name' => 'Обществознание', 'fb2_code' => 'sci_social_studies', 'age' => '0'],
					['name' => 'Экология', 'fb2_code' => 'sci_ecology', 'age' => '0'],
					['name' => 'Военная история', 'fb2_code' => 'military_history', 'age' => '0'],
					['name' => 'Ветеринария', 'fb2_code' => 'sci_veterinary', 'age' => '0'],
					['name' => 'Биология', 'fb2_code' => 'sci_biology', 'age' => '0'],
					['name' => 'Религиоведение', 'fb2_code' => 'sci_religion', 'age' => '0'],
					['name' => 'История', 'fb2_code' => 'sci_history', 'age' => '0'],
					['name' => 'Политика', 'fb2_code' => 'sci_politics', 'age' => '0'],
					['name' => 'Химия', 'fb2_code' => 'sci_chem', 'age' => '0'],
					['name' => 'Технические науки', 'fb2_code' => 'sci_tech', 'age' => '0'],
					['name' => 'Прочая научная литература', 'fb2_code' => 'science', 'age' => '0'],
					['name' => 'Медицина', 'fb2_code' => 'sci_medicine', 'age' => '0'],
					['name' => 'Философия', 'fb2_code' => 'sci_philosophy', 'age' => '0'],
					['name' => 'Психология', 'fb2_code' => 'sci_psychology', 'age' => '0'],
					['name' => 'Культурология', 'fb2_code' => 'sci_culture', 'age' => '0'],
					['name' => 'Юриспруденция', 'fb2_code' => 'sci_juris', 'age' => '0'],
					['name' => 'Языкознание', 'fb2_code' => 'sci_linguistic', 'age' => '0'],
					['name' => 'Физика', 'fb2_code' => 'sci_phys', 'age' => '0'],
					['name' => 'Математика', 'fb2_code' => 'sci_math', 'age' => '0'],
					['name' => 'Секс и семейная психология', 'fb2_code' => 'psy_sex_and_family', 'age' => '0'],
					['name' => 'Психотерапия и консультирование', 'fb2_code' => 'psy_theraphy', 'age' => '0'],
					['name' => 'Детская психология', 'fb2_code' => 'psy_childs', 'age' => '0'],
					['name' => 'Иностранные языки', 'fb2_code' => 'foreign_language', 'age' => '0'],
					['name' => 'Рефераты', 'fb2_code' => 'sci_abstract', 'age' => '0'],
					['name' => 'Шпаргалки', 'fb2_code' => 'sci_crib', 'age' => '0'],
					['name' => 'Учебники', 'fb2_code' => 'sci_textbook', 'age' => '0'],
					['name' => 'Ботаника', 'fb2_code' => 'sci_botany', 'age' => '0'],
					['name' => 'Зоология', 'fb2_code' => 'sci_zoo', 'age' => '0'],
					['name' => 'Биохимия', 'fb2_code' => 'sci_biochem', 'age' => '0'],
					['name' => 'Физическая химия', 'fb2_code' => 'sci_physchem', 'age' => '0'],
					['name' => 'Аналитическая химия', 'fb2_code' => 'sci_anachem', 'age' => '0'],
					['name' => 'Органическая химия', 'fb2_code' => 'sci_orgchem', 'age' => '0'],
					['name' => 'Государство и право', 'fb2_code' => 'sci_state', 'age' => '0'],
					['name' => 'Биофизика', 'fb2_code' => 'sci_biophys', 'age' => '0'],
					['name' => 'Астрономия и Космос', 'fb2_code' => 'sci_cosmos', 'age' => '0'],
				]
			],
			[
				'name' => 'Детективы и Триллеры',
				'subenre' => [
					['name' => 'Триллеры', 'fb2_code' => 'thriller', 'age' => '0'],
					['name' => 'Шпионские детективы', 'fb2_code' => 'det_espionage', 'age' => '0'],
					['name' => 'Дамский детективный роман', 'fb2_code' => 'det_cozy', 'age' => '0'],
					['name' => 'Криминальные детективы', 'fb2_code' => 'det_crime', 'age' => '0'],
					['name' => 'Прочие Детективы', 'fb2_code' => 'detective', 'age' => '0'],
					['name' => 'Полицейские детективы', 'fb2_code' => 'det_police', 'age' => '0'],
					['name' => 'Иронические детективы', 'fb2_code' => 'det_irony', 'age' => '0'],
					['name' => 'Техно триллер', 'fb2_code' => 'thriller_techno', 'age' => '0'],
					['name' => 'Исторические детективы', 'fb2_code' => 'det_history', 'age' => '0'],
					['name' => 'Крутой детектив', 'fb2_code' => 'det_hard', 'age' => '0'],
					['name' => 'Боевики', 'fb2_code' => 'det_action', 'age' => '0'],
					['name' => 'Политические детективы', 'fb2_code' => 'det_political', 'age' => '0'],
					['name' => 'Маньяки', 'fb2_code' => 'det_maniac', 'age' => '0'],
					['name' => 'Юридический триллер', 'fb2_code' => 'thriller_legal', 'age' => '0'],
					['name' => 'Медицинский триллер', 'fb2_code' => 'thriller_medical', 'age' => '0'],
					['name' => 'Классические детективы', 'fb2_code' => 'det_classic', 'age' => '0'],
				]
			],
			[
				'name' => 'Документальная литература',
				'subenre' => [
					['name' => 'Научпоп', 'fb2_code' => 'sci_popular', 'age' => '0'],
					['name' => 'Публицистика', 'fb2_code' => 'nonf_publicism', 'age' => '0'],
					['name' => 'Биографии и мемуары', 'fb2_code' => 'nonf_biography', 'age' => '0'],
					['name' => 'Критика', 'fb2_code' => 'nonf_criticism', 'age' => '0'],
					['name' => 'Прочая документальная литература', 'fb2_code' => 'nonfiction', 'age' => '0'],
					['name' => 'Искусство и Дизайн', 'fb2_code' => 'design', 'age' => '0'],
					['name' => 'Военная документалистика', 'fb2_code' => 'nonf_military', 'age' => '0'],
				]
			],
			[
				'name' => 'Детские',
				'subenre' => [
					['name' => 'Сказки', 'fb2_code' => 'child_tale', 'age' => '0'],
					['name' => 'Детская образовательная литература', 'fb2_code' => 'child_education', 'age' => '0'],
					['name' => 'Детский фольклор', 'fb2_code' => 'child_folklore', 'age' => '0'],
					['name' => 'Детские приключения', 'fb2_code' => 'child_adv', 'age' => '0'],
					['name' => 'Прочая детская литература', 'fb2_code' => 'children', 'age' => '0'],
					['name' => 'Детские остросюжетные', 'fb2_code' => 'child_det', 'age' => '0'],
					['name' => 'Детская проза', 'fb2_code' => 'child_prose', 'age' => '0'],
					['name' => 'Детская фантастика', 'fb2_code' => 'child_sf', 'age' => '0'],
					['name' => 'Книга-игра', 'fb2_code' => 'prose_game', 'age' => '0'],
					['name' => 'Детские стихи', 'fb2_code' => 'child_verse', 'age' => '0'],
				]
			],
			[
				'name' => 'Приключения',
				'subenre' => [
					['name' => 'Вестерны', 'fb2_code' => 'adv_western', 'age' => '0'],
					['name' => 'Приключения про индейцев', 'fb2_code' => 'adv_indian', 'age' => '0'],
					['name' => 'Морские приключения', 'fb2_code' => 'adv_maritime', 'age' => '0'],
					['name' => 'Путешествия и география', 'fb2_code' => 'adv_geo', 'age' => '0'],
					['name' => 'Исторические приключения', 'fb2_code' => 'adv_history', 'age' => '0'],
					['name' => 'Природа и животные', 'fb2_code' => 'adv_animal', 'age' => '0'],
					['name' => 'Прочие приключения', 'fb2_code' => 'adventure', 'age' => '0'],
				]
			],
			[
				'name' => 'Дом и Семья',
				'subenre' => [
					['name' => 'Хобби и ремесла', 'fb2_code' => 'home_crafts', 'age' => '0'],
					['name' => 'Сад и Огород', 'fb2_code' => 'home_garden', 'age' => '0'],
					['name' => 'Спорт', 'fb2_code' => 'home_sport', 'age' => '0'],
					['name' => 'Сделай сам', 'fb2_code' => 'home_diy', 'age' => '0'],
					['name' => 'Коллекционирование', 'fb2_code' => 'home_collecting', 'age' => '0'],
					['name' => 'Эротика и секс', 'fb2_code' => 'home_sex', 'age' => '18'],
					['name' => 'Прочее домоводство', 'fb2_code' => 'home', 'age' => '0'],
					['name' => 'Здоровье и красота', 'fb2_code' => 'home_health', 'age' => '0'],
					['name' => 'Развлечения', 'fb2_code' => 'home_entertain', 'age' => '0'],
					['name' => 'Кулинария', 'fb2_code' => 'home_cooking', 'age' => '0'],
					['name' => 'Домашние животные', 'fb2_code' => 'home_pets', 'age' => '0'],
				]
			],
			[
				'name' => 'Поэзия и драматургия',
				'subenre' => [
					['name' => 'Эпическая поэзия', 'fb2_code' => 'epic_poetry', 'age' => '0'],
					['name' => 'Поэзия', 'fb2_code' => 'poetry', 'age' => '0'],
					['name' => 'Драматургия', 'fb2_code' => 'dramaturgy', 'age' => '0'],
					['name' => 'Басни', 'fb2_code' => 'fable', 'age' => '0'],
					['name' => 'Верлибры', 'fb2_code' => 'vers_libre', 'age' => '0'],
					['name' => 'Визуальная поэзия', 'fb2_code' => 'visual_poetry', 'age' => '0'],
					['name' => 'Лирика', 'fb2_code' => 'lyrics', 'age' => '0'],
					['name' => 'Палиндромы', 'fb2_code' => 'palindromes', 'age' => '0'],
					['name' => 'Песенная поэзия', 'fb2_code' => 'song_poetry', 'age' => '0'],
					['name' => 'Экспериментальная поэзия', 'fb2_code' => 'experimental_poetry', 'age' => '0'],
					['name' => 'в стихах', 'fb2_code' => 'in_verse', 'age' => '0'],
				]
			],
			[
				'name' => 'Религия и духовность',
				'subenre' => [
					['name' => 'Хиромантия', 'fb2_code' => 'palmistry', 'age' => '0'],
					['name' => 'Астрология', 'fb2_code' => 'astrology', 'age' => '0'],
					['name' => 'Иудаизм', 'fb2_code' => 'religion_judaism', 'age' => '0'],
					['name' => 'Ислам', 'fb2_code' => 'religion_islam', 'age' => '0'],
					['name' => 'Прочая религиозная литература', 'fb2_code' => 'religion', 'age' => '0'],
					['name' => 'Буддизм', 'fb2_code' => 'religion_budda', 'age' => '0'],
					['name' => 'Православие', 'fb2_code' => 'religion_orthodoxy', 'age' => '0'],
					['name' => 'Христианство', 'fb2_code' => 'religion_christianity', 'age' => '0'],
					['name' => 'Католицизм', 'fb2_code' => 'religion_catholicism', 'age' => '0'],
					['name' => 'Религия', 'fb2_code' => 'religion_rel', 'age' => '0'],
					['name' => 'Эзотерика', 'fb2_code' => 'religion_esoterics', 'age' => '0'],
					['name' => 'Протестантизм', 'fb2_code' => 'religion_protestantism', 'age' => '0'],
					['name' => 'Индуизм', 'fb2_code' => 'religion_hinduism', 'age' => '0'],
					['name' => 'Самосовершенствование', 'fb2_code' => 'religion_self', 'age' => '0'],
					['name' => 'Язычество', 'fb2_code' => 'religion_paganism', 'age' => '0'],
				]
			],
			[
				'name' => 'Прочее',
				'subenre' => [
					['name' => 'Шахматы', 'fb2_code' => 'chess', 'age' => '0'],
					['name' => 'Недописанное', 'fb2_code' => 'unfinished', 'age' => '0'],
					['name' => 'Театр', 'fb2_code' => 'theatre', 'age' => '0'],
					['name' => 'Газеты и журналы', 'fb2_code' => 'periodic', 'age' => '0'],
					['name' => 'Музыка', 'fb2_code' => 'music', 'age' => '0'],
					['name' => 'Кино', 'fb2_code' => 'cine', 'age' => '0'],
					['name' => 'Партитуры', 'fb2_code' => 'notes', 'age' => '0'],
					['name' => 'Изобразительное искусство, фотография', 'fb2_code' => 'visual_arts', 'age' => '0'],
					['name' => 'Фанфик', 'fb2_code' => 'fanfiction', 'age' => '0'],
					['name' => 'Подростковая литература', 'fb2_code' => 'ya', 'age' => '0'],
				]
			],
			[
				'name' => 'Юмор',
				'subenre' => [
					['name' => 'Юмористические стихи', 'fb2_code' => 'humor_verse', 'age' => '0'],
					['name' => 'Комедия', 'fb2_code' => 'comedy', 'age' => '0'],
					['name' => 'Прочий юмор', 'fb2_code' => 'humor', 'age' => '0'],
					['name' => 'Сатира', 'fb2_code' => 'humor_satire', 'age' => '0'],
					['name' => 'Анекдоты', 'fb2_code' => 'humor_anecdote', 'age' => '0'],
					['name' => 'Юмористическая проза', 'fb2_code' => 'humor_prose', 'age' => '0'],
				]
			],
			[
				'name' => 'Справочная литература',
				'subenre' => [
					['name' => 'Прочая справочная литература', 'fb2_code' => 'reference', 'age' => '0'],
					['name' => 'Руководства', 'fb2_code' => 'ref_guide', 'age' => '0'],
					['name' => 'Словари', 'fb2_code' => 'ref_dict', 'age' => '0'],
					['name' => 'Энциклопедии', 'fb2_code' => 'ref_encyc', 'age' => '0'],
					['name' => 'Путеводители', 'fb2_code' => 'geo_guides', 'age' => '0'],
					['name' => 'Справочники', 'fb2_code' => 'ref_ref', 'age' => '0'],
				]
			],
			[
				'name' => 'Деловая литература',
				'subenre' => [
					['name' => 'Управление, подбор персонала', 'fb2_code' => 'management', 'age' => '0'],
					['name' => 'Ценные бумаги, инвестиции', 'fb2_code' => 'stock', 'age' => '0'],
					['name' => 'Деловая литература', 'fb2_code' => 'sci_business', 'age' => '0'],
					['name' => 'Недвижимость', 'fb2_code' => 'real_estate', 'age' => '0'],
					['name' => 'Корпоративная культура', 'fb2_code' => 'org_behavior', 'age' => '0'],
					['name' => 'Внешнеэкономическая деятельность', 'fb2_code' => 'global_economy', 'age' => '0'],
					['name' => 'Банковское дело', 'fb2_code' => 'banking', 'age' => '0'],
					['name' => 'Личные финансы', 'fb2_code' => 'personal_finance', 'age' => '0'],
					['name' => 'Экономика', 'fb2_code' => 'economics', 'age' => '0'],
					['name' => 'Делопроизводство', 'fb2_code' => 'paper_work', 'age' => '0'],
					['name' => 'Торговля', 'fb2_code' => 'trade', 'age' => '0'],
					['name' => 'Поиск работы, карьера', 'fb2_code' => 'job_hunting', 'age' => '0'],
					['name' => 'Маркетинг, PR, реклама', 'fb2_code' => 'marketing', 'age' => '0'],
					['name' => 'О бизнесе популярно', 'fb2_code' => 'popular_business', 'age' => '0'],
					['name' => 'Бухучет и аудит', 'fb2_code' => 'accounting', 'age' => '0'],
					['name' => 'Малый бизнес', 'fb2_code' => 'small_business', 'age' => '0'],
					['name' => 'Отраслевые издания', 'fb2_code' => 'industries', 'age' => '0'],
				]
			],
			[
				'name' => 'Старинная литература',
				'subenre' => [
					['name' => 'Древневосточная литература', 'fb2_code' => 'antique_east', 'age' => '0'],
					['name' => 'Прочая старинная литература', 'fb2_code' => 'antique', 'age' => '0'],
					['name' => 'Античная литература', 'fb2_code' => 'antique_ant', 'age' => '0'],
					['name' => 'Европейская старинная литература', 'fb2_code' => 'antique_european', 'age' => '0'],
					['name' => 'Древнерусская литература', 'fb2_code' => 'antique_russian', 'age' => '0'],
					['name' => 'Мифы. Легенды. Эпос', 'fb2_code' => 'antique_myths', 'age' => '0'],
				]
			],
			[
				'name' => 'Военное дело',
				'subenre' => [
					['name' => 'Военное дело: прочее', 'fb2_code' => 'military', 'age' => '0'],
					['name' => 'Боевые искусства', 'fb2_code' => 'military_arts', 'age' => '0'],
					['name' => 'Военная техника и вооружение', 'fb2_code' => 'military_weapon', 'age' => '0'],
					['name' => 'Cпецслужбы', 'fb2_code' => 'military_special', 'age' => '0'],
				]
			],
			[
				'name' => 'Компьютеры и Интернет',
				'subenre' => [
					['name' => 'Базы данных', 'fb2_code' => 'comp_db', 'age' => '0'],
					['name' => 'Прочая компьютерная литература', 'fb2_code' => 'computers', 'age' => '0'],
					['name' => 'ОС и Сети', 'fb2_code' => 'comp_osnet', 'age' => '0'],
					['name' => 'Цифровая обработка сигналов', 'fb2_code' => 'comp_dsp', 'age' => '0'],
					['name' => 'Компьютерное \"железо\"', 'fb2_code' => 'comp_hard', 'age' => '0'],
					['name' => 'Программирование', 'fb2_code' => 'comp_programming', 'age' => '0'],
					['name' => 'Интернет', 'fb2_code' => 'comp_www', 'age' => '0'],
					['name' => 'Программное обеспечение', 'fb2_code' => 'comp_soft', 'age' => '0'],
				]
			],
			[
				'name' => 'Техника',
				'subenre' => [
					['name' => 'Архитектура', 'fb2_code' => 'architecture_book', 'age' => '0'],
					['name' => 'Автомобили и ПДД', 'fb2_code' => 'auto_regulations', 'age' => '0'],
					['name' => 'Строительство и сопромат', 'fb2_code' => 'sci_build', 'age' => '0'],
					['name' => 'Радиоэлектроника', 'fb2_code' => 'sci_radio', 'age' => '0'],
					['name' => 'Металлургия', 'fb2_code' => 'sci_metal', 'age' => '0'],
					['name' => 'Транспорт и авиация', 'fb2_code' => 'sci_transport', 'age' => '0'],
				]
			],
			[
				'name' => 'Драматургия',
				'subenre' => [
					['name' => 'Мистерия', 'fb2_code' => 'mystery', 'age' => '0'],
					['name' => 'Драма', 'fb2_code' => 'drama', 'age' => '0'],
					['name' => 'Водевиль', 'fb2_code' => 'vaudeville', 'age' => '0'],
					['name' => 'Киносценарии', 'fb2_code' => 'screenplays', 'age' => '0'],
					['name' => 'Трагедия', 'fb2_code' => 'tragedy', 'age' => '0'],
					['name' => 'Сценарии', 'fb2_code' => 'scenarios', 'age' => '0'],
				]
			],
			[
				'name' => 'Фольклор',
				'subenre' => [
					['name' => 'Былины', 'fb2_code' => 'epic', 'age' => '0'],
					['name' => 'Народные песни', 'fb2_code' => 'folk_songs', 'age' => '0'],
					['name' => 'Народные сказки', 'fb2_code' => 'folk_tale', 'age' => '0'],
					['name' => 'Пословицы, поговорки', 'fb2_code' => 'proverbs', 'age' => '0'],
					['name' => 'Частушки, прибаутки, потешки', 'fb2_code' => 'limerick', 'age' => '0'],
					['name' => 'Фольклор: прочее', 'fb2_code' => 'folklore', 'age' => '0'],
					['name' => 'Загадки', 'fb2_code' => 'riddles', 'age' => '0'],
				]
			],
			[
				'name' => 'Жанр не определен',
				'subenre' => [
					['name' => 'Разное', 'fb2_code' => 'other', 'age' => '0'],
				]
			],
		];

		\Illuminate\Support\Facades\DB::transaction(function () use ($array) {
			array_walk($array, function ($array) {
				$this->genre($array);
			});
		});
	}

	public function genre(array $array, $genreGroupId = null)
	{
		$genre = new \App\Genre([
			'name' => $array['name'],
			'fb_code' => $array['fb2_code'] ?? null,
			'age' => $array['age'] ?? null,
			'genre_group_id' => $genreGroupId
		]);

		$genre->save();

		if (!empty($array['subenre'])) {
			array_walk($array['subenre'], function ($array) use ($genre) {
				$this->genre($array, $genre->id);
			});
		}
	}
}
