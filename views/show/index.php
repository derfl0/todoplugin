<table class="default todos">
    <colgroup>
        <col width="*">
        <col width="0">
        <col width="0">
    </colgroup>
    <? foreach ($todos as $todo): ?>
        <?= $this->render_partial('show/todo.php', compact('todo')); ?>
    <? endforeach; ?>
    <tr>
    <form class="studip_form" method="post" id="new_todo_form">
        <td>
            <input type="text" name="new_todo" placeholder="<?= dgettext('todo', 'Neue Aufgabe anlegen'); ?>" value="<?= Request::get('edit_todo') ? : '' ?>">
        </td>
        <td>
            <input type="text" style="width: 200px;" name="todo_until" placeholder="<?= dgettext('todo', 'Erledigen bis'); ?>" value="<?= Request::get('edit_todo_until') ?>">
        </td>
        <td class="actions">
            <?= \Studip\Button::create('Anlegen', 'new_todo_button'); ?>
        </td>
    </form>
</tr>
</table>
