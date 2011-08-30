<?php
    $recipients = $this->recipients;
    //print_r($recipients);
    $form = $this->form;
    $filterExists = $this->filterExists;
    //$filterExists = false;
?>

<style type="text/css">
    td.caption {
        text-align: right;
    }

    td.cation, td.field {
        vertical-align: top;
    }
</style>

<script type="text/javascript">
    var attachmentId = 0;
    //var recipientsCount = <?php echo count($this->recipients); ?>;
    var recipients = {
    <?php foreach ($recipients as $i => $recipient): ?>
        '<?php echo $i; ?>':{
            'name':'<?php if (array_key_exists('recipient_description', $recipient) && array_key_exists(0, $recipient['recipient_description']) && $recipient['recipient_description'][0] == 'Куратор') echo 'Куратор: '; echo $recipient['recipient_name']; ?>',
            'desc':"<?php 
                if ((array_key_exists('curator',$recipient)) && array_key_exists('role',$recipient) && $recipient['role'] == 'student' &&
                    array_key_exists($recipient['curator'], $recipients)) 
                        echo 'Куратор: ' . $recipients[$recipient['curator']]['recipient_name'];
                else if (array_key_exists('recipient_description',$recipient))
                    array_walk($recipient['recipient_description'], function ($x) { echo $x . '<br>'; });
                ?>",
            'role' :"<?php if ($filterExists) echo $recipient['role']; ?>"
        },
    <?php endforeach; ?>
    };
    
    function showRecipientDescription() {
        var recId = recipientsSelect.val();

        if (recId) {
            //$('recipientDescription').html(recipients[recId].desc);
            recipientDescription.html(recipients[recId].desc);
            recipientDescription.dialog('open');
        }
    }

    function hideRecipientDescription() {
        recipientDescription.dialog('close');
    }
    
    function clearRecipientSelect() {
        while (recipientsSelect[0].firstChild) {
            recipientsSelect[0].removeChild (recipientsSelect[0].firstChild);
        }
    }

    function fillRecipientsSelect() {
        if (<?php if ($filterExists) echo 'true'; else echo 'false'; ?>) {
            clearRecipientSelect();
            var recipientsFiltered = {};
            var b = false;
            //var b = true;
            for (var key in recipients) {
                var val = recipients[key];
                switch (recipientsType[0].selectedIndex) {
                    case 0:
                        b = true;
                        break;
                    case 1:
                        b = (val.role == 'teacher');
                        break;
                    case 2:
                        b = (val.role == 'student');
                        break;
                }
                if (b) {
                    recipientsFiltered[key] = val;
                }
            }
            $.each(recipientsFiltered, function (key, value) {var option = new Option(value.name, key); recipientsSelect.append(option);});
        } else {
            $.each(recipients, function (key, value) {var option = new Option(value.name, key); recipientsSelect.append(option);});
        }
    }

    function showSelectRecipientDialog() {
        selectRecipientDialog.dialog(
            'option',
            'buttons',
            {
                'Выбрать': selectRecipient,
                'Информация' : showRecipientDescription
            }
        );
        selectRecipientDialog.dialog('open');
    }

    /*
    function selectRecipient() {
        recipientId.val(recipientsSelect.val());
        selectRecipientButton.text(recipients[recipientsSelect.val()].name);
        selectRecipientDialog.dialog('close');
    }
    */
    
    function selectRecipient() {
        var str = '';
        var delimiter = '';
        selectRecipientButton.text('    ');
        for (var i = 0; i < recipientsSelect[0].length; i++) {
            if (recipientsSelect[0].options[i].selected) {
                str += delimiter;
                str += recipientsSelect[0].options[i].value;
                selectRecipientButton.text(selectRecipientButton[0].text + delimiter + recipientsSelect[0].options[i].text);    // бредовая строка, не правда ли? Такой вот он этот jQuery
                delimiter = ',';
            }
        }
        
        //alert (str);

        recipientId.val(str);
        selectRecipientDialog.dialog('close');
    }

    function initRecipientField() {
        var recId = recipientId.val();

        if (recId) {
            selectRecipientButton.text(recipients[recId].name);
        }
    }

    function submitForm() {
        if (!recipientId.val()) {
            alert('Не выбран адресат');
            return;
        }

        $('#sendMessageForm').submit();
    }
    
    function appendAttachment() {
        $('#appendAttachmentButton').before('<div id="attachment' + attachmentId + '"><input type="file" name="attachment[' + attachmentId + ']" /><a href="javascript:removeAttachment(' + attachmentId + ')">удалить</a></div>');
        attachmentId++;
    }
    
    function removeAttachment(attachmentId) {
        $('#attachment' + attachmentId).remove();
    }
</script>
<h3>Отправка сообщения</h3>
<form id="sendMessageForm" enctype="multipart/form-data" action="<?php echo $form->action(); ?>" method="<?php echo $form->method(); ?>">
<input name="<?php echo $form->recipient->name; ?>" type="hidden" value="<?php echo $form->recipient->value; ?>"/>
<table cellspacing="0" cellpadding="0">
<tr><td class="caption">Кому:</td><td class="field"><a id="selectRecipientButton" href="javascript:showSelectRecipientDialog()">Выбрать адресата</a></td></tr>
<tr><td class="caption">Тема:</td>
<td class="field"><input name="<?php echo $form->subject->name; ?>" type="text" value="<?php echo $form->subject->value; ?>" />
<?php if (isset($form->subject->error)): ?>
<div class="error"><?php echo $form->subject->error; ?></div>
<?php endif; ?>
</td></tr>
<tr><td class="caption" valign="top">Сообщение:</td>
<td class="field"><textarea name="<?php echo $form->message->name; ?>" cols="35" rows="7"><?php echo $form->message->value; ?></textarea>
<?php if (isset($form->message->error)): ?>
<div class="error"><?php echo $form->message->error; ?></div>
<?php endif; ?>
</td></tr>
<tr><td colspan="2"><a id="appendAttachmentButton" href="javascript:appendAttachment()">Добавить файл</a></td></tr>
<tr><td colspan="2"><input type="button" value="Отправить" onclick="submitForm()" /></td></tr>
</table>
</form>
<div id="selectRecipientDialog">
    <?php
    if ($filterExists)
    echo
    "<select id='recipientsType' size=1 onchange='fillRecipientsSelect()'>
        <option>Все</option>
        <option>Преподователи</option>
        <option>Студенты</option>
    </select>"
    ?>
    <select id="recipientsSelect" multiple="on" size="10" style="width: 100%;">
    </select>
</div>
<div id="recipientDescription"></div>

<script type="text/javascript">
    var recipientId = $("#sendMessageForm > :input[name='recipient']");
    var recipientsSelect = $('#recipientsSelect');
    var recipientsType = $('#recipientsType');
    var selectRecipientDialog = $('#selectRecipientDialog');
    var selectRecipientButton = $('#selectRecipientButton');
    var recipientDescription = $('#recipientDescription');

    

    recipientDescription.dialog(
        {
            autoOpen: false,
            draggable : false,
            modal: true,
            resizable: false,
            title: 'Информация о пользователе',
            width: 'auto',
        }
    );
    selectRecipientDialog.dialog(
        {
            autoOpen: false,
            draggable : false,
            modal : true,
            resizable: false,
            title: 'Выбор адресата',
            width: 'auto'
        }
    );
    fillRecipientsSelect();
    initRecipientField();
</script>
