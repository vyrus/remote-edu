<h3>Мои слушатели</h3>
<?php if (count($this->listeners)): ?>
<ul>
<?php foreach($this->listeners as $listener): ?>
    <li><?php printf('%s %s %s', $listener['surname'], $listener['name'], $listener['patronymic']); ?></li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<p>У Вас нет ни одного слушателя.</p>
<?php endif; ?>