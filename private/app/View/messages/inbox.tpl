<?php
    $inbox = $this->inbox;
    $messagesTotalNumber = $this->messagesTotalNumber;
    $page = $this->page;
?>

<style type="text/css">
    tr.odd {
        background-color: #ECECEC;
    }
    
    tr.even {
        background-color: #FFFFFF;
    }
    
    tr.unread {
        font-weight: bold;
    }
    
    td.subject, th.subject {
        width: 200px;
    }
    
    td.author, th.author {
        width: 150px;
        text-align: center;
    }
    
    td.time, th.time {
        width: 125px;
        text-align: center;
    }
</style>

<script type="text/javascript">
    function setAllCheckboxesStatus(form) {
    	$('#' + form + ' :checkbox').attr('checked', $("#" + form + " :checkbox[name='all']").attr('checked'));
    }
    
    function submitForm() {
        $('#removeMessagesForm').submit();
    }
</script>

<div>
<form id="removeMessagesForm" action="/messages/remove" method="post">
<table cellspacing="0" cellpadding="0">
<tr class="odd"><th><input name="all" type="checkbox" onclick="setAllCheckboxesStatus('removeMessagesForm')" /></th><th class="subject">Тема</th><th class="author">Автор</th><th class="time">Дата</th></tr>
<?php foreach ($inbox as $i => $message): ?>
<tr class="<?php echo ($i % 2 ? 'odd' : 'even') . ($message['read'] == 'unread' ? ' unread' : ''); ?>"><td class="checkbox"><input type="checkbox" name="messages[<?php echo $message['message_id']; ?>]" /></td><td class="subject"><a href="/messages/<?php echo $message['message_id']; ?>"><?php echo $message['subject']; ?></a></td><td class="author"><?php echo $message['author']; ?></td><td class="time"><?php echo date('d-m-Y H:i', $message['time']); ?></td></tr>
<?php endforeach; ?>
</table>
<a href="javascript:submitForm()">Удалить выделенные сообщения</a>
</form>
<?php
    $pagesNumber = floor($messagesTotalNumber / Model_Messages::INBOX_MESSAGES_ON_PAGE) + ($messagesTotalNumber % Model_Messages::INBOX_MESSAGES_ON_PAGE ? 1 : 0);
    
    for ($i = 0; $i < $pagesNumber; $i++) {
        if ($i == $page) {
            echo '[' . ($i + 1) . ']&nbsp;';            
        }
        else {
            echo '<a href="/messages/inbox/' . $i . '">' . ($i + 1) . '</a>&nbsp;';
        }
    }
?>
</div>