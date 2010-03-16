<?php
    $rolesCaptions = $this->rolesCaptions;
    $users = $this->users;
?>

<style type="text/css">
	tr.odd, th.odd {
		background-color: #ECECEC;
	}
	
	tr.even, th.even {
		background-color: #FFFFFF;
	}
	
	th {
		font-weight: bold;
	}
	
	table.users td {
	    text-align: center;
	    padding: 5px 10px;
    }	
</style>

<h3>Список пользователей системы</h3>
<table class="users" border="0" cellspacing="2" cellpadding="0">
<tr class="odd"><th class="id">ID</th><th class="login">Логин</th><th class="fio">ФИО</th><th class="role">Роль в системе</th><th></th></tr>
<?php foreach ($users as $i => $user): ?>
<tr<?php if ($i % 2): ?> class="odd"<?php else: ?> class="even"<?php endif; ?>><td class="id"><?php echo $user['user_id']; ?></td><td class="login"><?php echo $user['login']; ?></td><td class="fio"><?php echo $user['surname'] . ' ' . $user['name'] . ' ' . $user['patronymic']; ?></td><td class="role"><?php echo $rolesCaptions[$user['role']]; ?></td><td class="edit"><a href="<?php echo $this->_links->get('users.edit', array('user_id' => $user['user_id'])) ?>">редактировать</a></td></tr>
<?php endforeach; ?>
</table>
