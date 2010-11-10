<?php

    /* ссылки, доступные только слушателю */
    $elements = array(
        'Учебная комната'         => $this->_links->get('student.programs'),
        'Зачетная книжка'         => $this->_links->get('student.record_book'),
        'Инструкция пользователю' => $this->_links->get('help.materials')
    );

?>

<?php foreach ($elements as $title => $link): ?>
    <li class="headli">
        <a href="<?php echo $link ?>"><?php echo $title ?></a>
    </li>
<?php endforeach; ?>