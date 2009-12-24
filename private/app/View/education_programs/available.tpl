<?php if(!empty($this->programs)) { ?>
<h3>Доступные программы:</h3>
<ul>
    <?php foreach ($this->programs as $p): ?>
    <li>
        <?php echo $p['title'] ?>
        <ul>
            <?php foreach ($p['disciplines'] as $d): ?>
            <?php $d = (object) $d ?>
            <li><a href="/educational_materials/show/<?php echo $d->discipline_id ?>/<?php echo $p['app_id'] ?>/"><?php echo $d->title ?></a></li>
            <?php endforeach; ?>
        </ul>
    </li>
    <?php endforeach; ?>
</ul> 
<?php 
}elseif(!empty($this->disciplines)) { ?>
<br />
<h3>Доступные дисциплины:</h3>
<ul>
    <?php foreach ($this->disciplines as $d): ?>
    <?php $d = (object) $d ?>
    <li><a href="/educational_materials/show/<?php echo $d->discipline_id ?>/<?php echo $d->app_id ?>/"><?php echo $d->title ?></a></li>
    <?php endforeach; ?>
</ul>
<?php
}else {
?>
<b>Вами не выбрано ни одной программы для обучения:</b>
<ol>
    <li>В разделе <a href="/applications/index_by_student/" target=blank>
"Новый курс"</a> выберите интересующую Вас учебную программу;</li>
    <li>Нажмите кнопку "Подать заявку", и подайте заявку на обучение по выбранной демонстрационной программе.<br>
    Статус заявки на обучение по выбранному направлению, можно посмотреть в разделе
    <a href=/applications/list_by_student/" target=blank>"Мои заявки"</a>;</li>
    <li>После этого Вы сможете получить доступ к интересующим Вас материалам в разделе
    <a href="/education_programs/available/" target=blank>"Мои курсы"</a>.*</li>

</ol>
<?php } ?>