<?php $this->title = $this->discipline['title'] ?>

<h1><?php echo $this->discipline['title'] ?></h1>
<br />

<?php foreach ($this->sections as $s): ?>
    <?php $s = (object) $s ?>
    
    <h3><?php echo $s->title ?></h3>
    <?php if (!isset($this->materials[$s->section_id])) continue; ?>
    
    <ul>
        <?php foreach ($this->materials[$s->section_id] as $m): ?>
            <?php $m = (object) $m ?>
            <li><a href="/educational_material/<?php echo $m->id ?>"><?php echo $m->description ?></a></li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>