<?php
    $message = $this->message;
?>

<style type="text/css">
    td.caption {
        text-align: right;
    }

    td.field {
        max-width: 400px;
    }

    td.cation, td.field {
        vertical-align: top;
    }
</style>

<h3>Просмотр сообщения</h3>
<table cellspacing="0" cellpadding="0">
<tr><td class="caption">От кого:</td><td class="field"><?php echo $message['author']; ?></td></tr>
<tr><td class="caption">Тема:</td><td class="field"><?php echo $message['subject']; ?></td></tr>
<tr><td class="caption">Дата:</td><td class="field"><?php echo date('d-m-Y H:i',$message['time']); ?></td></tr>
<tr><td class="caption" valign="top">Сообщение:</td><td class="field" valign="top"><?php echo nl2br($message['message']); ?></td></tr>
</table>
<a href="<?php echo $this->_links->get('messages.send', array('to_id' => $message['from'])) ?>">ответить</a>&nbsp;<a href="<?php echo $this->_links->get('messages.inbox') ?>">вернуться к входящим</a>