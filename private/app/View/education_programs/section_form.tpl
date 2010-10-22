<?php
    $form = $this->form;
    $form_checkpoint = $this->form_checkpoint;

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
<h3>Контрольная точка раздела</h3>
<form action="<?php echo $form_checkpoint->action() ?>" method="<?php echo $form_checkpoint->method() ?>">
    <div class="form">
        <input name="checkpoint_object_id" type="hidden" id="checkpoint_object_id" value="<?php echo $this->checkpoint_object_id; ?>" />
        <input name="checkpoint_object_type" type="hidden" id="checkpoint_object_type" value="<?php echo $this->checkpoint_object_type; ?>" />
        <div class="field">
            <label for="checkpoint_title">Заголовок:</label>
            <input name="<?php echo $form_checkpoint->title->name; ?>" type="text" id="checkpoint_title" value="<?php echo $form_checkpoint->title->value; ?>" />
        </div>
        <?php if (isset($form_checkpoint->title->error)): ?>
        <div class="error"><?php echo $form_checkpoint->title->error; ?></div>
        <?php endif; ?>

        <div class="field">
            <label for="checkpoint_text">Текст:</label>
            <textarea name="<?php echo $form_checkpoint->text->name; ?>" id="checkpoint_text"><?php echo $form_checkpoint->text->value; ?></textarea>
        </div>
        <?php if (isset($form_checkpoint->text->error)): ?>
        <div class="error"><?php echo $form_checkpoint->text->error; ?></div>
        <?php endif; ?>

        <div class="field">
            <label for="checkpoint_type">Тип:</label>
            <select name="<?php echo $form_checkpoint->type->name; ?>" id="checkpoint_type">
            <?php
                $values = array(
                    'lab' => 'Лабораторная работа',
                    'control' => 'Контрольная работа',
                    'test' => 'Он-лайн тест'
                );
                foreach ($values as $value => $title):
            ?>
                <option value="<?php echo $value ?>"<?php echo ($value == $form_checkpoint->type->value ? ' selected' : '') ?>>
                    <?php echo $title ?>
                </option>
            <?php endforeach ?>
            </select>
        </div>
        <?php if (isset($form_checkpoint->type->error)): ?>
        <div class="error"><?php echo $form_checkpoint->type->error; ?></div>
        <?php endif; ?>

        <div class="field">
            <input type="submit" value="Сохранить" />
        </div>
    </div>
</form>