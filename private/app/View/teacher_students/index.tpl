<h3>Мои слушатели</h3>
<?php if (count($this->listeners)): ?>
<ul>
<?php foreach($this->listeners as $listener): ?>
    <li><a href="<?php echo $this->_links->get('teacher.student_disciplines', array('student_id' => $listener['user_id'])) ?>"><?php printf('%s %s %s', $listener['surname'], $listener['name'], $listener['patronymic']); ?></a></li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<p>У Вас нет ни одного слушателя.</p>
<?php endif; ?>