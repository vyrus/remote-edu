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
        '<?php echo $i; ?>':{'name':'<?php echo $recipient['recipient_name']; ?>','desc':"<?php foreach ($recipient['recipient_description'] as $i => $desc) {echo $desc . '<br />';} ?>"},
<?php endforeach; ?>        
    };
    
    function showRecipientDescription() {
        var recId = recipientsSelect.val();
        
        if (recId) {
            $('#recipientDescription').html(recipients[recId].desc);
            recipientDescription.dialog('open');
        }
    }
    
    function hideRecipientDescription() {
        recipientDescription.dialog('close');
    }
    
    function fillRecipientsSelect() {
		$.each(recipients, function (key, value) {var option = new Option(value.name, key); recipientsSelect.append(option);});				    
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
    
    function selectRecipient() {
        recipientId.val(recipientsSelect.val());
        selectRecipientButton.text(recipients[recipientsSelect.val()].name);
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
<div id="selectRecipientDialog"><select id="recipientsSelect" size="10" style="width: 100%;"></select></div>
<div id="recipientDescription"></div>

<script type="text/javascript">
    var recipientId = $("#sendMessageForm > :input[name='recipient']");
    var recipientsSelect = $('#recipientsSelect');
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