<?php if (!empty($this->programs)): ?>
<h3>Доступные программы:</h3>
<ul>
    <?php foreach ($this->programs as $p): ?>
    <li>
        <?php echo $p['title'] ?>
        <ul>
            <?php foreach ($p['disciplines'] as $d): ?>
            <?php $d = (object) $d ?>
            <li><a href="<?php echo $this->_links->get('materials.show', array('discipline_id' => $d->discipline_id,
                                                                               'app_id'        => $p['app_id'])) ?>"><?php echo $d->title ?></a></li>
            <?php endforeach; ?>
        </ul>
    </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if (!empty($this->disciplines)): ?>
<h3>Доступные дисциплины:</h3>
<ul>
    <?php foreach ($this->disciplines as $d): ?>
    <?php $d = (object) $d ?>
    <li><a href="<?php echo $this->_links->get('materials.show', array('discipline_id' => $d->discipline_id,
                                                                       'app_id'        => $d->app_id)) ?>"><?php echo $d->title ?></a></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if (empty($this->programs) && empty($this->disciplines)): ?>
<b>Вами не выбрано ни одной программы для обучения:</b>
<ol>
    <li>В разделе <a href="<?php echo $this->_links->get('student.apply') ?>" target=blank>"Новый курс"</a> выберите интересующую Вас учебную программу;</li>
    <li>Нажмите кнопку "Подать заявку", и подайте заявку на обучение по выбранной демонстрационной программе.<br>Статус заявки на обучение по выбранному направлению, можно посмотреть в разделе <a href="<?php echo $this->_links->get('student.applications') ?>" target=blank>"Мои заявки"</a>;</li>
    <li>После этого Вы сможете получить доступ к интересующим Вас материалам в разделе <a href="<?php echo $this->_links->get('student.programs') ?>" target=blank>"Мои курсы"</a>.*</li>
</ol>
<?php endif; ?>