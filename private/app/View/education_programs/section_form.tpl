<?php
    $form = $this->form;

    if (isset($form->discipline)) {
        $discipline = $form->discipline;
    }

    if (isset($form->section)) {
        $section = $form->section;
    }
?>

<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
    <div class="form">
        <?php if (isset($discipline->error)): ?>
        <div class="error"><?php echo $discipline->error ?></div>
        <?php endif; ?>

        <?php if (isset($section->error)): ?>
        <div class="error"><?php echo $section->error ?></div>
        <?php endif; ?>

        <div class="field">
            <label for="title">Название раздела:</label>
            <input name="<?php echo $form->title->name ?>" type="text" id="title" value="<?php echo $form->title->value ?>" />
        </div>

        <?php if (isset($form->title->error)): ?>
        <div class="error"><?php echo $form->title->error ?></div>
        <?php endif; ?>

        <div class="field">
            <label for="number">Номер раздела:</label>
            <input name="<?php echo $form->number->name ?>" type="text" id="number" value="<?php echo $form->number->value ?>" />
        </div>

        <?php if (isset($form->number->error)): ?>
        <div class="error"><?php echo $form->number->error ?></div>
        <?php endif; ?>

        <div class="field">
            <input type="submit" value="Сохранить" />
        </div>
    </div>
</form>

<?php
    if (isset($this->form_checkpoint)) {
        $form_checkpoint = $this->form_checkpoint;
?>
<h3>Контрольная точка раздела</h3>
<?php
        $this->renderElement('checkpoint-form');
    }
?>