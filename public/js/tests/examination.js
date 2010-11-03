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

    var data = {
        test_id: test.getId(),
        section_id: section_id,
        sec_code: sec_code
    };

    $.ajax({
        type:     'POST',
        url:      '/tests/ajax_get_exam_questions',
        data:     data,
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

    var results = response.results;
    test.displayCorrectness(results);

    time = Math.round(results.time / 60 * 100) / 100;
    corr_answers = results.correct.length;
    incorr_answers = results.incorrect.length + results.unanswered.length;

    num_questions = corr_answers + incorr_answers;
    mistakes = Math.round(incorr_answers / num_questions * 100 * 100) / 100;
    passed = (results.passed ? 'Тест сдан' : 'Тест не сдан');

    $('#exam-time').text(time + ' мин');
    $('#exam-corr-answers').text(corr_answers);
    $('#exam-incorr-answers').text(incorr_answers);
    $('#exam-mistakes-perc').text(mistakes + '%');
    $('#exam-result').text(passed);

    $('#status').hide();
    $('#results').show();
}

function onAjaxError(xhr, textStatus, errorThrown) {
    var msg = 'Ошибка: ' + textStatus;
    $('#status').text(msg);
}