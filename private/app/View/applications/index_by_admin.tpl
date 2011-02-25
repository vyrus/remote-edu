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

<?php $this->title = 'Заявки' ?>
<h3>Статусы заявок слушателей</h3><br />
<table class="materials" border="0" cellspacing="2" cellpadding="0">
    <tr class="odd">
    <th class="description">Заявка</th>
    <th class="description">Слушатель</th>
    <th class="description">Статус</th>
    <th class="description">Действие</th>
    </tr>

    <?php foreach ($this->applications as $i => $app): ?>
    <?php $class = ($i % 2 ? "odd" : "even") ?>
    <tr class="<?php echo $class ?>">
        <?php if ($app['program_title']): ?>
            <td class="description">Заявка на изучение направления "<?php echo $app['program_title'] ?>"</td>
        <?php elseif($app['discipline_title']): ?>
            <td class="description">Заявка на изучение дисциплины "<?php echo $app['discipline_title'] ?>"</td>
        <?php endif; ?>

            <td>
            <?php if(empty($app['name']) && empty($app['surname']) && empty($app['patronymic'])): ?>
               <?php echo $app['login'] ?>
            <?php else: ?>
                <?php echo $app['surname'] . ' ' . $app['name'] . ' ' . $app['patronymic'] ?>
            <?php endif; ?>
            <a href="<?php echo $this->_links->get('users.profile', array('user_id' => $app['user_id'])); ?>" title="Подробная анкета слушателя" target="_blank">&rarr;</a>
            </td>
            <td width='10%'><?php echo $this->statuses[$app['status']] ?></td>
            <td> <?php
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
                                  method="post" action="/applications/index_by_admin" enctype="multipart/form-data">
                                <div class="educationalMaterial" id="edMatContainer">
                                Договор&nbsp;&nbsp;
                                <input type="file" name="fileReference<?php echo $app['app_id'] ?>">
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
						} ?> <br>
                    <?php break; ?>
                <?php endswitch; ?>
            </td>
        </tr>
    <?php endforeach; ?>
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