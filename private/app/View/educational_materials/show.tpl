<?php $this->title = $this->discipline['title'] ?>
<h1 class="title_discipline"><?php echo $this->discipline['title'] ?></h1>
<?php $i = 0; ?>
<?php foreach ($this->sections as $s): ?>
    <?php $i++; ?>
    <?php $s = (object) $s; ?>
    <h2 class="title_section">Раздел <?php echo $i .'. ' . $s->title ?></h2>
    <h3 class="title_materials">Лекционный материал</h3>
    <?php if (!isset($this->materials[$s->section_id])) continue; ?>
        <ul class="materials">
        <?php foreach ($this->materials[$s->section_id] as $m): ?>
            <?php if ('last' == $m['state']): ?>
            <li style="background-color: #cfc;"><a href="/educational_material/<?php echo $m['id'] ?>"><?php echo $m['description'] ?></a>
            <?php elseif ('downloaded' == $m['state']): ?>
            <li style="background-color: #ccc;"><a href="/educational_material/<?php echo $m['id'] ?>"><?php echo $m['description'] ?></a>
            <?php else: ?>
            <li><a href="/educational_material/<?php echo $m['id'] ?>"><?php echo $m['description'] ?></a>
            <?php endif; ?>
            <?php endforeach; ?>
        </ul>
<?php endforeach; ?>