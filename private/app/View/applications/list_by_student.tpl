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
<h3>Статусы заявок пользователя</h3><br />
<table class="materials" border="0" cellspacing="2" cellpadding="0">
	<tr class="odd">
	<th class="description">Заявка</th>
	<th class="description">Статус</th>
	<th class="description">Договор</th>	
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
				<td><?=$this->statuses[$app['status']]?></td>
				<td><?
					if ($app['status'] == 'accepted')
					{
						if (empty($app['contract_filename']))
						{
							echo"идёт формирование договора";
						}else
						{ ?>
							<a href="<?=$app['contract_filename'].'.doc'?>">скачать договор</a></td> <?
						}
					} ?>
			</tr> <?
		}
	} ?>
</table>
