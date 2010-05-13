<?php $this->title = 'Восстановление пароля' ?>
<?php $form = $this->form ?>
<h3>Восстановление пароля</h3>

<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
<div class="form">
    <p>
    Заполните форму, указав свой логин, после чего ждите письмо на тот email, 
    который Вы использовали при регистрации. 
    </p>
    <br />
    
    <div class="field">
      <label for="login">Логин:</label>
      <input name="<?php echo $form->login->name ?>" type="text" id="login" value="<?php echo $form->login->value ?>" />
    </div>
    
    <?php if (isset($form->login->error)): ?>
    <div class="error"><?php echo $form->login->error ?></div>
    <?php endif; ?>
    
    <br />
    <div class="field">
        <input type="submit" value="Восстановить" />
    </div>
</div>
</form>
