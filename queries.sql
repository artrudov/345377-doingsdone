-- -----------------------------------------------------
--  Insert data `CasesInOrder`.`users`
-- -----------------------------------------------------
INSERT INTO `users` (`name`,`password`,`email`,`contacts`,`registration`) VALUES
('Игнат','$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka','ignat.v@gmail.com',NULL,CURRENT_TIMESTAMP()),
('Леночка','$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa','kitty_93@li.ru',NULL,CURRENT_TIMESTAMP()),
('Руслан','$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW','warrior07@mail.ru',NULL,CURRENT_TIMESTAMP());

-- -----------------------------------------------------
--  Insert data `CasesInOrder`.`projects`
-- -----------------------------------------------------
INSERT INTO `projects` (`name`,`user_id`) VALUES
('Входящие',1),
('Учеба', 1),
('Работа', 1),
('Домашние дела', 1),
('Авто', 2);

-- -----------------------------------------------------
--  Insert data `CasesInOrder`.`tasks`
-- -----------------------------------------------------
INSERT INTO `tasks` (`name`,`create_date`,`complete_date`,`deadline`,`file`,`project_id`, `user_id` ) VALUES
('Собеседование в IT компании',CURRENT_TIMESTAMP(),NULL,TIMESTAMP(STR_TO_DATE('01.06.2018 00:00', '%d.%c.%Y %H:%i')),NULL,2,1),
('Выполнить тестовое задание',CURRENT_TIMESTAMP(),NULL,TIMESTAMP(STR_TO_DATE('25.05.2018 00:00', '%d.%c.%Y %H:%i')),NULL,3,1),
('Сделать задание первого раздела',CURRENT_TIMESTAMP(),NULL,TIMESTAMP(STR_TO_DATE('25.05.2018 00:00', '%d.%c.%Y %H:%i')),NULL,2,1),
('Встреча с другом',CURRENT_TIMESTAMP(),NULL,TIMESTAMP(STR_TO_DATE('25.05.2018 00:00', '%d.%c.%Y %H:%i')),NULL,1,1),
('Купить корм для кота',CURRENT_TIMESTAMP(),NULL,TIMESTAMP(STR_TO_DATE('18.05.2018 00:00', '%d.%c.%Y %H:%i')),NULL,4,1),
('Заказать пиццу',CURRENT_TIMESTAMP(),NULL,NULL,NULL,4,1);

-- -----------------------------------------------------
--  Получить список из всех проектов для одного пользователя;
-- -----------------------------------------------------
SELECT * FROM projects
WHERE user_id = 1;

-- -----------------------------------------------------
--  Получить список из всех задач для одного проекта;
-- -----------------------------------------------------
SELECT * FROM tasks
WHERE project_id = 1;

-- -----------------------------------------------------
--  Пометить задачу как выполненную;
-- -----------------------------------------------------
UPDATE tasks
SET complete_date = CURRENT_TIMESTAMP()
WHERE id = 1;

-- -----------------------------------------------------
--  Получить все задачи для завтрашнего дня;
-- 86400 секунд в сутках
-- -----------------------------------------------------
SELECT * FROM tasks
WHERE deadline < CURRENT_TIMESTAMP() + 86400;

-- -----------------------------------------------------
--  Обновить название задачи по её идентификатору.;
-- -----------------------------------------------------
UPDATE tasks
SET name = 'Купить коту проплан'
WHERE id = 5;
