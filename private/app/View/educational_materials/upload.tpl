<?php
    $directions 	= $this->directions;
    $courses		= $this->courses;
    $disciplines	= $this->disciplines;
    $sections		= $this->sections;

    foreach ($sections as $i => $disciplineSections) {
        foreach ($disciplineSections as $j => $section) {
            $sections[$i][$j]['title'] = 'Раздел ' . $section['number'] . ': ' . $section['title'];
        }
    }

    $invalidMaterialsForms = $this->invalidMaterialsForms;
?>
<style type="text/css">
    div.educationalMaterial {
    }

    div.educationalMaterial
        table {
        border-bottom 			: 2px solid #000000;
    }

    td.caption {
        width 					: 50%;
        padding-right 			: 10px;
        text-align				: right;
    }

    td.field {
        width 					: 50%;
        padding-left 			: 10px;
        text-align				: left;
    }

    td.cancel {
        width 					: 100%;
        text-align				: right;
    }

    select.educationProgramItems {
        width						: 130px;
        margin-right				: 15px;
    }
</style>

<script type="text/javascript">
    /*-----------------------------------*/
    /* Данные для диалога выбора раздела */
    /*-----------------------------------*/

    var PROGRAMS			= [];
    PROGRAMS['direction']	= [<?php $delimiter = ''; foreach ($directions as $i => $direction): echo $delimiter; ?>{'id':<?php echo $direction['program_id']; ?>,'title':'<?php echo $direction["title"]; $delimiter = ","; ?>'}<?php endforeach; ?>];
    PROGRAMS['course'] 		= [<?php $delimiter = ''; foreach ($courses as $i => $course): echo $delimiter; ?>{'id':<?php echo $course['program_id']; ?>,'title':'<?php echo $course["title"]; $delimiter = ","; ?>'}<?php endforeach; ?>];

    var DISCIPLINES = {
    <?php $disciplinesGroupsDelimiter = ''; foreach ($disciplines as $i => $specialityDiscipline): ?>
    <?php echo $disciplinesGroupsDelimiter; $disciplinesGroupsDelimiter = ','; ?>"<?php echo $specialityDiscipline[0]['program_id']; ?>": [<?php $delimiter = ""; foreach ($specialityDiscipline as $j => $discipline): echo $delimiter ?>{"id":<?php echo $discipline['discipline_id']; ?>,"title":"<?php echo $discipline['title']; ?>"}<?php ; $delimiter =","; endforeach; ?>]
    <?php endforeach; ?>
    };

    var SECTIONS 	= {
    <?php $sectionsGroupsDelimiter = ''; foreach ($sections as $i => $disciplineSection): ?>
    <?php echo $sectionsGroupsDelimiter; $sectionsGroupsDelimiter = ','; ?>"<?php echo $disciplineSection[0]['discipline_id']; ?>": [<?php $delimiter = ""; foreach ($disciplineSection as $j => $section): echo $delimiter ?>{"id":<?php echo $section['section_id']; ?>,"title":"<?php echo $section['title']; ?>","parentID":<?php echo $disciplineSection[0]['discipline_id']; ?>}<?php ; $delimiter =","; endforeach; ?>]
    <?php endforeach; ?>
    };

    var MATERIALS = [
        <?php $materialTypes = Model_Educational_Materials::$MATERIAL_TYPES_CAPTIONS; foreach ($materialTypes as $value => $caption): ?>
        {'caption':'<?php echo $caption; ?>','value':'<?php echo $value; ?>'},
        <?php endforeach; ?>
    ];

    /*----------------------------------------------------*/
    /* Функции для работы группы select'ов выбора раздела */
    /*----------------------------------------------------*/

    function switchProgramsType () {
        clearSelect (programsSelect);
        clearSelect (disciplinesSelect);
        clearSelect (sectionsSelect);

        for (var i = 0; i < PROGRAMS[programsTypeSelect.val ()].length; i++) {
            programsSelect.append ('<option value="' + PROGRAMS[programsTypeSelect.val ()][i].id + '">' + PROGRAMS[programsTypeSelect.val ()][i].title + '</option>');
        }
    }

    function clearSelect (select) {
        select.empty ();
    }

    function updateSelect (parentSelect, select, items, clear) {
        for (var i = 0; i < clear.length; i++) {
            clearSelect (clear[i]);
        }

        var parentSelectOptions	= parentSelect.attr ('options');
        var selectItems 		= items[parentSelectOptions[parentSelect.attr ('selectedIndex')].value];
        if (selectItems) {
            for (var i = 0; i < selectItems.length; i++) {
                select.append ('<option value="' + selectItems[i].id + '">' + selectItems[i].title + '</option>');
            }
        }
    }

    function getItemById (items, itemID) {
        for (var i in items) {
            if (i) {
                if ($.isArray (items[i])) {
                    for (var j = 0; j < items[i].length; j++) {
                        if (items[i][j].id == itemID) {
                            return {
                                'item'		: items[i][j],
                                'parentID'	: i
                            };
                        }
                    }
                }
                else if (items[i].id == itemID) {
                    return items[i];
                }
            }
        }
    }

    function getFullSectionDescription (sectionID) {
        var section 	= getItemById (SECTIONS, sectionID);
        var discipline	= getItemById (DISCIPLINES, section.parentID);
        var direction	= getItemById (PROGRAMS['direction'], discipline.parentID);

        return direction.title + ', ' + discipline.item.title + ', ' + section.item.title;
    }

    function fillSelects (sectionID) {
        var section 	= getItemById (SECTIONS, sectionID);
        var discipline	= getItemById (DISCIPLINES, section.parentID);
        var direction	= getItemById (PROGRAMS['direction'], discipline.parentID);

        var programsSelectOptions	= programsSelect.attr ('options');
        var selectedProgramIndex	= programsSelect.attr ('selectedIndex');
        if (
            (selectedProgramIndex < 0) ||
            (selectedProgramIndex >= 0) &&
            (programsSelectOptions[selectedProgramIndex].value != direction.id)
        ){
            for (var i = 0; i < PROGRAMS['direction'].length; i++) {
                if (PROGRAMS['direction'][i].id == direction.id) {
                    programsSelectOptions[i].selected = 'selected';
                    break;
                }
            }

            updateSelect (programsSelect, disciplinesSelect, DISCIPLINES, [disciplinesSelect]);
        }

        var disciplinesSelectOptions	= disciplinesSelect.attr ('options');
        var selectedDisciplineIndex		= disciplinesSelect.attr ('selectedIndex');
        if (
            (selectedDisciplineIndex < 0) ||
            (selectedDisciplineIndex >= 0) &&
            (disciplinesSelectOptions[selectedDisciplineIndex].value != discipline.item.id)
        ) {
            for (var i = 0; i < DISCIPLINES[direction.id].length; i++) {
                if (DISCIPLINES[direction.id][i].id == discipline.item.id) {
                    disciplinesSelectOptions[i].selected = 'selected';
                    break;
                }
            }

            updateSelect (disciplinesSelect, sectionsSelect, SECTIONS, [sectionsSelect]);
        }

        var sectionsSelectOptions	= sectionsSelect.attr ('options');
        var selectedSectionIndex	= sectionsSelect.attr ('selectedIndex');

        if (
            (selectedSectionIndex < 0) ||
            (selectedSectionIndex >= 0) &&
            (sectionsSelectOptions[selectedSectionIndex].value != section.item.id)
        ) {
            for (var  i = 0; i < SECTIONS[discipline.item.id].length; i++) {
                if (SECTIONS[discipline.item.id][i].id == section.item.id) {
                    sectionsSelectOptions[i].selected = 'selected';
                    break;
                }
            }
        }
    }

    /*-----------------------------------------------*/
    /* "Класс" для формы загрузки учебного материала */
    /*-----------------------------------------------*/

    __EducationalMaterial = [];

    function EducationalMaterial () {
        this.id = __EducationalMaterial.length;
        __EducationalMaterial[this.id] = this;
    }

    EducationalMaterial.prototype.create = function(descriptionValue, sectionValue, typeValue, descriptionErrorText, filenameErrorText, sectionErrorText) {
        var description = document.createElement ('input');
        description.type = 'text';
        description.name = 'material[' + this.id + '][description]';
        description.value = ((descriptionValue) ? (descriptionValue) : (''));

        var fileReference = document.createElement ('input');
        fileReference.type = 'file';
        fileReference.name = 'fileReference' + this.id;

        this.section = document.createElement ('input');
        this.section.type = 'hidden';
        this.section.name = 'material[' + this.id + '][section]';
        this.section.value = ((sectionValue) ? (sectionValue) : (''));

        this.materialType = document.createElement("select");
        this.materialType.name = 'material[' + this.id + '][type]';

        for (var i = 0; i < MATERIALS.length; i++) {
            this.materialType.options[i] = new Option(MATERIALS[i].caption, MATERIALS[i].value);

            if (typeValue && MATERIALS[i].value == typeValue) {
                this.materialType.options[i].selected = 'selected';
            }
        }

        this.container = document.createElement ('div').appendChild (
            document.createElement ('table').appendChild (
                document.createElement ('tbody').appendChild (
                    document.createElement ('tr').appendChild (
                        document.createElement ('td').appendChild (
                            document.createTextNode ('Название материала')
                        ).parentNode
                    ).parentNode.appendChild (
                        document.createElement ('td').appendChild (
                            description
                        ).parentNode
                    ).parentNode
                ).parentNode.appendChild (
                    document.createElement ('tr').appendChild (
                        document.createElement ('td').appendChild (
                            document.createTextNode ('Файл')
                        ).parentNode
                    ).parentNode.appendChild (
                        document.createElement ('td').appendChild (
                            fileReference
                        ).parentNode
                    ).parentNode
                ).parentNode.appendChild (
                    document.createElement ('tr').appendChild (
                        document.createElement ('td').appendChild (
                            document.createTextNode ('Раздел')
                        ).parentNode
                    ).parentNode.appendChild (
                        document.createElement ('td').appendChild (
                            document.createElement ('a').appendChild (
                                document.createTextNode ('выбрать')
                            ).parentNode
                        ).parentNode.appendChild (
                            this.section
                        ).parentNode
                    ).parentNode
                ).parentNode.appendChild (
                    document.createElement ('tr').appendChild (
                        document.createElement ('td').appendChild (
                            document.createTextNode ('Тип')
                        ).parentNode
                    ).parentNode.appendChild (
                        document.createElement ('td').appendChild (
                            this.materialType
                        ).parentNode
                    ).parentNode
                ).parentNode.appendChild (
                    document.createElement ('tr').appendChild (
                        document.createElement ('td').appendChild (
                            document.createElement ('a').appendChild (
                                document.createTextNode ('отмена')
                            ).parentNode
                        ).parentNode
                    ).parentNode
                ).parentNode
            ).parentNode
        ).parentNode;

        this.container.className = 'educationalMaterial';

        document.getElementById ('educationalMaterials').appendChild (this.container);

        if ((this.section.value != '') && (sectionErrorText === null)) {
            $('tr:eq(2) td:last-child a', this.container).text(getFullSectionDescription (this.section.value));
        }
        $('tr:eq(2) td:last-child a', this.container).attr('href', 'javascript:__EducationalMaterial[' + this.id + '].openSectionSelectDialog()');
        $('tr:lt(4) td:first-child', this.container).addClass('caption');
        $('tr:lt(4) td:last-child', this.container).addClass('field');
        $('tr:eq(4) td', this.container).attr('colspan', '2');
        $('tr:eq(4) td', this.container).addClass('cancel');
        $('tr:eq(4) td a', this.container).attr('href', 'javascript:__EducationalMaterial[' + this.id + '].destroy()');

        var descriptionRow = $('tr:eq(0)', this.container);
        var filenameRow	= $('tr:eq(1)', this.container);
        var sectionRow = $('tr:eq(2)', this.container);

        if (descriptionErrorText !== null) {
            $(descriptionRow).after(this.createErrorMessage(descriptionErrorText))
        }

        if (filenameErrorText !== null) {
            $(filenameRow).after(this.createErrorMessage(filenameErrorText))
        }

        if (sectionErrorText !== null) {
            $(sectionRow).after(this.createErrorMessage(sectionErrorText))
        }
    }

    EducationalMaterial.prototype.createErrorMessage = function (errorText) {
        var errorMessage = document.createElement ('tr').appendChild (
            document.createElement ('td')
        ).parentNode.appendChild (
            document.createElement ('td').appendChild (
                document.createElement ('div').appendChild (
                    document.createTextNode (errorText)
                ).parentNode
            ).parentNode
        ).parentNode;

        $('div', errorMessage).addClass ('error');
        $('td:first-child', errorMessage).addClass ('caption');
        $('td:last-child', errorMessage).addClass ('field');

        return errorMessage;
    }

    EducationalMaterial.prototype.openSectionSelectDialog = function () {
        eval ('var callback = function () {__EducationalMaterial[' + this.id + '].onSectionSelect ();}');
        selectSectionDialog.dialog (
            'option',
            'buttons',
            {
                'Выбрать': callback
            }
        );
        selectSectionDialog.dialog ('open');

        if (this.section.value != '') {
            fillSelects (this.section.value);
        }
    }

    EducationalMaterial.prototype.onSectionSelect = function () {
        if (sectionsSelect.attr ('selectedIndex') == -1) {
            alert ('Необходимо выделить требуемый раздел');
            return;
        }

        var sectionsSelectOptions 		= sectionsSelect.attr ('options')
        this.section.value 				= sectionsSelectOptions[sectionsSelect.attr ('selectedIndex')].value;

        $ ('tr:eq(2) td:last-child a', this.container).text (getFullSectionDescription (this.section.value));

        selectSectionDialog.dialog ('close');
    }

    EducationalMaterial.prototype.destroy = function () {
        this.container.parentNode.removeChild (this.container);
    }

    function createEducationalMaterial (descriptionValue, sectionValue, typeValue, descriptionErrorText, filenameErrorText, sectionErrorText) {
        (new EducationalMaterial ()).create (descriptionValue, sectionValue, typeValue, descriptionErrorText, filenameErrorText, sectionErrorText);
    }
