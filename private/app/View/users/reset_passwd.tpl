<?php $this->title = 'Смена пароля' ?>
<?php $form = $this->form ?>
<h3>Смена пароля</h3>
<br />

<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
<div class="form">
    <?php $field = $form->new_passwd ?>
    <div class="field">
      <label for="new_passwd">Новый пароль:</label>
      <input name="<?php echo $field->name ?>" type="password" id="new_passwd" value="<?php echo $field->value ?>" />
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>
    <br/>
    
    <?php $field = $form->passwd_check ?>
    <div class="field">
      <label for="passwd_check">Повторите пароль:</label>
      <input name="<?php echo $field->name ?>" type="password" id="passwd_check" value="<?php echo $field->value ?>" />
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>
    <br />
    
    <div class="field">
        <input type="submit" value="Сменить пароль" />
    </div>
</div>
</form>
