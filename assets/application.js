$(document).ready(function () {
    STUDIP.todo.init();
    STUDIP.todo.applySubmit();
});

STUDIP.todo = {
    init: function () {
        $('input[name="todo_until"]').datetimepicker({
            onClose: function () {
                if ($('input[name="new_todo"]').val() !== '') {
                    $('form#new_todo_form').submit();
                }
            }
        });
        $('button[name="new_todo_button"]').parent('td').hide();
        $('input[name="todo_until"]').parent('td').prop('colspan', '2');
        STUDIP.todo.applyAnchor();
    },
    applySubmit: function () {
        // Ajax bling bling
        $('form#new_todo_form').submit(function (e) {
            e.preventDefault();
            var url = $(this).attr('action');
            $.ajax({
                url: url,
                data: e.target.serialize(),
                type: 'post',
                success: function (msg) {
                    $('input[name="todo_until"]').parents('tr').before(msg);
                    STUDIP.todo.init();
                }
            });
            $('input[name="todo_until"]').val('');
            $('input[name="new_todo"]').val('');
        });
    },
    applyAnchor: function () {
        // Even more ajax bling bling
        $('table.todos a:not(.applied)').click(function (e) {
            e.preventDefault();
            var url = $(this).attr('href');
            if ($(this).hasClass('delete')) {
                $(this).parents('tr').remove();
            }
            if ($(this).hasClass('done')) {
                $(this).parents('tr').toggleClass('done');
            }
            $.ajax({
                url: url,
                type: 'post',
            });
        }).addClass('applied');
    }
}