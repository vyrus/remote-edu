<h3>Студенты, изучающие дисциплину &laquo;<?php echo $this->discipline_title; ?>&raquo;</h3>
<?php if (count($this->students) > 0) { ?>
<ul>
    <?php foreach($this->students as $student) { ?>
    <li><?php echo implode(' ', array($student['surname'], $student['name'], $student['patronymic'])); ?>
    <?php } ?>
</ul>
<?php } else { ?>
<p>Эту дисциплину не изучает ни один студент.</p>
<?php } ?>