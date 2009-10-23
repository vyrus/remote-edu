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
						<input type='button' name='accept' value='принять'>
						<input type='button' name='accept' value='отклонить'>		<?
					}elseif ($app['status'] == 'declined')
					{ ?>
						<input type='button' name='accept' value='удалить'>		<?
					}elseif ($app['status'] == 'accepted')
					{ ?>
						<input type='button' name='accept' value='подписана'>		<?
					}elseif ($app['status'] == 'signed')
					{ ?>
						<input type='button' name='accept' value='оплачена'>		<?
					}elseif ($app['status'] == 'paid')
					{ ?>
						<input type='button' name='accept' value='удалить'>		<?
					}
					?>
				</td>
			</tr> <?
		}
	} ?>
</table>
