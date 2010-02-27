<?php
    $form = $this->form;
    $materialTypes = Model_Educational_Materials::$MATERIAL_TYPES_CAPTIONS;
?>
<h3>Редактирование данных учебного материала материала</h3>
<form aciton="<?php echo $form->action(); ?>" method="<?php echo $form->method(); ?>">
    <div class="field">
      <label for="<?php echo $form->description->name ?>">Название материала:</label>
      <input name="<?php echo $form->description->name ?>" id="<?php echo $form->description->name ?>" type="text" value="<?php echo $form->description->value ?>" />
    </div>
    
    <?php if (isset($form->description->error)): ?>
    <div class="error"><?php echo $form->description->error ?></div>
    <?php endif; ?>
    
    <div class="field">
      <label for="<?php echo $form->type->name ?>">Тип материала:</label>
      <select name="<?php echo $form->type->name ?>" id="<?php echo $form->type->name ?>">
<?php foreach ($materialTypes as $value => $caption): ?>
<option value="<?php echo $value; ?>"<?php if ($value == $form->type->value): ?>selected="selected"<?php endif; ?>><?php echo $caption; ?></option>
<?php endforeach; ?>
      </select>
    </div>
    
    <div class="field">
        <input type="submit" value="Сохранить" />
    </div>    
</form>