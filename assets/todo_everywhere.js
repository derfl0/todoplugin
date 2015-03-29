$(document).ready(function () {
    $('a.todo_link').click(function (e) {
        e.preventDefault();
        if (!$(this).hasClass('accepted-todo')) {
            var text = e.target.text;
            var url = $(this).prop('href')
            var anchor = $(this);
            if (e.shiftKey || confirm('Neue Aufgabe "' + text + '" hinzufügen?')) {
                $.ajax({
                    url: url
                });
                anchor.addClass('accepted-todo');
            }
        }
    });
});