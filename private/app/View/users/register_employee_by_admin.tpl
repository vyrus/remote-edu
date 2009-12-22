<?php $this->title = 'Регистрация сотрудника' ?>
<?php $form = $this->form ?>

<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
<div class="form">
Регистрация в системе дистанционного обучения Орловского регионального центра Интернет-образования:<br>
    <?php /* Логин */ $field = $form->login ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" type="text" id="login" value="<?php echo $field->value ?>" />
      <label for="login">Имя пользователя:</label>
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>
    
    <?php /* Роль */ $field = $form->role ?>
    <br>
    <div class="field">
      <select name="<?php echo $field->name ?>" id="role">
        <option value="teacher"<?php echo 'teacher' == $field->value ? ' selected' : '' ?>>Преподаватель</option>
        <option value="admin"<?php   echo 'admin'   == $field->value ? ' selected' : '' ?>>Администратор</option>
      </select>
      <label for="role">Роль:</label>
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>
    
    
    <?php /* Email */ $field = $form->email ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" type="text" id="email" value="<?php echo $field->value ?>" />
      <label for="email">Email:</label>
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>
    
    
    <?php /* Фамилия */ $field = $form->surname ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" type="text" id="surname" value="<?php echo $field->value ?>" />
      <label for="surname">Фамилия:</label>
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>

    
    <?php /* Имя */ $field = $form->name ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" type="text" id="name" value="<?php echo $field->value ?>" />
      <label for="name">Имя:</label>
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>

    
    <?php /* Отчество */ $field = $form->patronymic ?>
    <br>
    <div class="field">
      <input name="<?php echo $field->name ?>" type="text" id="patronymic" value="<?php echo $field->value ?>" />
      <label for="patronymic">Отчество:</label>
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>

    <br>
    <div class="field">
        <input type="submit" value="Зарегистрировать" />
    </div>
</div>
</form>