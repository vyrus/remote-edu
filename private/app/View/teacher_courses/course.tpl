<h3>Студенты, изучающие курс &laquo;<?php echo $this->course_title; ?>&raquo;</h3>
<?php if (count($this->students) > 0) { ?>
<ul>
    <?php foreach($this->students as $student) { ?>
    <li><?php echo implode(' ', array($student['surname'], $student['name'], $student['patronymic'])); ?>
    <?php } ?>
</ul>
<?php } else { ?>
<p>Этот курс не изучает ни один студент.</p>
<?php } ?>