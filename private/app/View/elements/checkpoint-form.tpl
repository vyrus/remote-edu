<?php $form_checkpoint = $this->form_checkpoint; ?>
<script>
    function updateCheckboxForm() {
        updateInputs();
        updateTestId();
    }

    function updateInputs() {
        state = ($('#active').attr('checked')) ? false : true;
        $('#checkpoint_title').attr('disabled', state);
        $('#checkpoint_text').attr('disabled', state);
        $('#checkpoint_type').attr('disabled', state);
        $('#checkpoint_test_id').attr('disabled', state);
    }

    function updateTestId() {
        ($('#checkpoint_type option:selected').attr('value') == 'test') ? $('#checkpoint_test_id_field').show() : $('#checkpoint_test_id_field').hide();
    }

    $(document).ready(function() {
        updateCheckboxForm();
        $('#active').change(function () {
            updateCheckboxForm();
        });
        $('#checkpoint_type').change(function () {
            updateTestId();
        });
    })
</script>
<form action="<?php echo $form_checkpoint->action() ?>" method="<?php echo $form_checkpoint->method() ?>">
    <div class="form">
        <input name="checkpoint_object_id" type="hidden" id="checkpoint_object_id" value="<?php echo $this->checkpoint_object_id; ?>" />
        <input name="checkpoint_object_type" type="hidden" id="checkpoint_object_type" value="<?php echo $this->checkpoint_object_type; ?>" />
        <div class="field">
            <label for="active">Активна</label>
            <input name="active" type="checkbox" id="active" value="1" <?php if ($form_checkpoint->active->value) echo 'checked '; ?>/>
        </div>
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

        <div class="field" id="checkpoint_test_id_field">
            <label for="checkpoint_test_id">Идентификатор теста:</label>
            <input id="checkpoint_test_id" name="<?php echo $form_checkpoint->test_id->name; ?>" type="text" value="<?php echo $form_checkpoint->test_id->value; ?>" />
            <?php if (isset($form_checkpoint->test_id->error)): ?>
            <div class="error"><?php echo $form_checkpoint->test_id->error; ?></div>
            <?php endif; ?>
        </div>

        <div class="field">
            <input type="submit" value="Сохранить" />
        </div>
    </div>
</form>