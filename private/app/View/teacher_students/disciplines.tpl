<h2>
    <a href="<?php echo $this->_links->get('teacher.students'); ?>">Мои слушатели</a>
    &gt; <?php printf('%s %s %s', $this->user_info['surname'], $this->user_info['name'], $this->user_info['patronymic']); ?>
</h2>
<h3>Отдельные дисциплины, изучаемые слушателем</h3>
<?php if (count($this->disciplines)): ?>
<ul>
<?php foreach($this->disciplines as $discipline): ?>
    <li><a href="<?php echo $this->_links->get('teacher.student_discipline', array('student_id' => $this->user_id, 'discipline_id' => $discipline['id'])) ?>"><?php echo $discipline['title']; ?></a></li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<p>Cлушатель не изучает ни одной отдельной дисциплины.</p>
<?php endif; ?>

<h3>Дисциплины, входящие в программы, изучаемые слушателем</h3>
<?php if (count($this->disciplines_programs)): ?>
<ul>
<?php foreach($this->disciplines_programs as $p_title => $discipline_group): ?>
    <li><?php echo $p_title;?>
        <ul>
        <?php foreach ($discipline_group as $discipline) : ?>
        <li><a href="<?php echo $this->_links->get('teacher.student_discipline', array('student_id' => $this->user_id, 'discipline_id' => $discipline['id'])) ?>"><?php echo $discipline['d_title']; ?></a></li>
        <?php endforeach; ?>
        </ul>
    </li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<p>Cлушатель не изучает ни одной дисциплины, входящей в программу.</p>
<?php endif; ?>