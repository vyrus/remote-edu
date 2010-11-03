<h3>Мои дисциплины</h3>
<?php if (count($this->disciplines) > 0) { ?>
<ul>
    <?php foreach($this->disciplines as $discipline) { ?>
    <li><a href="<?php echo $this->_links->get('teacher.discipline', array('discipline_id' => $discipline['discipline_id'])) ?>"><?php echo $discipline['title']; ?></a>
    <?php } ?></li>
</ul>
<?php } else { ?>
<p>Вы не назначены ответственным ни за одну дисциплину.</p>
<?php } ?>
<h3>Мои курсы</h3>
<?php if (count($this->courses) > 0) { ?>
<ul>
    <?php foreach($this->courses as $course) { ?>
    <li><?php echo $course['title']; ?></li>
    <?php } ?>
</ul>
<?php } else { ?>
<p>Вы не назначены ответственным ни за один курс.</p>
<?php } ?>