<?php $form = $this->form ?>

<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
<div class="form">
    
    <div class="field">
      <label for="title">Название направления:</label>
      <input name="<?php echo $form->title->name ?>" type="text" id="title" value="<?php echo $form->title->value ?>" />
    </div>
    
    <?php if (isset($form->title->error)): ?>
    <div class="error"><?php echo $form->title->error ?></div>
    <?php endif; ?>
    
    <div class="field">
      <label for="labourIntensive">Общая трудоемкость:</label>
      <input name="<?php echo $form->labourIntensive->name ?>" type="text" id="labourIntensive" value="<?php echo $form->labourIntensive->value ?>" />
    </div>

    <?php if (isset($form->labourIntensive->error)): ?>
    <div class="error"><?php echo $form->labourIntensive->error ?></div>
    <?php endif; ?>
    
    <div class="field">
        <input type="submit" value="Добавить" />
    </div>
</div>
</form>