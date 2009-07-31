<?php $this->title = 'Администратор'; ?>
<h3>Список администраторов и преподавателей</h3>
<ul>
<?php
    foreach ($this->admins as $value) {
        echo '<li>' . $value['login'] . "\n";
    }
?>
</ul>