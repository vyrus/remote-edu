<?php $this->title = 'Регистрация слушателя' ?>
<?php $form = $this->form ?>

<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
<div class="form">
    Регистрация в системе дистанционного обучения Орловского регионального центра Интернет-образования:<p>

    <?php /* Логин */ $field = $form->login ?>
    <div class="field">
      <input name="<?php echo $field->name ?>" type="text" id="login" value="<?php echo $field->value ?>" />
      <label for="login">Имя пользователя:</label>
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>
    
    <?php /* Пароль */ $field = $form->passwd ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" type="password" id="passwd" value="<?php echo $field->value ?>" /> 
      <label for="passwd">Пароль:</label>
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>
    
    <?php /* Проверка пароля */ $field = $form->passwd_check ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" type="password" id="passwd" value="<?php echo $field->value ?>" /> 
      <label for="passwd">Проверка пароля:</label>
    </div>
    
    <?php /* Email */ $field = $form->email ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" type="text" id="email" value="<?php echo $field->value ?>" />
      <label for="email">Email:</label>
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>

    <br>
    <div class="field">
        <input type="submit" value="Зарегистрироваться" />
    </div>
</div>
</form>