<?php
    $form = $this->form;
    $rolesCaptions = $this->rolesCaptions; 
?>
<h3>Редактирование аккаунта пользователя</h3>
<form aciton="<?php echo $form->action(); ?>" method="<?php echo $form->method(); ?>">
    <div class="field">
      <label for="<?php echo $form->surname->name ?>">Фамилия:</label>
      <input name="<?php echo $form->surname->name ?>" id="<?php echo $form->surname->name ?>" type="text" value="<?php echo $form->surname->value ?>" />
    </div>
    
    <?php if (isset($form->surname->error)): ?>
    <div class="error"><?php echo $form->surname->error ?></div>
    <?php endif; ?>

    <div class="field">
      <label for="<?php echo $form->name->name ?>">Имя:</label>
      <input name="<?php echo $form->name->name ?>" id="<?php echo $form->name->name ?>" type="text" value="<?php echo $form->name->value ?>" />
    </div>
    
    <?php if (isset($form->name->error)): ?>
    <div class="error"><?php echo $form->name->error ?></div>
    <?php endif; ?>


    <div class="field">
      <label for="<?php echo $form->patronymic->name ?>">Отчество:</label>
      <input name="<?php echo $form->patronymic->name ?>" id="<?php echo $form->patronymic->name ?>" type="text" value="<?php echo $form->patronymic->value ?>" />
    </div>
    
    <?php if (isset($form->patronymic->error)): ?>
    <div class="error"><?php echo $form->patronymic->error ?></div>
    <?php endif; ?>
    
    <div class="field">
      <label for="<?php echo $form->role->name ?>">Роль в системе:</label>
      <select name="<?php echo $form->role->name ?>" id="<?php echo $form->role->name ?>">
<?php foreach ($rolesCaptions as $value => $caption): ?>
<option value="<?php echo $value; ?>"<?php if ($value == $form->role->value): ?>selected="selected"<?php endif; ?>><?php echo $caption; ?></option>
<?php endforeach; ?>
      </select>
    </div>
    
    <div class="field">
        <input type="submit" value="Сохранить" />
    </div>    
</form>