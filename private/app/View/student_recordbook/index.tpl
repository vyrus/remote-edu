<h2>Моя зачётная книжка</h2>
<h3>Мои отдельные дисциплины</h3>
<?php if (count($this->disciplines)): ?>
<ul>
<?php foreach($this->disciplines as $discipline): ?>
    <li><a href="<?php echo $this->_links->get('student.record_book.discipline', array('discipline_id' => $discipline['id'])); ?>"><?php echo $discipline['title']; ?></a></li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<p>У меня нет ни одной отдельной дисциплины.</p>
<?php endif; ?>

<h3>Мои дисциплины, входящие в программы</h3>
<?php if (count($this->disciplines_programs)): ?>
<ul>
<?php foreach($this->disciplines_programs as $discipline): ?>
    <li><a href="<?php echo $this->_links->get('student.record_book.discipline', array('discipline_id' => $discipline['id'])); ?>"><?php echo $discipline['title']; ?></a></li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<p>У меня нет ни одной дисциплины, входящей в программу.</p>
<?php endif; ?>