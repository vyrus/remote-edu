<?php

    function _formatTimeMin($seconds) {
        $minutes = round($seconds / 60);
        $seconds = $seconds % 60;

        $output = ($minutes > 0 ? $minutes . ' мин ' : '').
                  ($seconds > 0 ? $seconds . ' cек' : '');
        return $output;
    }

    function _reformatMysqlDatetime($datetime) {
        return date('d.m.Y H:i:s', strtotime($datetime));
    }

?>
<style type="text/css">
    #results {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 1.15em;
    }

    #results table {
        border: 1px solid #e1e1e1;
    }

    #results table th, td {
        padding: 0;
        margin: 0;
    }

    #results table th {
        background-color: #e1e1e1;
        border-bottom: 1px solid #cccccc;
        font-weight: normal;
        color: #363636;
        padding: 5px 10px;
        text-align: left;
    }

    #results table td {
        color: #363636;
        padding: 3px 10px 5px 10px;
    }

    tr.odd td {
        background-color: #ebebeb;
    }

    tr.even td {
        background-color: #ffffff;
    }

    .extra-attempts {
        color: #7d7d;
    }
</style>

<div id="results">
    <table cellspacing="0px" cellpadding="0px">
        <tr>
            <th>Дата</th>
            <th>Пользователь</th>
            <th>Тест</th>
            <th>Результат</th>
            <th>Ошибок</th>
            <th>Время</th>
            <th>Попытка</th>
        </tr>

        <?php $tr_class = array('even', 'odd') ?>

        <?php foreach ($this->results as $r): ?>
        <tr class="<?php echo next($tr_class) ? current($tr_class) : reset($tr_class) ?>">
            <td><?php echo _reformatMysqlDatetime($r->created) ?></td>

            <td>
                <?php echo $r->surname . ' ' .
                           mb_substr($r->name, 0, 1, 'UTF-8'). '. ' .
                           mb_substr($r->patronymic, 0, 1, 'UTF-8') . '.' ?>
                <a href="<?php echo $this->_links->get('users.profile', array('user_id' => $r->user_id)); ?>" title="Подробная анкета слушателя" target="_blank">&rarr;</a>
            </td>

            <td>
                <?php echo $r->theme?>
                <a href="<?php echo $this->_links->get('tests.edit', array('test_id' => $r->test_id)); ?>" title="Редактирование теста" target="_blank">&rarr;</a>
            </td>

            <td>
                <img src="<?php echo $this->_links->getPath(
                    '/images/' . ('true' == $r->passed ? 'plus' : 'minus') . '.png'
                ) ?>" />
            </td>

            <td>
                <?php echo $r->num_errors ?>,
                <?php echo round($r->num_errors / $r->num_questions * 100, 2) ?>%
            </td>

            <td><?php echo _formatTimeMin($r->time) ?></td>

            <td>
                <?php echo $r->attempt_num ?> из <?php echo $r->attempts_limit ?>

                <?php if ($r->extra_attempts): ?>
                    <span class="extra-attempts"> + <?php echo $r->extra_attempts ?></span>
                <?php endif; ?>

                <a href="<?php echo $this->_links->get(
                    'tests.add-extra-attempt', array('user_id' => $r->user_id,
                                                     'test_id' => $r->test_id)
                ) ?>" title="Добавить дополнительную попытку">+</a>
            </td>
        </tr>
        <?php endforeach; ?>

        <?php if (!$this->results->rowCount()): ?>
        <tr class="odd">
            <td colspan="7" style="text-align: center;">
                Тестирования ещё не проводились
            </td>
        </tr>
        <?php endif; ?>
    </table>
</div>