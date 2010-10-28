<link href="<?php echo $this->_links->getPath('/css/tests/edit.css') ?>" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/jquery.json-2.2.js') ?>"></script>
<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/jquery.nano.js') ?>"></script>
<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/jquery.scrollTo.js') ?>"></script>
<script type="text/javascript" src="<?php echo $this->_links->getPath('/AjexFileManager/ajex.js') ?>"></script>

<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/tests/base.js') ?>"></script>
<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/tests/edit.js') ?>"></script>


<script type="text/javascript">
    var test,
        test_id = <?php echo isset($this->test_id) ? $this->test_id : 'null' ?>;
</script>

<div id="controls">
    <a href="" id="lnk-save">Сохранить</a>
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
    <a href="" id="lnk-add-question">Добавить вопрос</a>
    <a href="" id="lnk-show-all"></a>
    <a href="" id="lnk-hide-all"></a>
    <a href="" id="lnk-toggle-all"></a>
</div>