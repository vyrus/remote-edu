$(document).ready(function()
{
    test = new Test();
    test.setId(test_id);

    $('#btn-start').click(function() {
        startExamination();
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

    test.setExamQuestions(response.questions, $('#questions'));

    $('#status').hide();
}

function onAjaxError(xhr, textStatus, errorThrown) {
    var msg = 'Ошибка: ' + textStatus;
    $('#status').text(msg);
}