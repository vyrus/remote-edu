<?php $this->title = 'Вход' ?>
<?php $form = $this->form ?>

<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
<div class="form">
    <?php if (isset($this->error)): ?>
    <div class="error"><?php echo $this->error ?></div>
    <?php endif; ?>
    
    <div class="field">
      <label for="login">Имя пользователя:</label>
      <input name="<?php echo $form->login->name ?>" type="text" id="login" value="<?php echo $form->login->value ?>" />
    </div>
    
    <?php if (isset($form->login->error)): ?>
    <div class="error"><?php echo $form->login->error ?></div>
    <?php endif; ?>
    
    <div class="field">
      <label for="passwd">Пароль:</label>
      <input name="<?php echo $form->passwd->name ?>" type="text" id="passwd" value="<?php echo $form->passwd->value ?>" />
    </div>
    
    <?php if (isset($form->passwd->error)): ?>
    <div class="error"><?php echo $form->passwd->error ?></div>
    <?php endif; ?>

    <div class="field">
        <input type="submit" value="Войти" />
    </div>
</div>
</form>