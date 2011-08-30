<?php
    function _reformatMysqlDatetime($datetime) {
        return date('d.m.Y H:i:s', strtotime($datetime));
    }
?>
<h3>Студенты, изучающие дисциoплину &laquo;<?php echo $this->discipline_title; ?>&raquo;</h3>
<form method="post">
<input type="hidden" name="discipline_id" value="<?php echo $this->discipline_id; ?>">
<?php if (count($this->data) > 0) { ?>
<table class="checkpoints">
    <tr>
        <th class="column_student">Ф.И.О. студента</th>
        <th>Контрольные точки</th>
    </tr>
    <?php foreach($this->data as $student_id => $student) {  ?>
    <tr>
        <td><?php echo $student['name']; ?></td>
        <td>
            <table>
                <tr>
                    <th></th>
                    <th>Раздел</th>
                    <th class="column_status">Зачет</th>
                    <th class="column_status">Статус</th>
                    <th class="column_date">Дата зачета</th>
                </tr>
                <?php
                    foreach($this->sections as $section) {
                        if (in_array($section['section_id'], $student['open_sections'])) {
                            $open_status = 'доступен';
                        } else {
                            $open_status = 'не доступен';
                        }
                        $i = array_search($section['section_id'], $student['credit_sections']['ids']);
                        if ($i !== false) {
                            $credit_status = 'зачтено';
                            $credited_date = _reformatMysqlDatetime($student['credit_sections']['dates'][$i]);
                        } else {
                            $credit_status = 'не зачтено';
                            $credited_date = '&mdash;';
                        }
                ?>
                <tr>
					<td><input type="checkbox" name="open[<?php echo $student_id; ?>][<?php echo $section['section_id']; ?>]"></input></td>
                    <td>Раздел <?php echo $section['number'] . '. ' . $section['title']; ?></td>
                    <td class="column_center"><?php echo $credit_status; ?></td>
                    <td class="column_center"><?php echo $open_status; ?></td>
                    <td class="column_center"><?php echo ($credited_date) ?></td>
                </tr>
                <?php if (array_key_exists('first_uncredited_section', $student) && ($student['first_uncredited_section'] == $section['section_id'])) : ?>
                <tr>
                    <td colspan="5">
                       <table> 
                           <tr>
                                <th>Наименование</th>
                                <th>Тип</th>
                                <th>Зачет</th>
                                <th>Текущая оценка</th>
                                <th>Дата оценки</th>
                                <th>Оценить</th>
                           </tr>
                           <?php foreach ($student['cur_control_works'] as $rec) : ?>
                           <tr> 
                                <td class="control_name"><?php 
                                        switch ($rec['control_material_type']) {
                                            case 'practice': echo $rec['title_material']; break; 
                                            case 'control': echo $rec['title_material']; break; 
                                            case 'test': echo $rec['title_test']; break; 
                                            case 'credit' : echo 'Зачет'; break;
                                        }
                                        ?></td>
                                <td class="control_type"><?php echo $this->TYPE_NAMES[$rec['control_material_type']]; ?></td>
                                <td class="credit"><?php echo ($rec['auto_set_credit']) ? 'Да' : 'Нет'; ?></td>
                                <td><?php echo (!empty($rec['mark'])) ?  $this->MARK_NAMES[$rec['mark']] : '&mdash;'; ?></td>
                                <td><?php echo (!empty($rec['control_date'])) ? _reformatMysqlDatetime($rec['control_date']) : '&mdash;'; ?></td>
                                <td><?php if ($rec['control_material_type'] != 'test') : ?>
                                    <select name="mark[<?php echo $student_id; ?>][<?php echo $rec['control_id']; ?>]">
                                        <option value="null">не оценена</option>
                                        <?php foreach ($this->MARK_NAMES as $mark_id => $mark_name): ?>
                                            <option value = "<?php echo $mark_id; ?>"><?php echo $mark_name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php else: ?>
                                    оценивается автоматически
                                    <?php endif; ?></td>

                                
                           </tr>
                           <?php endforeach; ?>
                       </table>
                    </td>
                </tr>
                <?php endif; ?>
                <?php } // endforeach sections ?>
            </table>
        </td>
    </tr>
    <?php } ?>
</table>
<br>
<a href="javascript:openSections()">Открыть отмеченные разделы</a>&nbsp;&nbsp;
<a href="javascript:closeSections()">Закрыть отмеченные разделы</a>&nbsp;&nbsp;
<a href="javascript:saveMarks()">Сохранить проставленные оценки</a>
<?php } else { ?>
<p>Эта дисциплина не изучается ни одним слушателем.</p>
<?php } ?>
</form>
<script>

    function openSections() {
        document.forms[0].action = "<?php echo $this->_links->get('teacher.sections.set_open'); ?>";
        document.forms[0].submit();

    }

    function closeSections() {
        document.forms[0].action = "<?php echo  $this->_links->get('teacher.sections.set_close'); ?>";
        document.forms[0].submit();

    }

    function saveMarks() {
        document.forms[0].action = "<?php echo $this->_links->get('teacher.set_marks'); ?>";
        document.forms[0].submit();
    }

</script>
