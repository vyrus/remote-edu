<?php $test = (object) $this->test ?>

<style type="text/css">
    #intro td {
        padding: 0;
        margin: 0;
    }

    #intro td.theme {
        text-align: center;
        font: 2.3em Georgia;
        padding-bottom: 0.5em;
    }

    #intro td.label {
        text-align: right;
        line-height: 1.7em;
        padding-right: 0.5em;
    }

    #status {
        margin-top: 1.7em;
    }

    #questions {
        margin-top: 1.7em;
    }

    .exam-container {
        margin-bottom: 1.5em;
    }

    .exam-question {
        margin-bottom: 0.5em;
    }

    .exam-answer {
        line-height: 1.7em;
    }
</style>

<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/jquery.json-2.2.js') ?>"></script>
<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/jquery.nano.js') ?>"></script>
<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/tests.js') ?>"></script>
<script type="text/javascript" src="<?php echo $this->_links->getPath('/js/tests-exam.js') ?>"></script>
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
            <td><?php echo round($test->num_questions / 100 * $test->errors_limit) ?> (<?php echo $test->errors_limit ?>%)</td>
        </tr>

        <tr>
            <td class="label">Попыток:</td>
            <td><?php echo $test->attempts_limit ?> (осталось ...<?php // ?>)</td>
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