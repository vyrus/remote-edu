<?php 
	$form = $this->form;
	if (isset ($form->speciality)) {
		$speciality = $form->speciality;
	}
?>

<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
<div class="form">
    <?php if (isset($speciality->error)): ?>
    <div class="error"><?php echo $speciality->error ?></div>
    <?php endif; ?>	
	
    <div class="field">
      <label for="title">Название <?php echo $this->programTypeCaption; ?>:</label>
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
        <input type="submit" value="<?php echo $this->buttonCaption; ?>" />
    </div>
</div>
</form>