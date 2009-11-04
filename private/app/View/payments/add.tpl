<?php $this->title = 'Новый платёж' ?>
<?php $form = $this->form ?>

<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
<div class="form">
    <?php /* Размер платежа */ $field = $form->amount ?>
    <div class="field">
      <label for="amount">Размер платежа:</label>
      <input name="<?php echo $field->name ?>" type="text" id="amount" value="<?php echo $field->value ?>" />
    </div>
    
    <?php if (isset($field->error)): ?>
    <div class="error"><?php echo $field->error ?></div>
    <?php endif; ?>

    <div class="field">
        <input type="submit" value="Добавить" />
    </div>
</div>
</form>