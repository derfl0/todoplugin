<tr class="<?= $todo->done ? 'done' : '' ?> <?= $todo->expires && $todo->expires < time() ? 'expired' : '' ?>">
    <td>
        <?= htmlReady($todo->text) ?>
    </td>
    <td class=dateview" data-jsdate="<?= strftime('%d.%m.%Y %H:%M', $todo->expires) ?>">
        <?= htmlReady($todo->getDate()) ?>
    </td>
    <td class="actions">
        <a class="edit" href="<?= UrlHelper::getLink('', array('delete_todo' => $todo->id, 'edit_todo' => $todo->text, 'edit_todo_until' =>  $todo->expires ? strftime('%d.%m.%Y %H:%M', $todo->expires) : '')); ?>"></a>
        <a class="done" href="<?= UrlHelper::getLink('', array('todo_swap' => $todo->id)); ?>"></a>
        <a class="delete" href="<?= UrlHelper::getLink('', array('delete_todo' => $todo->id)); ?>"></a>
    </td>
</tr>