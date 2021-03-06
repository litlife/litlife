<?php

return [
	'remove_bold' => 'Удалить "жирное" выделение во всем тексте. Будут удалены теги b, strong',
	'remove_extra_spaces' => 'Убрать лишние пробелы перед текстом внутри параграфов',
	'split_into_chapters' => 'Попробовать разбить тексты на главы. Главы разбваются, если в тексте параграфа будут тексты похожие на примеры: "Глава 4", "Эпилог", "Предисловие", "Глава пятая", "Глава XIV", "Глава 7. Название главы", "4. НАЗВАНИЕ ГЛАВЫ", "ЧАСТЬ ПЕРВАЯ. Название части"',
	'convert_new_lines_to_paragraphs' => 'Преобразовать новые строки в параграфы. Теги br будут преобразованы в теги p',
	'add_a_space_after_the_first_hyphen_in_the_paragraph' => 'Добавить пробел после первого дефиса в параграфе. Например текст в параграфе "-текст текст .." станет таким "- текст текст .."',
	'processing' => 'Обработать',
	'book_id' => 'ID обрабатываемой книги',
	'create_user_id' => 'ID пользователя создавшего обработку',
	'started_at' => 'Время начала обработки',
	'completed_at' => 'Время окончания обработки',
	'processing_a_text_is_successfully_created' => 'Обработка текст успешно создана. Редактирование книги временно заблокировано до завершения обработки. По завершении обработки вам будет отправлено уведомление',
	'at_least_one_item_must_be_marked' => 'Должен быть отмечен хотя бы один пункт',
	'completed' => 'Завершена',
	'started' => 'Начата',
	'created' => 'Создал',
	'no_text_processing_has_been_created_yet' => 'Ни одной обработки текст пока не создано',
	'tidy_chapter_names' => 'Сделать аккуратными названия глав. Например: "ГЛАВА  1" будет приведено в "Глава 1", "3 глава." в "Глава 3"',

	'remove_italics' => 'Удалить курсив во всем тексте. Будут удалены все теги i, em, emphasis',
	'remove_spaces_before_punctuations_marks' => 'Удалить пробелы перед знаками препинания. Пример: "Текст , текст : текст ; текст ... текст ? текст ! текст . текст текст ". Будет преобразован в "Текст, текст: текст; текст... текст? текст! текст. текст текст."',
	'add_spaces_after_punctuations_marks' => 'Добавить пробелы после знаков препинания. Пример: "Текст.текст.текст,текст!текст?текст." будет преобразован в "Текст. текст. текст, текст! текст? текст. "',
	'merge_paragraphs_if_there_is_no_dot_at_the_end' => 'Слить параграфы, если в конце текста параграфа нет точки. Например: "<p>Текст текст</p><p> текст текст.</p><p>Текст текст.</p>" станет таким: "<p>Текст текст текст текст.</p><p>Текст текст.</p>"',
	'remove_empty_paragraphs' => 'Удалить все пустые параграфы в тексте книги. Пустыми считаются параграфы те, которые не содержат тексты, не содержат изображения, содержат только пробелы',
];
