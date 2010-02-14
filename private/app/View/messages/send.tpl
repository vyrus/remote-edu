<?php
    $recipients = $this->recipients;
    $form = $this->form;
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
    var recipients = {
<?php foreach ($recipients as $i => $recipient): ?>
        '<?php echo $recipient['recipient_id']; ?>':'<?php echo $recipient['recipient_name']; ?>',
<?php endforeach; ?>        
    };
    
    function fillRecipientsSelect() {
		$.each(recipients, function (key, value) {recipientsSelect.append(new Option(value, key));});				    
    }
    
    function showSelectRecipientDialog() {
		selectRecipientDialog.dialog(
			'option',
			'buttons',
			{
				'Выбрать': selectRecipient,
			}
		);
		selectRecipientDialog.dialog('open');        
    }
    
    function selectRecipient() {
        recipientId.val(recipientsSelect.val());
        selectRecipientButton.text(recipients[recipientsSelect.val()]);
        selectRecipientDialog.dialog('close');
    }
    
    function initRecipientField() {
        var recId = recipientId.val();
        
        if (recId) {
            selectRecipientButton.text(recipients[recId]);
        }
    }
    
    function submitForm() {
        if (!recipientId.val()) {
            alert('Не выбран адресат');
            return;
        }
        
        $('#sendMessageForm').submit();
    }
</script>

<h3>Отправка сообщения</h3>
<form id="sendMessageForm" action="<?php echo $form->action(); ?>" method="<?php echo $form->method(); ?>">
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
<tr><td colspan="2"><input type="button" value="Отправить" onclick="submitForm()" /></td></tr>
</table>
</form>
<div id="selectRecipientDialog"><select id="recipientsSelect" size="10"></select></div>

<script type="text/javascript">
    var recipientId = $("#sendMessageForm > :input[name='recipient']");
    var recipientsSelect = $('#recipientsSelect');
	var selectRecipientDialog = $('#selectRecipientDialog');
	var selectRecipientButton = $('#selectRecipientButton');
	
	selectRecipientDialog.dialog(
		{
			autoOpen: false,
			draggable : false,
			modal: true,
			resizable: false,
			title: 'Выбор адресата',
			width: 'auto'
		}
	);
	fillRecipientsSelect();
	initRecipientField();
</script>