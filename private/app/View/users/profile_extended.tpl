<?php $this->title = 'Расширенный профиль слушателя' ?>
<?php $form = $this->form ?>

<script type="text/javascript">
    function time() {
        return new Date().getTime();
    }
    
    function getRegionId() {
        return $('#region_id').attr('value');
    }
                        
    /**
    * @todo Disable response cache.
    */
    
    var custom_type_idx = 3;
                               
    $(function() {
        if (undefined == $('#region_id').attr('value')) {
            $('#city').attr('disabled', 'disabled');
        }
        
        if (custom_type_idx != $('#doc_type')[0].selectedIndex) {
            $('#doc_custom_field').hide();
        }
        
        $('#birthday').datepicker({
            dateFormat: 'dd.mm.yy',
            maxDate: '',
            changeYear: true,
            changeMonth: true,
            yearRange: '-100:+0'
        });
        
        $('#passport_given_date').datepicker({
            dateFormat: 'dd.mm.yy',
            maxDate: '',
            changeYear: true,
            changeMonth: true,
            yearRange: '-100:+0'
        });
        
        $('#region').autocomplete({
            serviceUrl: '/ajax/autocomplete/region/',
            params: {
                timestamp: time
            },
            onSelect: function(value, data) {
                $('#region_id').attr('value', data);
                $('#city').attr('disabled', false);
            }
        });
        
        $('#city').autocomplete({
            serviceUrl: '/ajax/autocomplete/city/',
            params: {
                region_id: getRegionId,
                timestamp: time
            },
            onSelect: function(value, data) {
                $('#city_id').attr('value', data);
            }
        });
        
        $('#doc_type').change(function () {
            if (custom_type_idx == $('#doc_type')[0].selectedIndex) {
                $('#doc_custom_field').show();
            } else {
                $('#doc_custom_field').hide();
            }
        });
    });
</script>

<form action="<?php echo $form->action() ?>" method="<?php echo $form->method() ?>">
<div class="form">
    <?php /* Фамилия */ $field = $form->surname ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Фамилия:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
        
    <?php /* Имя */ $field = $form->name ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Имя:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
        
    <?php /* Отчество */ $field = $form->patronymic ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Отчество:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
    
    <?php /* Дата рождения */ $field = $form->birthday ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Дата рождения:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
            
    <?php /* Серия паспорта */ $field = $form->passport_series ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Серия паспорта:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>

    <?php /* Номер паспорта */ $field = $form->passport_number ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Номер паспорта:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
    
    <?php /* Кем выдан паспорт */ $field = $form->passport_given_by ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Кем выдан паспорт:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>

    <?php /* Дата выдачи паспорта */ $field = $form->passport_given_date ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Дата выдачи паспорта:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>

    <?php /* Область/край */ $field = $form->region ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Область/край:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" style="width: 150px;"/> 
      
      <?php $field = $form->region_id ?> 
      <input type="hidden" name="<?php echo $field->name ?>" id="region_id" value="<?php echo $field->value ?>" />
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
            
    <?php /* Город */ $field = $form->city ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Город:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
      
      <?php $field = $form->city_id ?> 
      <input type="hidden" name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" />
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
    
    <?php /* Улица */ $field = $form->street ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Улица:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>        
    
    <?php /* Дом/корпус */ $field = $form->house ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Дом/корпус:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>        
    
    <?php /* Квартира/комната */ $field = $form->flat ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Квартира/комната:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>        
    
    <?php /* Вид документа об образовании */ $field = $form->doc_type ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Вид документа:</label>
      <select name="<?php echo $field->name ?>" id="<?php echo $field->name ?>">
        <?php $values = array(
                    Model_User::DOC_TYPE_EMPTY          => '',
                    Model_User::DOC_TYPE_DIPLOMA_HIGH   => 'Диплом о высшем образовании',
                    Model_User::DOC_TYPE_DIPLOMA_MEDIUM => 'Диплом о среднем образовании', 
                    Model_User::DOC_TYPE_CUSTOM         => 'Иное'
              )
        ?>
        <?php foreach ($values as $value => $title): ?>
            <option value="<?php echo $value ?>"<?php echo ($value == $field->value ? ' selected' : '') ?>>
                <?php echo $title ?>
            </option>
        <?php endforeach ?>
      </select> 
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
            
    <?php /* Иной вид документа */ $field = $form->doc_custom_type ?>
    <div class="field" id="doc_custom_field">
      <label for="<?php echo $field->name ?>">Иное:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
            
    <?php /* Номер документа */ $field = $form->doc_number ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Номер документа:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
            
    <?php /* Год окончания */ $field = $form->exit_year ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Год окончания:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
    
    <?php /* Специальность */ $field = $form->speciality ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Специальность:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
    </div>                      
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
            
    <?php /* Квалификация */ $field = $form->qualification ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Квалификация:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
            
    <?php /* Мобильный телефон */ $field = $form->phone_mobile ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Мобильный телефон:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
            
    <?php /* Стационарный телефон */ $field = $form->phone_stationary ?>
    <div class="field">
      <label for="<?php echo $field->name ?>">Стационарный телефон:</label>
      <input name="<?php echo $field->name ?>" id="<?php echo $field->name ?>" value="<?php echo $field->value ?>" /> 
    </div>
    
            <?php if (isset($field->error)): ?>
            <div class="error"><?php echo $field->error ?></div>
            <?php endif; ?>
            
    <div class="field">
        <input type="submit" value="Сохранить" />
    </div>
</div>
</form>