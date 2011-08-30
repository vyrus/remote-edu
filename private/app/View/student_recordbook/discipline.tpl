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

$cw = array();
foreach ($this->control_works as $rec) {
    $section_title = $rec['section_title'];
    unset($rec['section_title']);
    if (!array_key_exists($section_title,$cw)) $cw[$section_title] = array();
	array_push($cw[$section_title],$rec);
}
$links = Resources::getInstance()->links;
$cm = array();
$TYPE_NAMES = $this->control_names_map;
$MARK_NAMES = $this->mark_names_map;
?>

<style type="text/css">
	tr.odd, th.odd {
		background-color: #ECECEC;
	}
	
	tr.even, th.even {
		background-color: #FFFFFF;
	}
	
	th {
		font-weight: bold;
	}
	
	table.control td {
	    text-align: center;
	    padding: 5px 10px;
    }	
</style>

<h2>
    <a href="<?php echo $this->_links->get('student.record_book'); ?>">Моя зачётная книжка</a>
    &gt; &laquo;<?php echo $this->discipline_title; ?>&raquo;
</h2><br><br>

<!--Зачеты по разделам дисциплины-->
<h3>Зачеты по разделам дисциплины</h3><br>
<?php if (count($this->credits)): ?>
    <table class="control">
        <tr class="odd">
            <th>Наименование раздела</th>
            <th>Отметка о зачете</th>
            <th>Дата</th>
        </tr>
        <?php foreach($this->credits as $i => $credit): ?>
            <tr<?php if ($i % 2): ?> class="odd"<?php else: ?> class="even"<?php endif; ?>>
                <td><?php echo $credit['title'] ?></td>
                <td><?php echo !empty($credit['section_id']) ? 'зачтено' : 'не зачтено'; ?></td>
                <td><?php echo !empty($credit['created_date']) ? _reformatMysqlDatetime($credit['created_date']) : '&mdash;'; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    Дисциплина пока не содержит разделов
<?php endif; ?>
<br><br>

<!--Контрольные работы-->
<h3>Контрольные работы</h3><br>
<?php if (count($cw)): ?>
    <table class="control" cellspacing="2" cellpadding="0" width='100%'>
    <tr class="odd">
        <th class="title" width="20%">Название раздела</th>
        <th class="control_work">Контрольные работы</th>
    </tr>
    <?php foreach ($cw as $section_title => $cw_section) : ?>
        <tr>
            <td width="20%">
                <?php echo $section_title; ?>
            </td>
            <td >
                <table class="control_work" width='100%'>
                    <tr class="odd">
                        <th class="control_name" width="40%">Имя</th>
                        <th class="control_type" width="20%">Тип</th>
                        <th class="mark" width="20%">Оценка</th>
                        <th class="control_date" width="20%">Дата</th>
                    </tr>
                    <?php foreach ($cw_section as $i => $rec): ?>
                        <tr<?php if ($i % 2): ?> class="odd"<?php else: ?> class="even"<?php endif; ?>>
                            <td class="control_name"><?php 
                                    switch ($rec['control_material_type']) {
                                        case 'practice': echo $rec['title_material']; break; 
                                        case 'control': echo $rec['title_material']; break; 
                                        case 'test': echo $rec['title_test']; break; 
                                        case 'credit' : echo 'Зачет'; break;
                                    }
                                    ?></td>
                            <td class="control_type"><?php echo $TYPE_NAMES[$rec['control_material_type']]; ?></td>
                            <td class="mark"><?php echo empty($rec['mark']) ? 'не оценена' : $MARK_NAMES[$rec['mark']]; ?></td>
                            <td class="control_date"><?php echo !empty($rec['control_date']) ? _reformatMysqlDatetime($rec['control_date']) : '&mdash;'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
<?php else: ?>
    Дисциплина пока не содержит контрольных работ
<?php endif; ?>
<br><br>

<!--Тесты -->
<h3>Подробные результаты тестирования</h3><br>
<div id="results">
    <table class="control">
        <tr class="odd"`>
            <th>Тест</th>
            <th>Результат</th>
            <th>Ошибок</th>
            <th>Время</th>
            <th>Попытка</th>
            <th>Дата</th>
        </tr>

        <?php $tr_class = array('odd', 'even') ?>

        <?php foreach ($this->test_results as $r): ?>
        <tr class="<?php echo next($tr_class) ? current($tr_class) : reset($tr_class) ?>">

            <td>
                <?php echo $r['theme']?>
            </td>

            <td>
                <?php echo ('true' == $r['passed']) ? 'зачтено' : 'не зачтено'; ?>
            </td>

            <td>
                <?php echo $r['num_errors'] ?> &ndash;
                <?php echo round($r['num_errors'] / $r['num_questions'] * 100, 2) ?>%
            </td>

            <td><?php echo _formatTimeMin($r['time']) ?></td>

            <td>
                <?php echo $r['attempt_num'] ?> из <?php echo $r['attempts_limit']?>

                <?php if ($r['extra_attempts']): ?>
                    <span class="extra-attempts"> + <?php echo $r['extra_attempts'] ?></span>
                <?php endif; ?>

            </td>

            <td><?php echo _reformatMysqlDatetime($r['created']) ?></td>
        </tr>
        <?php endforeach; ?>

        <?php if (!count($this->test_results)): ?>
        <tr class="odd">
            <td colspan="6" style="text-align: center;">
               Вы еще не проходили ни одного тестирования
            </td>
        </tr>
        <?php endif; ?>
    </table>
</div>