</script>

<div id="sectionSelectDialog">
    <table cellspacing="0" cellpadding="0">
    <tr><td><select id="programsTypeSelect" onchange="switchProgramsType ();"><option value="direction">Направления</option><option value="course">Курсы</option></select></td><td>Дисциплины</td><td>Разделы</td></tr>
    <tr>
    <td><select id="programsSelect" class="educationProgramItems" size="10" onchange="updateSelect (programsSelect, disciplinesSelect, DISCIPLINES, [disciplinesSelect, sectionsSelect]);">
    <?php foreach ($directions as $i => $direction): ?>
    <option value="<?php echo $direction['program_id']; ?>"><?php echo $direction['title']; ?></option>
    <?php endforeach; ?>
    </select></td>
    <td><select id="disciplinesSelect" class="educationProgramItems" size="10" onchange="updateSelect (disciplinesSelect, sectionsSelect, SECTIONS, [sectionsSelect]);"></select></td>
    <td><select id="sectionsSelect" class="educationProgramItems" size="10"></select></td>
    </tr>
    </table>
</div>

<h3>Загрузить материалы</h3>
<form id="educationalMaterials" name="educationalMaterials" method="post" action="<?php echo $this->_links->get('materials.upload') ?>" enctype="multipart/form-data">
</form>
<a href="javascript:createEducationalMaterial(null, null, null, null, null, null)">добавить материал</a>
<a href="javascript:document.educationalMaterials.submit()">загрузить</a>

