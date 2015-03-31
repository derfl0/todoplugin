<tr class="<?= $todo->done ? 'done' : '' ?> <?= $todo->expires && $todo->expires < time() ? 'expired' : '' ?>">
    <td>
        <?= htmlReady($todo->text) ?>
    </td>
    <td>
        <?= htmlReady($todo->getDate()) ?>
    </td>
    <td class="actions">
        <a class="done" href="<?= UrlHelper::getLink('', array('todo_swap' => $todo->id)); ?>">
        </a>
        <a class="delete" href="<?= UrlHelper::getLink('', array('delete_todo' => $todo->id)); ?>">
        </a>
    </td>
</tr>