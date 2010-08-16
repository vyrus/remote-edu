<style type="text/css">
    div#controls {
        /*margin-bottom: 1.7em;*/
    }

    .question-wrapper {
        margin-top: 1em;
    }

    .question-expanded, .question-collapsed {
        margin-top: 0.5em;
    }

    .question-form {
        /*border: 1px solid red;*/
        /*margin: 1.7em 0px;*/
    }

    .question-text {
        overflow: auto;
        width: 30em;
        height: 1.25em;
        font: 1.7em Tahoma, Verdana, Arial;
        border: 1px solid #e8e8e8;
    }

    .ui-resizable-se {
        bottom: 14px;
        right: 2px;
    }

    .answer-container {
        margin-left: 1.5em;
    }

    .answer-container td {
        padding: 0;
        margin: 0;
    }

    .question-radio {
        margin-top: -1em;
    }

    .question-answer {
        /*border: 1px solid green;*/
        overflow: auto;
        width: 15em;
        height: 1.3em;
        font: 1em Tahoma, Verdana, Arial;
        border: 1px solid #e8e8e8;
    }

    td.error-target {
        padding-top: 0.2em;
        padding-left: 0.7em;
        vertical-align: top;
    }

    #questions {
        margin-top: 1em;
    }

    #status {
        margin-left: 7px;
    }

    .inactive {
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

<div id="questions" style="display: none;">
    <a href="#" id="lnk-add-question">Добавить вопрос</a>
    <a href="#" id="lnk-show-all"></a>
    <a href="#" id="lnk-hide-all"></a>
    <a href="#" id="lnk-toggle-all"></a>
</div>