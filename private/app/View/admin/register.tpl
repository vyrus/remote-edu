<?php $this->title = 'Регистрация нового пользователя' ?>
<h3>Регистрация нового пользователя</h3> 
<?php $form = $this->form ?>

<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
<div class="form">
    
    
    <div class="field">
      <label for="login">Имя пользователя (логин):</label>
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
      <label for="email">e-mail:</label>
      <input name="<?php echo $form->email->name ?>" type="text" id="email" value="<?php echo $form->email->value ?>" />
    </div>
    
    <?php if (isset($form->email->error)): ?>
    <div class="error"><?php echo $form->email->error ?></div>
    <?php endif; ?>

    <div class="field">
        <input type="submit" value="Зарегистрировать" />
    </div>
</div>
</form>