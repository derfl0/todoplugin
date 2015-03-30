<?php

/**
 * ToDo.php
 * model class for table ToDo
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Florian Bieringer <florian.bieringer@uni-passau.de>
 * @copyright   2014 Stud.IP Core-Group
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       3.0
 */
class ToDo extends SimpleORMap {

    const TIMEOUT = 259200; // 3 Days timeout

    protected static function configure($config = array()) {
        $config['db_table'] = 'todos';
        parent::configure($config);
    }

    public static function done($id) {
        $todo = self::find($id);

        // Security: can only do my own todos
        if ($todo->user_id == User::findCurrent()->id && !$todo->done) {
            $todo->done = time();
            $todo->store();
        }
    }

    public static function undo($id) {
        $todo = self::find($id);

        // Security: can only do my own todos
        if ($todo->user_id == User::findCurrent()->id && $todo->done) {
            $todo->done = NULL;
            $todo->store();
        }
    }

    public static function remove($id) {
        $todo = self::find($id);

        // Security: can only do my own todos
        if ($todo->user_id == User::findCurrent()->id) {

            // Calculate check md5 to remove from cache
            $md5 = md5(User::findCurrent()->id . $todo->text . $todo->expires);
            $todo->delete();
            return $md5;
        }
    }

    public function getDate() {
        if ($this->expires) {
            $rest = $this->expires - time();
            if ($rest <= 0) {
                return dgettext('todos', 'Abgelaufen');
            }
            if ($rest < 3600) {
                return sprintf(dgettext('todos', 'Noch %s Minuten'), round($rest / 60));
            }
            if ($rest < 86400) {
                return sprintf(dgettext('todos', 'Noch %s Stunden'), round($rest / 3600));
            }

            if ($rest < 864000) {
                return sprintf(dgettext('todos', 'Noch %s Tage'), round($rest / 86400));
            }

            return strftime('%d.%m.%y', $this->expires);
        }
    }

    public static function getActive() {
        return self::findBySQL('user_id = ? AND (expires IS NULL OR expires > ?) AND (done IS NULL OR done > ?)', array(User::findCurrent()->id, time() - self::TIMEOUT, time() - self::TIMEOUT));
    }

}
