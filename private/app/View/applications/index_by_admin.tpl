<?php
	$invalidMaterialsForms = $this->invalidMaterialsForms;

	$listNames = $this->listNames;
	$listPrograms = $this->listPrograms;
	$listDisciplines = $this->listDisciplines;

	$sortField = $this->sortField;
	$sortDirection = $this->sortDirection;

	$filterName = $this->filterName;
	$filterStatus = $this->filterStatus;
	$filterObjectType = $this->filterObjectType;
	$filterObjectId = $this->filterObjectId;

	$links = $this->links;
	
	//print_r($this->applications);

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
        font-weight     : bold;
    }

    td.description, th.description {
        width             : 300px;
    }

    td.caption {
        width                     : 50%;
        padding-right             : 10px;
        text-align                : right;
    }

    td.field {
        width                     : 50%;
        padding-left             : 10px;
        text-align                : left;
    }

    td.cancel {
        width                     : 100%;
        text-align                : right;
    }
</style>

<script type="text/javascript">

	var sort_direction_cur = "<?php echo $sortDirection;?>";
	var sort_field_cur = "<?php echo $sortField;?>";

	// номер последней позиции занимаемой дисцилиной/программой в выборбоксе, формируется в
	// fillListDiscAndPrograms
	/*
	var lastCommonDiscProgsPos = 0;
	var lastDisciplinePos = 0;
	var lastProgramPos = 0;
	*/

	var lastCommonDiscProgsPos = 0;
	var lastProgramPos = <?php echo count($listPrograms); ?> + lastCommonDiscProgsPos;
	var lastDisciplinePos = <?php echo count($listDisciplines); ?> + lastProgramPos; 

	function getDirectionReverse(direct) {
		if (direct == 'asc') return 'desc';
		if (direct == 'desc') return 'asc';
	}

	function sortSelectOnchange(field) {
		if (sort_field_cur == field) {
			sort_direction_cur = getDirectionReverse(sort_direction_cur);
		} else {
			sort_direction_cur = 'asc';	
		}
		//window.location = '<?php echo $links->get("admin.applications"); ?>' + field + '/' +	sort_direction_cur + '/';
		document.forms[0].sort_field.value = field;
		document.forms[0].sort_direction.value = sort_direction_cur;
		document.forms[0].submit();
	}

	function filterObjectSelectOnChange() {
		document.forms[0].filter_object_id.value = filterObject.options[filterObject.selectedIndex].value;
		if (filterObject.selectedIndex == lastCommonDiscProgsPos) {
			document.forms[0].filter_object_type.value = 'all';
		} else if 
			((filterObject.selectedIndex > lastCommonDiscProgsPos) &&
				(filterObject.selectedIndex <= lastProgramPos)) {
			document.forms[0].filter_object_type.value = 'program';
		} else if
			((filterObject.selectedIndex > lastProgramPos) &&
				(filterObject.selectedIndex <= lastDisciplinePos)) {
			document.forms[0].filter_object_type.value = 'discipline';
		}
	}
	
	function filterStatusSelectOnChange() {
		document.forms[0].filter_status.value = filterStatus.options[filterStatus.selectedIndex].value;
	}

	function filterNameSelectOnChange() {
		document.forms[0].filter_name.value = filterName.options[filterName.selectedIndex].value;
		
	}


	function initFilter() {
		document.forms[0].filter_name.value = "<?php echo $filterName; ?>";
		document.forms[0].filter_status.value = "<?php echo $filterStatus; ?>";
		document.forms[0].filter_object_type.value = "<?php echo $filterObjectType; ?>";
		document.forms[0].filter_object_id.value = "<?php echo $filterObjectId; ?>";

		for (var i = 0; i < filterName.length; i++) {
			if (filterName.options[i].value == "<?php echo $filterName; ?>") {
				filterName.selectedIndex = i;
				break;
			};
		}
		for (var i = 0; i < filterStatus.length; i++) {
			if (filterStatus.options[i].value == "<?php echo $filterStatus; ?>") {
				filterStatus.selectedIndex = i;
				break;
			};
		}
		if ("<?php echo $filterObjectType; ?>" == 'all') {
			filterObject.selectedIndex= 0;
		} else if ("<?php echo $filterObjectType; ?>" == 'program') {
			for (var i = lastCommonDiscProgsPos+1; i <= lastProgramPos; i++) {
				if (filterObject.options[i].value == "<?php echo $filterObjectId; ?>") {
					filterObject.selectedIndex = i;
					break;
				}
			}
		} else if ("<?php echo $filterObjectType; ?>" == 'discipline') {
			for (var i = lastProgramPos+1; i <= lastDisciplinePos; i++) {
				if (filterObject.options[i].value == "<?php echo $filterObjectId; ?>") {
					filterObject.selectedIndex = i;
					break;
				}
			}
		}
	}

</script>

<?php $this->title = 'Заявки' ?>
<h3>Статусы заявок слушателей</h3><br />

