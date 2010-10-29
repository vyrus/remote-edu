<h3>Студенты, изучающие дисциплину &laquo;<?php echo $this->discipline_title; ?>&raquo;</h3>
<?php if (count($this->students) > 0) { ?>
<table class="checkpoints">
    <tr>
        <th class="column_student">Ф.И.О. студента</th>
        <th>Контрольные точки</th>
    </tr>
    <?php foreach($this->students as $student) { ?>
    <tr>
        <td><?php echo implode(' ', array($student['surname'], $student['name'], $student['patronymic'])); ?></td>
        <td>
            <table>
                <tr>
                    <th>Раздел</th>
                    <th class="column_status">Статус</th>
                    <th class="column_date">Дата</th>
                    <th class="column_action">Действие</th>
                </tr>
                <?php
                    foreach($this->sections as $section) {
                        if (isset($this->checkpoints[$student['user_id']][$section['section_id']])) {
                            $created = $this->checkpoints[$student['user_id']][$section['section_id']];
                            $status = 'доступен';
                            $action = $this->_links->get('teacher.remove_checkpoint_pass', array('student_id' => $student['user_id'], 'section_id' => $section['section_id']));
                            $action_title = 'отменить доступ';
                        } else {
                            $created = '&mdash';
                            $status = 'не доступен';
                            $action = $this->_links->get('teacher.set_checkpoint_pass', array('student_id' => $student['user_id'], 'section_id' => $section['section_id']));
                            $action_title = 'сделать доступным';
                        }
                ?>
                <tr>
                    <td>Раздел <?php echo $section['number']; ?>. <?php echo $section['title']; ?></td>
                    <td class="column_center"><?php echo $status; ?></td>
                    <td class="column_center"><?php echo $created; ?></td>
                    <td class="column_center"><a href="<?php echo $action; ?>"><?php echo $action_title; ?></a></td>
                </tr>
                <?php } ?>
            </table>
        </td>
    </tr>
    <?php } ?>
</table>
<?php } else { ?>
<p>Эта дисциплина не изучается ни одним слушателем.</p>
<?php } ?>