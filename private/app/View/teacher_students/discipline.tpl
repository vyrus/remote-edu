<h2>
    <a href="<?php echo $this->_links->get('teacher.students'); ?>">Мои слушатели</a>
    &gt; <a href="<?php echo $this->_links->get('teacher.student_disciplines', array('student_id' => $this->user_id)); ?>"><?php printf('%s %s %s', $this->user_info['surname'], $this->user_info['name'], $this->user_info['patronymic']); ?></a>
    &gt; &laquo;<?php echo $this->discipline_title; ?>&raquo;
</h2>
<?php if (count($this->checkpoints)): ?>
<table class="table_simple">
<?php foreach($this->checkpoints as $checkpoint): ?>
    <tr>
        <td><?php echo $checkpoint['title'] ?></td>
        <td><?php echo $checkpoint['created'] ? 'зачтено' : 'не зачтено'; ?></td>
    </tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p>В дисциплине нет ни одного раздела.</p>
<?php endif; ?>