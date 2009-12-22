<?php $this->title = 'Вход' ?>
<?php $form = $this->form ?>

<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
<div class="form">
    <?php if (isset($this->error)): ?>
    <div class="error"><?php echo $this->error ?></div>
    <?php endif; ?>
    
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
<br>
Для того чтобы авторизоваться в системе, Вам необходимо пройти <a href="/users/register_student" title="Регистрация">регистрацию</a>