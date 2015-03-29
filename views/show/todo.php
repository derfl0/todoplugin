<tr class="<?= $todo->done ? 'done' : '' ?> <?= $todo->expires && $todo->expires < time() ? 'expired' : '' ?>">
    <td>
        <?= htmlReady($todo->text) ?>
    </td>
    <td>
        <?= htmlReady($todo->getDate()) ?>
    </td>
    <td class="actions">
        <? if ($todo->done): ?>
            <a class="done" href="<?= UrlHelper::getLink('', array('todo_undo' => $todo->id)); ?>">
                <?= Assets::img('icons/16/blue/decline.png'); ?>
            </a>
        <? else: ?>
            <a class="done" href="<?= UrlHelper::getLink('', array('todo_done' => $todo->id)); ?>">
                <?= Assets::img('icons/16/blue/accept.png'); ?>
            </a>
        <? endif; ?>
        <a class="delete" href="<?= UrlHelper::getLink('', array('delete_todo' => $todo->id)); ?>">
            <?= Assets::img('icons/16/blue/trash.png'); ?>
        </a>
    </td>
</tr>