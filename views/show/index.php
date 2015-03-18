<table class="default todos">
    <colgroup>
        <col width="*">
        <col width="0">
        <col width="0">
    </colgroup>
    <? foreach ($todos as $todo): ?>
        <tr class="<?= $todo->done ? 'done' : '' ?> <?= $todo->expires && $todo->expires < time() ? 'expired' : '' ?>">
            <td>
                <?= htmlReady($todo->text) ?>
            </td>
            <td>
                <?= htmlReady($todo->getDate()) ?>
            </td>
            <td class="actions">
                <? if ($todo->done): ?>
                    <a href="<?= UrlHelper::getLink('', array('todo_undo' => $todo->id)); ?>">
                        <?= Assets::img('icons/16/blue/decline.png'); ?>
                    </a>
                <? else: ?>
                    <a href="<?= UrlHelper::getLink('', array('todo_done' => $todo->id)); ?>">
                        <?= Assets::img('icons/16/blue/accept.png'); ?>
                    </a>
                <? endif; ?>
                <a>
                    <a href="<?= UrlHelper::getLink('', array('delete_todo' => $todo->id)); ?>">
                        <?= Assets::img('icons/16/blue/trash.png'); ?>
                    </a>
                </a>
            </td>
        </tr>
    <? endforeach; ?>
    <tr>
    <form class="studip_form" method="post">
        <td>
            <input type="text" name="new_todo" placeholder="<?= dgettext('todo', 'Neue Aufgabe anlegen'); ?>">
        </td>
        <td>
            <input type="text" style="width: 200px;" name="todo_until" placeholder="<?= dgettext('todo', 'Erledigen bis'); ?>">
        </td>
        <td class="actions">
            <?= \Studip\Button::create('Anlegen', 'new_todo_button'); ?>
        </td>
    </form>
</tr>
</table>
