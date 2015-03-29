<?php

require_once "models/ToDo.php";

/**
 * TodoPlugin.class.php
 *
 * Widget that manages todos
 *
 * @author  Florian Bieringer <florian.bieringer@uni-passau.de>
 * @version 0.1a
 */
class TodoPlugin extends StudIPPlugin implements SystemPlugin, PortalPlugin {

    public function __construct() {
        parent::__construct();

        // Insert new todo
        if (Request::submitted('new_todo')) {
            Todo::create(array(
                'user_id' => User::findCurrent()->id,
                'text' => Request::get('new_todo'),
                'expires' => Request::get('todo_until') ? strtotime(Request::get('todo_until')) : null
            ));
            $info[] = Request::get('new_todo');
            if (Request::get('todo_until')) {
                $info[] = Request::get('todo_until');
            }
            PageLayout::postMessage(MessageBox::info(dgettext('todos', 'Neue Aufgabe hinzugefügt'), $info));
        }

        // Set todo as done
        if (Request::submitted('todo_done')) {
            ToDo::done(Request::get('todo_done'));
        }

        // Set todo as undone
        if (Request::submitted('todo_undo')) {
            ToDo::undo(Request::get('todo_undo'));
        }

        // Delete todo
        if (Request::submitted('delete_todo')) {
            ToDo::remove(Request::get('delete_todo'));
        }

        // Register notification
        NotificationCenter::addObserver($this, "setDefaultToDos", "UserDidCreate");

        // Register markup
        StudipFormat::addStudipMarkup('todoplugin', "\[todo\s?([^\]])*\]", "\[\/todo\]", 'TodoPlugin::markup');
    }

    public function getPortalTemplate() {

        self::addStylesheet('/assets/style.less');
        PageLayout::addScript($this->getPluginURL() . '/assets/application.js');

        $templatefactory = new Flexi_TemplateFactory(__DIR__ . "/views");
        $template = $templatefactory->open("show/index.php");

        $todos = ToDo::getActive();
        $template->set_attribute("todos", $todos);
        return $template;
    }

    public function setDefaultToDos($event, $user) {
        foreach (Config::get()->TODO_DEFAULT as $todo) {

            // Check if we got some until
            if (is_array($todo)) {
                $text = $todo[0];
                $until = strtotime($todo[1]);
            } else {
                $text = $todo;
                $until = null;
            }

            // Create his todo
            Todo::create(array(
                'user_id' => $user->id,
                'text' => $text,
                'expires' => $until
            ));
        }
    }

    public static function markup($markup, $matches, $contents) {
        $linkParams = array("new_todo" => $contents);
        if ($matches[0] != "[todo]") {
            $linkParams['todo_until'] = trim(ltrim(rtrim($matches[0], ']'), '[todo'));
        }
        return "<a href='".URLHelper::getLink('', $linkParams)."'>".$contents."</a>";
    }

}
