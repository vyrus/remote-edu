<?php
    
    $educationMaterialsAction = array(
        'Список материалов'		=> 'index',
        'Загрузить материалы' 	=> 'upload',
    );
	
    //Wtfi
    $cur_ctrl = $_SERVER['REQUEST_URI'];
	$prefix = '/educational_materials/';
?>

<?php foreach ($educationMaterialsAction as $title => $controller): ?>
    <?php if ($prefix . $controller == strtolower ($cur_ctrl)): ?>
        <li class="headli active"><?php echo $title; ?></li>
    <?php else: ?>
        <li class="headli">
			<a href="<?php echo $prefix . $controller; ?>"><?php echo $title ?></a>
		</li>
    <?php endif; ?>
<?php endforeach; ?>