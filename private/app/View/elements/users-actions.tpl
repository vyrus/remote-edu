
<?php
    
    $user_action_elements = array(
        'Регистрация сотрудника'	=> 'register_employee',
        'Регистрация слушателя' 	=> 'register_student',
		'Вход'						=> 'login',
        'Кто я?'                    => 'whoami',
        'Расширенный профиль'       => 'profile_extended',
		'Выход'       => 'logout'
    );
	
    //Wtfi
    $cur_ctrl = $_SERVER['REQUEST_URI'];
	$prefix = '/users/';
?>

<?php foreach ($user_action_elements as $title => $controller): ?>
    <?php if ('/users/'. $controller == strtolower ($cur_ctrl)): ?>
        <li class="headli active"><?php echo $title; ?></li>
    <?php else: ?>
        <li class="headli">
			<a href="/users/<?php echo $controller ?>"><?php echo $title ?></a>
		</li>
    <?php endif; ?>
<?php endforeach; ?>