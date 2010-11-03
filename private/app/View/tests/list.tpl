<div style="margin-bottom: 1.2em;">
    <a href="<?php echo $this->_links->get('tests.create') ?>">Новый тест</a>
</div>

<script type="text/javascript">
    function confirmDelete() {
        if (confirm('Вы действительно хотите удалить тест?')) {
            return true;
        }

        return false;
    }
</script>

<table>
    <tr>
        <th>Название теста</th>
        <td></td>
        <td></td>
    </tr>

    <?php foreach ($this->tests as $t): ?>
    <tr>
        <td><?php echo $t->theme ?></td>
        <td>
            <a href="<?php echo $this->_links->get('tests.results', array('test_id' => $t->test_id)) ?>">результаты</a>
            <a href="<?php echo $this->_links->get('tests.edit', array('test_id' => $t->test_id)) ?>">редактировать</a>
            <a href="<?php echo $this->_links->get('tests.delete', array('test_id' => $t->test_id)) ?>" onclick="return confirmDelete();">удалить</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
