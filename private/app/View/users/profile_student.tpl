<?php $this->title = 'Профиль слушателя' ?>
<?php $form = $this->form ?>

<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
<div class="form">
    <?php /* Новый пароль */ $field = $form->new_passwd ?>
    <div class="field">
      <label for="new_passwd">Новый пароль:</label>
      <input name="<?php echo $field->name ?>" type="password" id="new_passwd" value="<?php echo $field->value ?>" /> 
    </div>
    
        <?php if (isset($field->error)): ?>
        <div class="error"><?php echo $field->error ?></div>
        <?php endif; ?>
    
    <?php /* Проверка нового пароля */ $field = $form->passwd_check ?>
    <div class="field">
      <label for="passwd_check">Проверка пароля:</label>
      <input name="<?php echo $field->name ?>" type="password" id="passwd_check" value="<?php echo $field->value ?>" /> 
    </div>
    
    <?php /* Email */ $field = $form->email ?>
    <div class="field">
      <label for="email">Email:</label>
      <input name="<?php echo $field->name ?>" type="text" id="email" value="<?php echo $field->value ?>" />
    </div>
    
        <?php if (isset($field->error)): ?>
        <div class="error"><?php echo $field->error ?></div>
        <?php endif; ?>
    
    <?php /* Старый пароля */ $field = $form->old_passwd ?>
    <div class="field">
      <label for="old_passwd">Старый пароль:</label>
      <input name="<?php echo $field->name ?>" type="password" id="old_passwd" value="<?php echo $field->value ?>" /> 
    </div>
    
        <?php if (isset($field->error)): ?>
        <div class="error"><?php echo $field->error ?></div>
        <?php endif; ?>

    <div class="field">
        <input type="submit" value="Сохранить" />
    </div>
</div>
</form>