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

    .e-target {
        color: #DE0000;
    }
</style>

<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/jquery.json-2.2.js') ?>"></script>
<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/jquery.nano.js') ?>"></script>
<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/tests.js') ?>"></script>
<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/tests-init.js') ?>"></script>
<script type="text/javascript">
    var test,
        test_id = <?php echo isset($this->test_id) ? $this->test_id : 'null' ?>;
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