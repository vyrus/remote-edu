$(document).ready(function()
{
    test = new Test();
    test.setId(test_id);

    $('#btn-start').click(function() {
        startExamination();
    });

    $('#btn-finish').click(function() {
        finishExamination();
    });
})

function startExamination() {
    $('#status')
        .text('Загрузка...')
        .show();

    $.ajax({
        type:     'POST',
        url:      '/tests/ajax_get_exam_questions',
        data:     {test_id: test.getId()},
        dataType: 'json',
        success:  onGetQuestionsSuccess,
        error:    onAjaxError
    });
}

function onGetQuestionsSuccess(response) {
    if (response.result != true) {
        var msg = 'Не удалось загрузить вопросы. ' + response.error;
        $('#status').text(msg);
        return;
    }

    var ol = $('<ol></ol>');
    $('#questions').append(ol);

    test.setExamQuestions(response.questions, ol);

    $('#status').hide();
    $('#btn-start').hide();
    $('#btn-finish').show();
}

function finishExamination() {
    var answers = test.getExamAnswers();
    answers = $.toJSON(answers);
    //alert(answers);

    $('#status')
        .text('Проверка...')
        .show();

    test.disableRadios();

    $.ajax({
        type:     'POST',
        url:      '/tests/ajax_check_exam_answers',
        data:     {test_id: test.getId(), answers: answers},
        dataType: 'json',
        success:  onCheckAnswersSuccess,
        error:    onAjaxError
    });
}

function onCheckAnswersSuccess(response) {
    if (response.result != true) {
        var msg = 'Произошла ошибка при проверке ответов. ' + response.error;
        $('#status').text(msg);
        return;
    }

    test.displayCorrectness(response.results);

    alert('Correct: ' + response.results.correct.length + '\n' +
          'Incorrect: ' + response.results.incorrect.length + '\n' +
          'Unanswered: ' + response.results.unanswered.length + '\n' +
          'Time: ' + response.results.time + '\n' +
          'Passed: ' + response.results.passed);

    $('#status').hide();
}

function onAjaxError(xhr, textStatus, errorThrown) {
    var msg = 'Ошибка: ' + textStatus;
    $('#status').text(msg);
}