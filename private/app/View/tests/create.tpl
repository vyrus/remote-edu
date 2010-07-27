<style type="text/css">
    div#controls {
        /*margin-bottom: 1.7em;*/
    }

    .question-form {
        /*border: 1px solid red;*/
        margin: 1.7em 0px;
    }

    .question-text {
        /*border: 1px solid blue;*/
        display: block;
    }

    .question-radio {
        /*border: 1px solid black;*/
    }

    .question-answer {
        /*border: 1px solid green;*/
        /*display: block;*/
    }

    #questions {
        margin-top: 1em;
    }

    #status {
        margin-left: 7px;
    }

    input.inactive {
        color: #BFBFBF;
    }

    #options {
        margin-top: 0.6em;
    }

    #options table tr, #options table td {
        margin: 0px;
        padding: 0px;
    }

    #options td.label {
        text-align: right;
        padding-right: 0.3em;
        line-height: 2.5em;
    }

    #options td.error {
        text-align: left;
        padding-left: 0.7em;
        color: #DE0000;
    }
</style>

<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/test-create.js') ?>"></script>
<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/jquery.json-2.2.js') ?>"></script>
<script type="text/javascript">
    var test = new Test(),
        test_id = <?php echo isset($this->test_id) ? $this->test_id : 'null' ?>;

    $(document).ready(function()
    {
        $('#lnk-save').click(function() {
            saveOptions();
        });

        $('#lnk-add-question').click(function() {
            test.addPickOne($('#questions'));
        });

        $('#frm-options').submit(function() {
            $('#lnk-save').click();
            return false;
        });

        if (null !== test_id) {
            test.setId(test_id);
            loadTest(test_id);
        }
    })

    function saveOptions() {
        $('#status').text('Сохранение...').show();
        $('td.error').text('');

        var options = test.saveOptions();

        $.ajax({
            type: 'POST',
            url: '/tests/ajax_save_options',
            data: options,
            dataType: 'json',

            success: function(response) {
                if (
                    response.result != true &&
                    undefined !== response.formErrors
                ) {
                    for (field in response.formErrors) {
                        $('#' + field)
                            .parent()
                            .next()
                            .text(response.formErrors[field]);
                    }
                    $('#status').hide();
                }
                else if(response.result != true) {
                    var msg = 'Не удалось сохранить тест. ' +
                               response.error;
                    $('#status').text(msg);
                }
                else
                {
                    if (undefined !== response.testId) {
                        test.setId(response.testId);
                    }

                    saveQuestions();

                    $('#lnk-add-question').show();
                }
            },

            error: function(xhr, textStatus, errorThrown) {
                var msg = 'Ошибка: ' + textStatus;
                $('#status').text(msg);
            }
        });
    }

    function saveQuestions() {
        var questions = test.saveQuestions();

        if (!questions.length) {
            $('#status').hide();
            return;
        }

        questions = $.toJSON(questions);
        alert(questions);

        var data = {
            testId: test.getId(),
            questions: questions
        }

        $.ajax({
            type: 'POST',
            url: '/tests/ajax_save_questions',
            data: data,
            dataType: 'json',

            success: function(response) {
                if (response.result != true) {
                    var msg = 'Не удалось сохранить вопросы. ' + response.error;
                    $('#status').text(msg);
                } else {
                    $('#status').hide();
                }
            },

            error: function(xhr, textStatus, errorThrown) {
                var msg = 'Ошибка: ' + textStatus;
                $('#status').text(msg);
            }
        });
    }

    function loadTest(tid) {
        $('#status').text('Загрузка...').show();

        $.ajax({
            type: 'POST',
            url: '/tests/ajax_load_test',
            data: {test_id: tid},
            dataType: 'json',

            success: function(response) {
                if (response.result != true) {
                    var msg = 'Не удалось загрузить параметры теста. ' +
                              response.error;
                    $('#status').text(msg);
                } else {
                    test.setOptions(response.options);
                    test.setQuestions(response.questions, $('#questions'));

                    $('#status').hide();
                    $('#lnk-add-question').show();
                }
            },

            error: function(xhr, textStatus, errorThrown) {
                /**
                * @todo Вынести отображение ошибок в отдельную функцию.
                */
                var msg = 'Ошибка: ' + textStatus;
                $('#status').text(msg);
            }
        });
    }
</script>

<div id="controls">
    <a href="#" id="lnk-save">Сохранить</a>
    <span id="status" style="display: none;"></span>
</div>

<div id="options">
    <form id="frm-options">
    <table>
        <tr>
            <td class="label">Тема тестирования:</td>
            <td><input type="text" id="theme" name="theme" style="width: 15em;" /></td>
            <td class="error"></td>
        </tr>

        <tr>
            <td class="label">Количество вопросов в тесте:</td>
            <td><input type="text" id="num_questions" name="num_questions" style="width: 1.5em;" /></td>
            <td class="error"></td>
        </tr>

        <tr>
            <td class="label">Допустимое количество ошибок:</td>
            <td><input type="text" id="errors_limit" name="errors_limit" style="width: 1.5em;" />&nbsp;%</td>
            <td class="error"></td>
        </tr>

        <tr>
            <td class="label">Количество попыток тестирования:</td>
            <td><input type="text" id="attempts_limit" name="attempts_limit" style="width: 1.5em;" /></td>
            <td class="error"></td>
        </tr>
    </table>
    </form>
</div>

<div id="questions">
    <a href="#" id="lnk-add-question" style="display: none;">Добавить вопрос</a>
</div>