<?php
	$invalidMaterialsForms = $this->invalidMaterialsForms;
?>
<style type="text/css">
	tr.odd, th.odd {
		background-color: #ECECEC;
	}
	
	tr.even, th.even {
		background-color: #FFFFFF;
	}
	
	th {
		font-weight 	: bold;
	}
	
	td.description, th.description {
		width 			: 300px;
	}

	td.caption {
		width 					: 50%;
		padding-right 			: 10px;
		text-align				: right;
	}
	
	td.field {
		width 					: 50%;
		padding-left 			: 10px;
		text-align				: left;
	}
	
	td.cancel {
		width 					: 100%;
		text-align				: right;
	}
</style>

<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.7.2.custom.min.js"></script>

<?php $this->title = 'Заявки' ?>
<h3>Статусы заявок слушателей</h3><br />
<table class="materials" border="0" cellspacing="2" cellpadding="0">
	<tr class="odd">
	<th class="description">Заявка</th>
	<th class="description">Слушатель</th>		
	<th class="description">Статус</th>	
	</tr> <?
	if (! empty ($this->applications))
	{
		foreach ($this->applications as $i => $app)
		{ ?>
			<tr<? if ($i % 2) {?> class="odd" <?} else {?> class="even"<?}?>> <?
				if ($app['program_title'])
				{ ?>
					<td class="description">Заявка на изучение программы "<?=$app['program_title']?>"</td> <?
				}elseif ($app['discipline_title'])
				{ ?>
					<td class="description">Заявка на изучение дисциплины "<?=$app['discipline_title']?>"</td> <?
				} ?>
				<td><?=$app['name'].' '.$app['surname'].' '.$app['patronymic']?></td>
				<td width='10%'><?=$this->statuses[$app['status']]?></td>
				<td> <?
					if ($app['status'] == 'applied')
					{ ?>
						<button class="addButton" name='accept' onclick="changeStatus ('accepted',<?=$app['app_id']?>);">принять</button>						
						<button class="addButton" name='decline' onclick="changeStatus ('declined',<?=$app['app_id']?>);">отклонить</button> <?
					}elseif ($app['status'] == 'declined')
					{ ?>
						<button class="addButton" name='delApp' onclick="changeStatus ('deleted',<?=$app['app_id']?>);">удалить</button>	<?
					}elseif ($app['status'] == 'accepted')
					{
						if (empty($app['contract_filename']))
						{ ?>
						<form id="educationalMaterials<?=$app['app_id']?>"
							  name="educationalMaterials<?=$app['app_id']?>"
							  method="post" action="/applications/index_by_admin" enctype="multipart/form-data">
							<div class="educationalMaterial" id="edMatContainer">
							Договор&nbsp;&nbsp;
							<input type="file" name="fileReference<?=$app['app_id']?>">
							<input type="button" value="загрузить" onclick="document.educationalMaterials<?=$app['app_id']?>.submit()">
							<div id="errorContainer"></div>
							</div>
						</form> <?
						}else
						{ ?>
						Договор загружен
						<button class="addButton" name='signedApp' onclick="changeStatus ('signed',<?=$app['app_id']?>);">подписана</button>	<?
						}
					}elseif ($app['status'] == 'signed')
					{ ?>
						<button class="addButton" name='paidApp' onclick="changeStatus ('paid',<?=$app['app_id']?>);">оплачена</button>	<?
					}elseif ($app['status'] == 'paid')
					{ ?>
						<button class="addButton" name='delApp' onclick="changeStatus ('deleted',<?=$app['app_id']?>);">удалить</button>	<?
					}
					?>
				</td>
			</tr> <?
		}
	} ?>
</table>
<script type="text/javascript">
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
		
		$ ('div'			, errorMessage).addClass ('error');
		$ ('td:first-child'	, errorMessage).addClass ('caption');
		$ ('td:last-child'	, errorMessage).addClass ('field');
		
		return errorMessage;
	}	
	
	function changeStatus(newStatus,appId)
	{
		window.location = '/applications/change_app_status/' + newStatus + '/' + appId;
	}

<?php if (empty ($invalidMaterialsForms)): ?>
		var filenameRow		= $ ('#errorContainer');
<?php else: ?>
		var filenameRow		= $ ('#errorContainer');
		if (filenameErrorText !== null) {
			$ (filenameRow).after (this.createErrorMessage (filenameErrorText))

$filename		= invalidMaterialsForms[0]->filename;
?>
filenameRow.innerHtML = <?php echo ((isset ($filename->error)) ? ("'" . $filename->error . "'") : ('null')); 
	  endif; ?>
</script>	