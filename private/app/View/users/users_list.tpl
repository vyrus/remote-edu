<?php
    $rolesCaptions = $this->rolesCaptions;
    $users = $this->users;
    $links = Resources::getInstance()->links;
    $filter = $this->filter;
	$sortField = $this->sortField;
	$sortDirection = $this->sortDirection;

	function showSortArrow($field, $sortFieldDin, $sortDirectionDin) {
		$links = Resources::getInstance()->links;
		if ($field == $sortFieldDin) {
			if ($sortDirectionDin == 'asc') $s = $links->getPath('/images/down.gif');
			if ($sortDirectionDin == 'desc') $s = $links->getPath('/images/up.gif');
			echo "<img src = '$s'>";			
		}
	}
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

<script type="text/javascript">

	var sort_direction_cur = "<?php echo $sortDirection;?>";
	var sort_field_cur = "<?php echo $sortField;?>";

	function getDirectionReverse(direct) {
		if (direct == 'asc') return 'desc';
		if (direct == 'desc') return 'asc';
	}

	function filterSelectOnchange(field) {
		if (sort_field_cur == field) {
			sort_direction_cur = getDirectionReverse(sort_direction_cur);
		} else {
			sort_direction_cur = 'asc';	
		}
		window.location = '<?php echo $links->get("users.list"); ?>' + $('#filter').val() + '/' + field + '/' +	sort_direction_cur + '/';
	}

/*
    function filterSelectOnchange() {
        window.location = '<?php echo $links->get("users.list"); ?>' + $('#filter').val() + '/' + $('#sort_field').val() + '/' + $('#sort_direction').val();
    }
*/
</script>


<h3>Список пользователей системы</h3>
Фильтр
<select id="filter" onchange="filterSelectOnchange()">
	<option value="all"<?php if ($filter == 'all'):?> selected="selected"<?php endif; ?>>Все</option>
	<option value="admin"<?php if ($filter == 'admin'):?> selected="selected"<?php endif; ?>>Администраторы</option>
	<option value="teacher"<?php if ($filter == 'teacher'):?> selected="selected"<?php endif; ?>>Преподаватели</option>
	<option value="student"<?php if ($filter == 'student'):?> selected="selected"<?php endif; ?>>Слушатели</option>
</select>
<table class="users" border="0" cellspacing="2" cellpadding="0">
<tr class="odd">
	<th class="id" onclick = 'filterSelectOnchange("id");'><?php  showSortArrow("id", $sortField, $sortDirection); ?>ID</th>
	<th class="login" onclick = 'filterSelectOnchange("login");'><?php  showSortArrow("login", $sortField, $sortDirection); ?>Логин</th>
	<th class="fio" onclick = 'filterSelectOnchange("fio");'><?php  showSortArrow("fio", $sortField, $sortDirection); ?>ФИО</th>
	<th class="role" onclick = 'filterSelectOnchange("role");'><?php  showSortArrow("role", $sortField, $sortDirection); ?>Роль в системе</th>
	<th class="date_reg" onclick = 'filterSelectOnchange("date_reg");'><?php  showSortArrow("date_reg", $sortField, $sortDirection); ?>Дата регистрации</th>
	<th> </th>
</tr>
<?php foreach ($users as $i => $user): ?>
	<tr<?php if ($i % 2): ?> class="odd"<?php else: ?> class="even"<?php endif; ?>>
		<td class="id"><?php echo $user['user_id']; ?></td>
		<td class="login"><?php echo $user['login']; ?></td>
		<td class="fio"><?php echo $user['surname'] . ' ' . $user['name'] . ' ' . $user['patronymic']; ?></td>
		<td class="role"><?php echo $rolesCaptions[$user['role']]; ?></td>
		<td class="date_reg"><?php echo $user['date_reg']; ?></td>
		<td class="edit"><a href="<?php echo $this->_links->get('users.edit', array('user_id' => $user['user_id'])) ?>">редактировать</a></td>
	</tr>
<?php endforeach; ?>
</table>
