<?php

class SetupTodo extends DBMigration {

    function up() {

        // Informationstext zum Speichern des Bildes im Kartenportal
        Config::get()->create('TODO_DEFAULT', array(
            'value' => json_encode(array("Interessante Kurse suchen", array("Einen Profilbild hochladen", "+7 day"))),
            'type' => 'array',
            'range' => 'global',
            'section' => 'ToDo Plugin',
            'description' => dgettext('todos', 'ToDos für neue Benutzer array("Einen Baum pflanzen", array("Kuchen backen", "+7 day"))')
        ));

        // Create printer table
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `todos` (
  `todo_id` varchar(32) NOT NULL,
  `user_id` varchar(32) NOT NULL,
  `text` varchar(255) NOT NULL DEFAULT '',
  `expires` int(11) DEFAULT NULL,
  `done` int(11) DEFAULT NULL,
  `chdate` int(11) DEFAULT NULL,
  `mkdate` int(11) DEFAULT NULL,
  PRIMARY KEY (`todo_id`),
  KEY `user_id` (`user_id`)
)");

        StudipAutoloader::addAutoloadPath(__DIR__ . '/../models');
        ToDo::expireTableScheme();
    }

    function down() {
        Config::get()->delete('TODO_DEFAULT');
        DBManager::get()->exec("DROP TABLE IF EXISTS `todos`");
    }

}
