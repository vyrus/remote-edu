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
</style>

<script type="text/javascript">
function changeStatus(newStatus,appId)
{
	window.location = '/applications/change_app_status/' + newStatus + '/' + appId;
}
</script>

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
					{ ?>
						<button class="addButton" name='signedApp' onclick="changeStatus ('signed',<?=$app['app_id']?>);">подписана</button>	<?
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
