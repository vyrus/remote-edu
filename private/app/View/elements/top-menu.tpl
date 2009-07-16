<?php
    
    $elements = array(
        'Index'        => 'index',
        'Пользователи' => 'users',
        'Ошибки'       => 'error'
    );
    
    $cur_ctrl = $this->_request->_router['handler']['controller'];

?>
<?php foreach ($elements as $title => $controller): ?>
    <?php if ($controller == $cur_ctrl): ?>
        <li class="active"><strong><?php echo $title; ?></strong></li>
    <?php else: ?>
        <li><a href="/<?php echo $controller ?>/index/"><?php echo $title ?></a></li>
    <?php endif; ?>
<?php endforeach; ?>