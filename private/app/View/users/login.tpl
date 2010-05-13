<?php $this->title = 'Вход' ?>
<?php $form = $this->form ?>
<h3>Вход в систему дистанционного обучения</h3>
<br>
<br>
<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
<div class="form">
    <?php if (isset($this->error)): ?>
    <div class="error"><?php echo $this->error ?></div>
    <?php endif; ?>

    Укажите, пожалуйста, имя пользователя и пароль, или 
    <a href="<?php echo $this->_links->get('student.register') ?>" title="Регистрация">зарегистрируйтесь</a>.
    <a href="<?php echo $this->_links->get('users.restore-passwd') ?>">Забыли пароль?</a>
    <br /><br />
    
    <div class="field">
      <input name="<?php echo $form->login->name ?>" type="text" id="login" value="<?php echo $form->login->value ?>" />
      <label for="login">Имя пользователя:</label>
    </div>
    
    <?php if (isset($form->login->error)): ?>
    <div class="error"><?php echo $form->login->error ?></div>
    <?php endif; ?>

    <br>
    <div class="field">
      <input name="<?php echo $form->passwd->name ?>" type="password" id="passwd" value="<?php echo $form->passwd->value ?>" />
      <label for="passwd">Пароль:</label>
    </div>
    
    <?php if (isset($form->passwd->error)): ?>
    <div class="error"><?php echo $form->passwd->error ?></div>
    <?php endif; ?>
    
    <br>
    <div class="field">
        <input type="submit" value="Войти" />
    </div>
</div>
</form>
