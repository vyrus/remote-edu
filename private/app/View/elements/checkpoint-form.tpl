<?php $form_checkpoint = $this->form_checkpoint; ?>
<!--<pre><?php //print_r($this->materials); ?></pre>-->
<script>
    <?php $test_id = & $form_checkpoint->test_id->value ?>
    var test_selection, test_id = <?php echo (empty($test_id) ? 'null' : $test_id) ?>,
        test_theme = '<?php echo (isset($this->test_theme) ? $this->test_theme : 'null') ?>';

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

    function selectTest() {
        $('#test-list').html('');
        $('#list-status').show();

        test_selection.dialog('open');

        $.ajax({
            type:     'GET',
            url:      '/tests/ajax_load_list',
            dataType: 'json',
            success:  onLoadListSuccess,
            error:    onAjaxError
        });

        return false;
    }

    function onLoadListSuccess(response) {
        $('#list-status').hide();

        var link, li;
        for (test_id in response)
        {
            theme = response[test_id];

            link = $('<a>').attr('href', '#')
                           .text(theme)
                           .click(createOnClickHandler(test_id, theme));
            li = $('<li>').html(link);
            $('#test-list').append(li);
        }

        $('#test-list').parent().show();
    }

    function createOnClickHandler(test_id, theme) {
        return function() {
            setTest(test_id, theme);
            test_selection.dialog('close');
        };
    }

    function onAjaxError(xhr, textStatus, errorThrown) {
        var msg = 'Ошибка: ' + textStatus;
        $('#list-status').text(msg);
    }

    function setTest(test_id, theme) {
        $('#checkpoint_test_id').attr('value', test_id);
        $('#test_theme').text(theme);
    }

    $(document).ready(function() {
        updateCheckboxForm();
        $('#active').change(function () {
            updateCheckboxForm();
        });
        $('#checkpoint_type').change(function () {
            updateTestId();
        });

        test_selection = $('#test-selection-dialog').dialog({
            modal: true,
            autoOpen: false,
            title: 'Выбор теста'
        });

        if (null != test_id) {
            setTest(test_id, test_theme);
        }
    })
</script>

<style>
    #list-status {
        text-align: center;
        margin-top: 15%;
    }

    #test-list li a {
        color: #33549D;
    }
</style>

<form action="<?php echo $form_checkpoint->action() ?>" method="<?php echo $form_checkpoint->method() ?>">
    <div class="form">
        <input name="section_id" type="hidden" id="section_id" value="<?php echo $this->section_id; ?>" />
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

        <div class="field" id="checkpoint_test_id_field" style="display: none;">
            Тест: <span id="test_theme"></span> <a href="#" onclick="return selectTest();">Выбрать</a>
            <input id="checkpoint_test_id" name="<?php echo $form_checkpoint->test_id->name; ?>" type="hidden" value="<?php echo $form_checkpoint->test_id->value; ?>" />

            <?php if (isset($form_checkpoint->test_id->error)): ?>
            <div class="error"><?php echo $form_checkpoint->test_id->error; ?></div>
            <?php endif; ?>
        </div>

        <div class="field">
            <input type="submit" value="Сохранить" />
        </div>
    </div>
</form>

<div id="test-selection-dialog" style="display: none;">
    <div id="list-status">Загрузка...</div>
    <div style="display: none;">
        <ul id="test-list"></ul>
    </div>
</div>