<script type="text/javascript">
    var programsTypeSelect = $('#programsTypeSelect');
    var programsSelect = $('#programsSelect');
    var disciplinesSelect = $('#disciplinesSelect');
    var sectionsSelect = $('#sectionsSelect');

    var selectSectionDialog	= $('#sectionSelectDialog');
    selectSectionDialog.dialog (
        {
            autoOpen : false,
            draggable : false,
            modal : true,
            resizable : false,
            title : 'Выбор раздела',
            width : 'auto'
        }
    );

<?php if (empty ($invalidMaterialsForms)): ?>
    createEducationalMaterial(null, null, null, null, null, null);
<?php else: ?>
<?php foreach ($invalidMaterialsForms as $i => $form): ?>
<?php
    $description = $form->description;
    $filename = $form->filename;
    $section = $form->section;
    $type = $form->type;
?>
    createEducationalMaterial (
        '<?php echo $description->value; ?>',
        <?php echo (($section->value) ? ($section->value) : ("''")); ?>,
        '<?php echo $type->value; ?>',
        <?php echo ((isset ($description->error)) ? ("'" . $description->error . "'") : ('null')); ?>,
        <?php echo ((isset ($filename->error)) ? ("'" . $filename->error . "'") : ('null')); ?>,
        <?php echo ((isset ($section->error)) ? ("'" . $section->error . "'") : ('null')); ?>
    );
<?php endforeach; ?>
<?php endif; ?>
</script>