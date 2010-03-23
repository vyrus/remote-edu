<?php $this->title = $this->discipline['title'] ?>
<h1 class="title_discipline"><?php echo $this->discipline['title'] ?></h1>
<p style="margin: 1em 0;"><a href="<?php echo $this->_links->get('messages.send') . $this->discipline['responsible_teacher']; ?>">Написать сообщение преподавателю</a></p>
<?php $i = 0; ?>
<?php foreach ($this->sections as $s): ?>
    <?php $i++; ?>
    <?php $s = (object) $s; ?>
    <h2 class="title_section">Раздел <?php echo $i . '. ' . $s->title ?></h2>
    <?php if (empty($this->materials[$s->section_id])) continue; ?>
    <h3 class="title_materials">Лекционный материал</h3>
        <ul class="materials">
        <?php foreach ($this->materials[$s->section_id] as $m): ?>
            <?php if ('last' == $m['state']): ?>
            <li style="background-color: #cfc;"><a href="<?php echo $this->_links->get('materials.download', array('material_id' => $m['id'])) ?>"><?php echo $m['description'] ?></a>
            <?php elseif ('downloaded' == $m['state']): ?>
            <li style="background-color: #ccc;"><a href="<?php echo $this->_links->get('materials.download', array('material_id' => $m['id'])) ?>"><?php echo $m['description'] ?></a>
            <?php else: ?>
            <li><a href="<?php echo $this->_links->get('materials.download', array('material_id' => $m['id'])) ?>"><?php echo $m['description'] ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
        </ul>
<?php endforeach; ?>