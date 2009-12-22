<?php $this->title = 'Регистрация' ?>
<?php $form = $this->form ?>

<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
<div class="form">
    
    
    <div class="field">
      <input name="<?php echo $form->login->name ?>" type="text" id="login" value="<?php echo $form->login->value ?>" />
      <label for="login">Имя пользователя:</label>
    </div>
    
    <?php if (isset($form->login->error)): ?>
    <div class="error"><?php echo $form->login->error ?></div>
    <?php endif; ?>
    
    <br>
    <div class="field">
      <input name="<?php echo $form->passwd->name ?>" type="text" id="passwd" value="<?php echo $form->passwd->value ?>" />
      <label for="passwd">Пароль:</label>
    </div>
    
    <?php if (isset($form->passwd->error)): ?>
    <div class="error"><?php echo $form->passwd->error ?></div>
    <?php endif; ?>
    
    <br>
    <div class="field">
      <input name="<?php echo $form->fio->name ?>" type="text" id="fio" value="<?php echo $form->fio->value ?>" />
      <label for="fio">Ф.И.О.:</label>
    </div>
    
    <?php if (isset($form->fio->error)): ?>
    <div class="error"><?php echo $form->fio->error ?></div>
    <?php endif; ?>

    <br>
    <div class="field">
        <input type="submit" value="Зарегистрироваться" />
    </div>
</div>
</form>