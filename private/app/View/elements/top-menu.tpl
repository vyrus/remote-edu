<?php
    
    $elements = array(
        'Index'        				=> 'index',
        'Пользователи' 				=> 'users',
		'Образовательные программы'	=> 'education_programs',
		'Учебные Материалы'			=> 'educational_materials',
        'Заявки'                    => 'applications',
        'Ошибки'       				=> 'error'
    );
    
    $cur_ctrl = $this->_request->_router['handler']['controller'];
?>
<?php foreach ($elements as $title => $controller): ?>
    <?php if ($controller == strtolower ($cur_ctrl)): ?>
        <li class="active"><?php echo $title; ?></li>
    <?php else: ?>
        <li><a href="/<?php echo $controller ?>/index/"><?php echo $title ?></a></li>
    <?php endif; ?>
<?php endforeach; ?>