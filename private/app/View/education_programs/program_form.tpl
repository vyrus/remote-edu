<?php 
	$form = $this->form;
	if (isset ($form->speciality)) {
		$speciality = $form->speciality;
	}
//	print_r ($form);
	
	$__CAPTIONS = array (
		'direction' => array (
			'type'	=> 'направления',
			'free'	=> 'бесплатное',
			'paid'	=> 'платное',
		),
		
		'course'	=> array (
			'type'	=> 'курсов',
			'free'	=> 'бесплатные',
			'paid'	=> 'платные',
		),
	);
?>

<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
<div class="form">
    <?php if (isset($speciality->error)): ?>
    <div class="error"><?php echo $speciality->error ?></div>
    <?php endif; ?>	
	
    <div class="field">
      <label for="title">Название <?php echo $__CAPTIONS[$this->programType]['type']; ?>:</label>
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
      <label for="paidType">Тип <?php echo $__CAPTIONS[$this->programType]['type']; ?>:</label>
      <select name="<?php echo $form->paidType->name ?>" id="paidType" onchange="if (this.value == 'paid') {document.getElementById ('costField').style.display = ''; document.getElementById ('costFieldError').style.display = '';} else {document.getElementById ('costField').style.display = 'none'; document.getElementById ('costFieldError').style.display = 'none';}"><option value="free"><?php echo $__CAPTIONS[$this->programType]['free']; ?></option><option value="paid"<?php if ($form->paidType->value == 'paid'): ?> selected="selected"<?php endif; ?>><?php echo $__CAPTIONS[$this->programType]['paid']; ?></option></select>
    </div>
		
    <div id="costField" class="field"<?php if ($form->paidType->value != 'paid'): ?> style="display:none;"<?php endif; ?>>
      <label for="cost">Стоимость <?php echo $__CAPTIONS[$this->programType]['type']; ?>:</label>
      <input name="<?php echo $form->cost->name ?>" type="text" id="cost" value="<?php echo $form->cost->value ?>" />
    </div>

    <?php if (isset($form->cost->error)): ?>
    <div id="costFieldError" class="error"><?php echo $form->cost->error ?></div>
    <?php endif; ?>

    <div class="field">
        <input type="submit" value="<?php echo $this->buttonCaption; ?>" />
    </div>
</div>
</form>