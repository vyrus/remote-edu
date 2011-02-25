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
Здесь можно увидеть статусы Ваших заявок на обучение:
<ol>
	<li><b>подана</b> - означает что Ваша заявка находится на рассмотрении наших преподавателей;</li>
	<li><b>отклонена</b> - Ваша заявка на обучение по данной учебной программе не может быть удовлетворена, возможно Вам стоит пройти демонстрационный курс, или пройти обучение по программе предваряющей выбранную Вами;</li>
	<li><b>подписана</b> - в разделе <a href="<?php echo $this->_links->get('student.programs') ?>">Мои курсы</a> Вам доступны материалы для обучения по данной программе.</li>
</ol>
<p>
Также, ещё возможен вариант когда Вам потребуется заключить договор на обучение, в этом случае ссылка на него появится в колонке "Договор". <a href="<?php echo $this->_links->get('help.how-to-start') ?>">Перейти к инструкции</a>.
<p>
<table class="materials" border="0" cellspacing="2" cellpadding="0">
	<tr class="odd">
	<th class="description">Заявка</th>
	<th class="description">Статус</th>
	<th class="description">Договор</th>
	</tr> <?php
	if (! empty ($this->applications))
	{
		foreach ($this->applications as $i => $app)
		{ ?>
			<tr<?php if ($i % 2) {?> class="odd" <?php } else { ?> class="even"<?php } ?>> <?php
				if ($app['program_title'])
				{ ?>
					<td class="description">Заявка на изучение направления "<?php echo $app['program_title']; ?>"</td> <?php
				}elseif ($app['discipline_title'])
				{ ?>
					<td class="description">Заявка на изучение дисциплины "<?php echo $app['discipline_title']; ?>"</td> <?php
				} ?>
				<td><?php echo $this->statuses[$app['status']]; ?></td>
				<td><?php
					if ($app['status'] == 'accepted')
					{
						if (empty($app['contract_filename']))
						{
							echo"идёт формирование договора";
						}else
						{ $test = $this->_links->get('applications.download_contract',array('file_name'=>$app['contract_filename'])); ?>
							<a href="<?php echo $test; ?>">скачать договор</a></td> <?php
						}
					}elseif ($app['status'] == 'signed')
					{
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
								echo " рублей,<br> что составляет ".$app['rest_rate']." % от общей суммы";
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
								echo " рублей,<br> что составляет ".$app['rest_rate']." % от общей суммы";
							}
						}
					} ?>
			</tr> <?php
		}
	} ?>
</table>