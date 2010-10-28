$(document).ready(function()
{
    test = new Test('#frm-options');

    $('#lnk-save').click(function() {
        setOptions();
        return false;
    });

    $('#lnk-add-question').click(function() {
        test.addQuestion('pick-one', $('#questions'));
        return false;
    });

    var hide_all   = 'Скрыть всё',
        show_all   = 'Показать всё',
        toggle_all = 'Переключить всё';

    $('#lnk-show-all')
        .text(show_all)
        .click(function() { test.showAll(); return false; });

    $('#lnk-hide-all')
        .text(hide_all)
        .click(function() { test.hideAll(); return false; });

    $('#lnk-toggle-all')
        .text(toggle_all)
        .click(function() {
            test.toggleAll();
            return false;
            //$(this).text(hide_all == $(this).text() ? show_all : hide_all);
        });

    $('#frm-options').submit(function() {
        $('#lnk-save').click();
        return false;
    });

    if (null !== test_id) {
        test.setId(test_id);
        loadTest(test_id);
    }

    AjexFileManager.init({
        returnTo: 'function'
    });
})

function setOptions() {
    $('#status').text('Сохранение...').show();
    $('td.error').text('');

    var options = test.getOptions();
    options.test_id = test.getId();

    $.ajax({
        type:     'POST',
        url:      '/tests/ajax_save_options',
        data:     options,
        dataType: 'json',
        success:  onSetOptionsSuccess,
        error:    onAjaxError
    });
}

function onSetOptionsSuccess(response) {
    if (response.result != true && undefined !== response.formErrors)
    {
        for (field in response.formErrors) {
            $('#' + field)
                .parent()
                .next()
                .text(response.formErrors[field]);
        }
        $('#status').hide();
    }
    else if(response.result != true)
    {
        var msg = 'Не удалось сохранить тест. ' + response.error;
        $('#status').text(msg);
    }
    else
    {
        if (undefined !== response.testId) {
            test.setId(response.testId);
        }

        setQuestions();

        $('#lnk-add-question').show();
    }
}

function setQuestions() {
    var questions = test.getQuestions();

    if (!questions.length) {
        $('#status').hide();
        return;
    }

    test.hideErrors();

    questions = $.toJSON(questions);
    //alert(questions);

    var data = {
        test_id: test.getId(),
        questions: questions
    }

    $.ajax({
        type:     'POST',
        url:      '/tests/ajax_save_questions',
        data:     data,
        dataType: 'json',
        success:  onSetQuestionsSuccess,
        error:    onAjaxError
    });
}

function onSetQuestionsSuccess(response) {
    if (response.result != true)
    {
        if (undefined != response.field_errors)
        {
            var errors;

            for (var idx in response.field_errors)
            {
                errors = response.field_errors[idx];

                if (undefined != errors.question_id) {
                    test.showErrors('old', errors.question_id, errors.errors);
                }
                else if (undefined != errors.tmp_id) {
                    test.showErrors('new', errors.tmp_id, errors.errors);
                }
            }

            $('#status').text('Ошибки при заполнение форм вопросов.');
        }
        else
        {
            var msg = 'Не удалось сохранить вопросы. ' + response.error;
            $('#status').text(msg);
        }
    } else {

        if (undefined != response.new_ids) {
            test.setNewIds(response.new_ids);
        }

        $('#status').hide();
    }
}

function loadTest(tid) {
    $('#status').text('Загрузка...').show();

    $.ajax({
        type:     'POST',
        url:      '/tests/ajax_load_test',
        data:     {test_id: tid},
        dataType: 'json',
        success:  onTestLoadSuccess,
        error:    onAjaxError
    });
}

function onTestLoadSuccess(response) {
    if (response.result != true) {
        var msg = 'Не удалось загрузить параметры теста. ' + response.error;
        $('#status').text(msg);
    } else {
        /* Показываем контейнер, чтобы корректно добавились вопросы */
        $('#questions').show();

        test.setOptions(response.options);
        test.setQuestions(response.questions, $('#questions'));

        /* Прячем, чтобы потом снова показать, но уже с анимацией */
        $('#questions').hide();

        $('#status').hide();
        $('#questions').show('slow', function() { test.hideAll() });
    }
}

function onAjaxError(xhr, textStatus, errorThrown) {
    var msg = 'Ошибка: ' + textStatus;
    $('#status').text(msg);
}

function deleteQuestion(category, id, force) {
    if (undefined === force) {
        force = false;
    }

    if (!confirm('Вы действительно хотите удалить вопрос?')) {
        return false;
    }

    if ('new' == category || force) {
        test.deleteQuestion(category, id);
        return;
    }

    if ('old' == category) {
        $('#status').text('Удаление...').show();

        $.ajax({
            type:     'POST',
            url:      '/tests/ajax_delete_question',
            data:     {question_id: id},
            dataType: 'json',
            error:    onAjaxError,
            success:  function(response) {
                if (response.result != true) {
                    var msg = 'Не удалось удалить вопрос. ' + response.error;
                    $('#status').text(msg);
                } else {
                    deleteQuestion(category, id, true);
                    $('#status').hide();
                }
            }
        });
    }
}
