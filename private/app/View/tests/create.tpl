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

    #status {
        margin-left: 7px;
    }

    input.inactive {
        color: #BFBFBF;
    }
</style>

<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/test-create.js') ?>"></script>
<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/jquery.json-2.2.js') ?>"></script>
<script type="text/javascript">
    var quests;
    var test = new Test();

    $(document).ready(function() {
        quests = $('#questions');
    })

    function addQuestion() {
        test.addPickOne(quests);
    }

    function saveTest() {
        var data = test.save();

        data.questions = $.toJSON(data.questions);
        //alert(data.questions);

        $('#status').text('Сохранение...').show();

        $.ajax({
            type: 'POST',
            url: '/tests/ajax_save',
            data: data,
            dataType: 'json',

            success: function(response){
                //alert(text);

                if (response.result != true) {
                    var msg = 'Не удалось сохранить тест. ' + response.error;
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
</script>

<div id="controls">
    <a href="#" onClick="javascript: addQuestion(); return false;">Добавить вопрос</a>
    <a href="#" onClick="javascript: saveTest(); return false;">Сохранить</a>

    <span id="status" style="display: none;"></span>
</div>

<div id="questions"></div>