<?php $this->title = 'Регистрация слушателя' ?>
<?php $form = $this->form ?>

<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
<div class="form">
    <?php /* Логин */ $field = $form->login ?>
    <div class="field">
      <label for="login">Имя пользователя:</label>
      <input name="<?php echo $field->name ?>" type="text" id="login" value="<?php echo $field->value ?>" />
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>
    
    <?php /* Пароль */ $field = $form->passwd ?>
    <div class="field">
      <label for="passwd">Пароль:</label>
      <input name="<?php echo $field->name ?>" type="password" id="passwd" value="<?php echo $field->value ?>" /> 
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>
    
    <?php /* Проверка пароля */ $field = $form->passwd_check ?>
    <div class="field">
      <label for="passwd">Проверка пароля:</label>
      <input name="<?php echo $field->name ?>" type="password" id="passwd" value="<?php echo $field->value ?>" /> 
    </div>
    
    <?php /* Email */ $field = $form->email ?>
    <div class="field">
      <label for="email">Email:</label>
      <input name="<?php echo $field->name ?>" type="text" id="email" value="<?php echo $field->value ?>" />
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>

    <div class="field">
        <input type="submit" value="Зарегистрироваться" />
    </div>
</div>
</form>