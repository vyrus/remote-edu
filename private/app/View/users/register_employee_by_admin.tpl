<?php $this->title = 'Регистрация сотрудника' ?>
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
    
    <?php /* Роль */ $field = $form->role ?>
    <div class="field">
      <label for="role">Роль:</label>
      <select name="<?php echo $field->name ?>" id="role">
        <option value="teacher"<?php echo 'teacher' == $field->value ? ' selected' : '' ?>>Преподаватель</option>
        <option value="admin"<?php   echo 'admin'   == $field->value ? ' selected' : '' ?>>Администратор</option>
      </select>
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>
    
    
    <?php /* Email */ $field = $form->email ?>
    <div class="field">
      <label for="email">Email:</label>
      <input name="<?php echo $field->name ?>" type="text" id="email" value="<?php echo $field->value ?>" />
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>
    
    
    <?php /* Фамилия */ $field = $form->surname ?>
    <div class="field">
      <label for="surname">Фамилия:</label>
      <input name="<?php echo $field->name ?>" type="text" id="surname" value="<?php echo $field->value ?>" />
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>

    
    <?php /* Имя */ $field = $form->name ?>
    <div class="field">
      <label for="name">Имя:</label>
      <input name="<?php echo $field->name ?>" type="text" id="name" value="<?php echo $field->value ?>" />
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>

    
    <?php /* Отчество */ $field = $form->patronymic ?>
    <div class="field">
      <label for="patronymic">Отчество:</label>
      <input name="<?php echo $field->name ?>" type="text" id="patronymic" value="<?php echo $field->value ?>" />
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>

    <div class="field">
        <input type="submit" value="Зарегистрировать" />
    </div>
</div>
</form>