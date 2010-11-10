<h2>
    <a href="<?php echo $this->_links->get('student.record_book'); ?>">Моя зачётная книжка</a>
    &gt; &laquo;<?php echo $this->discipline_title; ?>&raquo;
</h2>
<?php if (count($this->checkpoints)): ?>
<table class="table_simple">
<?php foreach($this->checkpoints as $checkpoint): ?>
    <tr>
        <td><?php echo $checkpoint['title'] ?></td>
        <td><?php echo $checkpoint['created'] ? 'зачтено' : 'не зачтено'; ?></td>
    </tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p>В дисциплине нет ни одного раздела.</p>
<?php endif; ?>