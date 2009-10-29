<h3>Доступные программы:</h3>
<ul>
    <?php foreach ($this->programs as $p): ?>
    <li>
        <?php echo $p['title'] ?>
        <ul>
            <?php foreach ($p['disciplines'] as $d): ?>
            <?php $d = (object) $d ?>
            <li><a href="/materials/show/<?php echo $d->discipline_id ?>"><?php echo $d->title ?></a></li>
            <?php endforeach; ?>
        </ul>
    </li>
    <?php endforeach; ?>
</ul> 

<br />          

<h3>Доступные дисциплины:</h3>
<ul>
    <?php foreach ($this->disciplines as $d): ?>
    <?php $d = (object) $d ?>
    <li><a href="/materials/show/<?php echo $d->discipline_id ?>"><?php echo $d->title ?></a></li>
    <?php endforeach; ?>
</ul>