<form action="<?php echo $links->get('admin.applications'); ?>" name="filter" method="post">
<input name="sort_field" type="hidden" value="<?php echo $sortField; ?>">
<input name="sort_direction" type="hidden" value="<?php echo $sortDirection; ?>">
<input name="filter_name" type="hidden">
<input name="filter_status" type="hidden">
<input name="filter_object_id" type="hidden">
<input name="filter_object_type" type="hidden">
Предмет:
<select id="filter_object_select" onchange = 'filterObjectSelectOnChange()' style="width: 320px;">
	<option value="all">--Любое направление/дисциплина--</option>
	<?php foreach ($listPrograms as $korteg): ?>
		<option value="<?php echo $korteg['object_id']; ?>"><?php echo $korteg['title']; ?></option>
	<?php endforeach; ?>
	<?php foreach ($listDisciplines as $korteg): ?>
		<option value="<?php echo $korteg['object_id']; ?>"><?php echo $korteg['title']; ?></option>
	<?php endforeach; ?>
</select>
Пользователь:
<select id="filter_name_select" onchange = 'filterNameSelectOnChange()' style="width: 270px;">
	<option value="all">--Любой пользователь--</option>
	<?php foreach ($listNames as $korteg): ?>
		<?php if(empty($korteg['name']) && empty($korteg['surname']) && empty($korteg['patronymic'])): ?>
			<option value="<?php echo $korteg['user_id']; ?>">
			   <?php echo $korteg['login'] ?>
			</option>
		<?php else: ?>
			<option value="<?php echo $korteg['user_id']; ?>">
				<?php echo $korteg['surname'] . ' ' .	$korteg['name'] .	' ' . $korteg['patronymic'] ?>
			</option>
		<?php endif; ?>
	<?php endforeach; ?>
</select>
Заявки:
<select id="filter_status_select" size="1" onchange = 'filterStatusSelectOnChange()'>
	<option value="all">Все</option>
	<option value="work">Рабочие</option>
	<option value="applied">Поданые</option>
	<option value="accepted">Принятые</option>
	<option value="declined">Отклоненные</option>
	<option value="signed">Подписанные</option>
	<option value="prepaid">Оплаченные</option>
	<option value="finished">Законченные</option>
