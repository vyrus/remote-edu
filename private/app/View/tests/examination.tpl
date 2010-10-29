<?php $test = (object) $this->test ?>

<link href="<?php echo $this->_links->getPath('/css/tests/examination.css') ?>" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/jquery.json-2.2.js') ?>"></script>
<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/jquery.nano.js') ?>"></script>

<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/tests/base.js') ?>"></script>
<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/tests/examination.js') ?>"></script>

<script type="text/javascript">
    var test,
        test_id = <?php echo $test->test_id ?>;
</script>

<div id="intro">
    <table>
        <tr>
            <td colspan="2" class="theme"><?php echo $test->theme ?></td>
        </tr>

        <tr>
            <td class="label">Вопросов:</td>
            <td><?php echo $test->num_questions ?></td>
        </tr>

        <tr>
            <td class="label">Время:</td>
            <td><?php echo round($test->time_limit / 60, 2) ?> мин</td>
        </tr>

        <tr>
            <td class="label">Допускается ошибок:</td>
            <td><?php echo Model_Test::calcAllowableErrors($test->num_questions, $test->errors_limit) ?> (<?php echo $test->errors_limit ?>%)</td>
        </tr>

        <tr>
            <td class="label">Попыток:</td>
            <td>
                <?php echo $test->attempts_limit ?>
                <?php echo ($this->extra_attempts ? ' + ' . $this->extra_attempts : '') ?>
                (осталось <?php echo $this->attempts_remaining ?>)
            </td>
        </tr>
    </table>
</div>

<div id="status">
    <input type="button" id="btn-start" value="Начать тестирование" />
</div>

<div id="questions">
    <ol></ol>
</div>

<input type="button" id="btn-finish" value="Закончить тестирование" style="display: none;" />

<div id="results" style="display: none;">
    <table>
        <tr>
            <td class="label">Время тестирование:</td>
            <td id="exam-time"></td>
        </tr>

        <tr>
            <td class="label">Правильных ответов:</td>
            <td id="exam-corr-answers"></td>
        </tr>

        <tr>
            <td class="label">Неправильных ответов:</td>
            <td id="exam-incorr-answers"></td>
        </tr>

        <tr>
            <td class="label">Процент ошибок:</td>
            <td id="exam-mistakes-perc"></td>
        </tr>

        <tr>
            <td class="label">Результат:</td>
            <td id="exam-result"></td>
        </tr>
    </table>
    <a href="#">Далее</a>
</div>