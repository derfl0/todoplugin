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
            $todo = Todo::create(array(
                        'user_id' => User::findCurrent()->id,
                        'text' => studip_utf8decode(Request::get('new_todo')),
                        'expires' => Request::get('todo_until') ? strtotime(Request::get('todo_until')) : null
            ));
            $info[] = Request::get('new_todo');
            if (Request::get('todo_until')) {
                $info[] = Request::get('todo_until');
            }

            // Add to cache
            self::addToCache($todo);

            // answer on xhr
            if (Request::isXhr()) {
                $templatefactory = new Flexi_TemplateFactory(__DIR__ . "/views");
                $template = $templatefactory->open("show/todo.php");
                $template->set_attribute("todo", $todo);
                Header('Content-Type: text/plain;charset=windows-1252');
                echo $template->render();
                die;
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
            $md5 = ToDo::remove(Request::get('delete_todo'));
            self::removeFromCache($md5);
        }

        // Register notification
        NotificationCenter::addObserver($this, "setDefaultToDos", "UserDidCreate");

        // Register markup
        StudipFormat::addStudipMarkup('todoplugin', "\[todo\s?([^\]])*\]", "\[\/todo\]", 'TodoPlugin::markup');

        // Register javascript for links
        PageLayout::addScript($this->getPluginURL() . '/assets/todo_everywhere.js');
        self::addStylesheet('/assets/style.less');

        // Load sessioncache
        self::loadCache();
    }

    public function getPortalTemplate() {

        PageLayout::addScript($this->getPluginURL() . '/assets/application.js');

        $templatefactory = new Flexi_TemplateFactory(__DIR__ . "/views");
        $template = $templatefactory->open("show/index.php");

        $todos = ToDo::getActive();
        $template->set_attribute("todos", $todos);

        // Add help navigation
        $navigation = new Navigation('', 'http://docs.studip.de/help/2.5/de/Basis/PluginToDoWidget');
        $navigation->setImage('icons/16/blue/question.png', array('title' => _('Hilfe'), 'target' => '_blank'));
        $template->icons = array($navigation);

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
        $inCache = self::inCache($contents, $linkParams['todo_until']) ? 'accepted-todo ' : '';

        // If todo already expired
        if ($linkParams['todo_until'] && $linkParams['todo_until'] < time()) {
            return "<span class='{$inCache}todo_link' href='" . URLHelper::getLink('', $linkParams) . "'>" . $contents . " (" . sprintf(dgettext('todos', 'Abgelaufen am %s'), $linkParams['todo_until']) . ")</span>";
        }

        return "<a class='{$inCache}todo_link' href='" . URLHelper::getLink('', $linkParams) . "'>" . $contents . "</a>";
    }

    public static function loadCache() {
        if (!$_SESSION['todos']) {
            $_SESSION['todos'] = DBManager::get()->fetchPairs('SELECT MD5(CONCAT(user_id,text,expires)) as cache, "true" as dummy  FROM todos WHERE user_id = ?', array(User::findCurrent()->id));
        }
    }

    public static function addToCache($todo) {
        $_SESSION['todos'][md5(User::findCurrent()->id . $todo->text . ($todo->expires))] = true;
    }

    public static function removeFromCache($md5) {
        unset($_SESSION['todos'][$md5]);
    }

    public static function inCache($text, $expires = null) {
        return $_SESSION['todos'][md5(User::findCurrent()->id . $text . strtotime($expires))];
    }

}