</select>
<button onclick="document.forms[0].submit()">фильтр</button>
</form>
<br>
<table class="materials" border="0" cellspacing="2" cellpadding="0">
    <tr class="odd">
    <th class="description">Заявка</th>
    <th class="description" onclick = 'sortSelectOnchange("fio");'><?php showSortArrow("fio", $sortField, $sortDirection); ?>Слушатель</th>
    <th class="description" onclick = 'sortSelectOnchange("status");'><?php showSortArrow("status", $sortField, $sortDirection); ?>Статус</th>
    <th class="description" onclick = 'sortSelectOnchange("date_app");'><?php showSortArrow("date_app", $sortField, $sortDirection); ?>Дата подачи</th>
    <th class="description">Действие</th>
    </tr>

    <?php foreach ($this->applications as $i => $app): ?>
    <?php $class = ($i % 2 ? "odd" : "even") ?>
    <tr class="<?php echo $class ?>">
        <?php if ($app['program_title']): ?>
            <td width='25%' class="description">Заявка на изучение направления "<?php echo $app['program_title'] ?>"</td>
        <?php elseif($app['discipline_title']): ?>
            <td  width='20% 'class="description">Заявка на изучение дисциплины "<?php echo $app['discipline_title'] ?>"</td>
        <?php endif; ?>

            <td width='20%'>
            <?php if(empty($app['name']) && empty($app['surname']) && empty($app['patronymic'])): ?>
               <?php echo $app['login'] ?>
            <?php else: ?>
                <?php echo $app['surname'] . ' ' . $app['name'] . ' ' . $app['patronymic'] ?>
            <?php endif; ?>
            <a href="<?php echo $this->_links->get('users.profile', array('user_id' => $app['user_id'])); ?>" title="Подробная анкета слушателя" target="_blank">&rarr;</a>
            </td>
            <td width='10%'><?php echo $this->statuses[$app['status']] ?></td>
			<td width='15%'>
				<?php echo $app['date_app']; ?>
			</td>
            <td width='30%'> <?php
            	switch ($app['status']):
                    case Model_Application::STATUS_APPLIED: ?>
                        <button class="addButton" name='accept' onclick="changeStatus('accepted', <?php echo $app['app_id'] ?>);">принять</button>
                        <button class="addButton" name='decline' onclick="changeStatus('declined', <?php echo $app['app_id'] ?>);">отклонить</button> <?php
                        break; ?> <?php

                    case Model_Application::STATUS_DECLINED: ?>
                        <button class="addButton" name='delApp' onclick="deleteApp(<?php echo $app['app_id'] ?>);">удалить</button> <?php
                        break; ?> <?php

                    case Model_Application::STATUS_ACCEPTED: ?>
                        <?php if (empty($app['contract_filename'])): ?>
                            <form id="educationalMaterials<?php echo $app['app_id'] ?>"
                                  name="educationalMaterials<?php echo $app['app_id'] ?>"
                                  method="post" action= "<?php
								  	echo $links->get('admin.applications');
									?>"
								  enctype="multipart/form-data">
                                <div class="educationalMaterial" id="edMatContainer">
                                Договор&nbsp;&nbsp;
                                <input type="file" name="fileReference<?php echo $app['app_id'] ?>">
								<input type="hidden" name="sort_field"  value="<?php echo $sortField; ?>">
								<input type="hidden" name="sort_direction" value="<?php echo $sortDirection; ?>">
								<input name="filter_name" type="hidden" value="<?php echo $filterName; ?>">
								<input name="filter_status" type="hidden" value="<?php echo $filterStatus; ?>">
								<input name="filter_object_id" type="hidden" value="<?php echo $filterObjectId; ?>">
								<input name="filter_object_type" type="hidden" value="<?php echo $filterObjectType; ?>">
                                <input type="button" value="загрузить" onclick="document.educationalMaterials<?php echo $app['app_id'] ?>.submit()">
                                <div id="errorContainer"></div>
                                </div>
                            </form>
                        <?php else: ?>
                            Договор загружен
                            <button class="addButton" name='signedApp' onclick="changeStatus('signed', <?php echo $app['app_id'] ?>);">договор подписан</button>
                        <?php endif; ?> <?php
                        break; ?> <?php

                    case Model_Application::STATUS_SIGNED:
						if ($app['program_title'])
						{
							if ($app['rest'] === 'free')
							{
								echo "бесплатное направление";
							}elseif ($app['rest'] <= 0)
							{
								echo "направление оплачено";
							}else
							{
								echo "задолженность по оплате ".$app['rest'];
								echo " рублей,<br> что составляет " . round($app['rest_rate'] * 100, 2) . "% от общей суммы"; ?>
		                        <button class="addButton" name='delApp' onclick="addPayment(<?php echo $app['app_id'] ?>);">добавить платёж</button>
		                        <button class="addButton" name='delApp' onclick="deleteApp(<?php echo $app['app_id'] ?>);">удалить</button> <?php
							}
						}else
						{
							if ($app['rest'] === 'free')
							{
								echo "бесплатная дисциплина";
							}elseif ($app['rest'] <= 0)
							{
								echo "дисциплина оплачена";
							}else
							{
								echo "задолженность по оплате ".$app['rest'];
								echo " рублей,<br> что составляет " . round($app['rest_rate'] * 100, 2) . "% от общей суммы"; ?>
		                        <button class="addButton" name='delApp' onclick="addPayment(<?php echo $app['app_id'] ?>);">добавить платёж</button>
		                        <button class="addButton" name='delApp' onclick="deleteApp(<?php echo $app['app_id'] ?>);">удалить</button> <?php
							}
						} 
                        break; ?> <br> <?php
					case Model_Application::STATUS_PREPAID:
						if ($app['program_title']) {
							echo "направление оплачено";
						} else {
							echo "дисциплина оплачена";
						};
						?>
                        <button class="addButton" name='endApp' onclick="changeStatus('finished',<?php echo $app['app_id'] ?>);">окончить заявку</button>
					 	<?php break; ?> <?
					case Model_Application::STATUS_FINISHED:
						echo "заявка окончена"; // или сертификат выдан
					?>
                <?php endswitch; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<script type="text/javascript">

	filterStatus = document.getElementById('filter_status_select');
	filterName = document.getElementById('filter_name_select');
	filterObject = document.getElementById('filter_object_select');

	initFilter();

    function EducationalMaterial ()
    {
        this.container = document.getElementById('edMatContainer');
        contract = this;
    }

    EducationalMaterial.prototype.createErrorMessage = function (errorText) {
        var errorMessage = document.createElement ('tr').appendChild (
            document.createElement ('td')
        ).parentNode.appendChild (
            document.createElement ('td').appendChild (
                document.createElement ('div').appendChild (
                    document.createTextNode (errorText)
                ).parentNode
            ).parentNode
        ).parentNode;

        $ ('div'            , errorMessage).addClass ('error');
        $ ('td:first-child'    , errorMessage).addClass ('caption');
        $ ('td:last-child'    , errorMessage).addClass ('field');

        return errorMessage;
    }

    function changeStatus(newStatus,appId)
    {
        window.location = '<?php echo $this->_links->get('applications.change-status') ?>' + newStatus + '/' + appId + '/';
    }

    function addPayment(appId) {
        window.location = '<?php echo $this->_links->get('payments.add') ?>' + appId + '/';
    }

    function deleteApp(appId) {
        var question = 'Вы действительно хотите удалить заявку?';

        if (confirm(question)) {
            window.location = '<?php echo $this->_links->get('applications.delete') ?>' + appId + '/';
        }
    }

<?php if (empty ($invalidMaterialsForms)): ?>
        var filenameRow        = $ ('#errorContainer');
<?php else: ?>
        var filenameRow        = $ ('#errorContainer');
        if (filenameErrorText !== null) {
            $ (filenameRow).after (this.createErrorMessage (filenameErrorText))

$filename        = invalidMaterialsForms[0]->filename;
?>
filenameRow.innerHTML = <?php echo ((isset ($filename->error)) ? ("'" . $filename->error . "'") : ('null'));
      endif; ?>
</script>
