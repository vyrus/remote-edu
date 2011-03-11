<?php
	$directions = $this->directions;
	$courses = $this->courses;
	$disciplines = $this->disciplines;
	$sections = $this->sections;
	
	$mapProgramDiscipline = $this->mapProgramDiscipline;
	//print_r($mapProgramDiscipline);
	$mapDisciplineSection = $this->mapDisciplineSection;
	//print_r($mapDisciplineSectio);
	
	foreach ($sections as $i => $disciplineSections) {
		foreach ($disciplineSections as $j => $section) {
			$sections[$i][$j]['title'] = 'Раздел ' . $section['number'] . ': ' . $section['title'];
		}
	}

	$programID = $this->programID;
	$disciplineID = $this->disciplineID;
	$sectionID = $this->sectionID;
	
	$materials = $this->materials;
	$materialTypes = Model_Educational_Materials::$MATERIAL_TYPES_CAPTIONS;
?>

<style type="text/css">
	tr.odd, th.odd {
		background-color: #ECECEC;
	}

	tr.even, th.even {
		background-color: #FFFFFF;
	}

	th {
		font-weight 	: bold;
	}

	td.description, th.description {
		width 			: 300px;
	}

	.materials th,
	.materials td {
		padding 		: 2px 0px;
	}

	.materials td {
		padding-left 	: 15px;
	}

	.materials td.checkbox,
	.materials th.checkbox {
		text-align		: center;
		padding 		: 3px;
	}

	.materials td.edit, .materials td.type,
	.materials th.edit, .materials th.type {
	    text-align: center;
	    padding: 2px 15px;
    }
</style>
<script type="text/javascript">
	var PROGRAMS		= [];
	PROGRAMS['direction']	= [<?php $delimiter = ''; foreach ($directions as $i => $direction): echo $delimiter; ?>{'id':<?php echo $direction['program_id']; ?>,'title':'<?php echo $direction["title"]; $delimiter = ","; ?>'}<?php endforeach; ?>];
	PROGRAMS['course'] 	= [<?php $delimiter = ''; foreach ($courses as $i => $course): echo $delimiter; ?>{'id':<?php echo $course['program_id']; ?>,'title':'<?php echo $course["title"]; $delimiter = ","; ?>'}<?php endforeach; ?>];

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
	
	var MATERIALS   =   [];
	<?php foreach ($materials as $i => $sectionMaterial): ?>
	MATERIALS[<?php echo $i; ?>] =
	        [<?php $delimiter = ""; foreach ($sectionMaterial as $j => $material): echo $delimiter ?>{"id":<?php echo $material['id']; ?>,"title":"<?php echo $material['description'].' ['.$material['type_rus'].']'; ?>"}<?php ; $delimiter =","; endforeach; ?>];
	<?php endforeach; ?>
	
	// карты php ->  JavaScript
	
	var MAP_PROGRAM_DISCIPLINE = [];
	<?php foreach ($mapProgramDiscipline as $i => $ar): ?>
	MAP_PROGRAM_DISCIPLINE[<?php echo $i; ?>] = 
	        [<?php $delimiter = ""; foreach ($ar as $j => $val): echo $delimiter; echo $val; $delimiter =","; endforeach; ?>];
	<?php endforeach; ?>
	
	var MAP_DISCIPLINE_SECTION = [];
	<?php foreach ($mapDisciplineSection as $i => $ar): ?>
	MAP_DISCIPLINE_SECTION[<?php echo $i; ?>] = 
	        [<?php $delimiter = ""; foreach ($ar as $j => $val): echo $delimiter; echo $val; $delimiter =","; endforeach; ?>];
	<?php endforeach; ?>
	
	
	// данные  константы передаются в updateMaterial
	const ANY_PROGRAM = 0;
	const ANY_DISCIPLINE = 1;
	const ANY_SECTION = 2;
	const ANY_MATERIAL = 3;
	
	
	/*
	function clearSelect (select) {
		$ ('option:gt(0)', select).remove ();
	}

	function updateSelect (parentSelect, select, items, clear) {
		
		for (var i = 0; i < clear.length; i++) {
			clearSelect (clear[i]);
			$ (clear[i]).attr ('disabled', 'disabled');
		}

		if (($ (parentSelect).val () != -1) && (clear.length)) {
			$ (clear[0]).attr ('disabled', '');
		}

		var parentSelectOptions	= $ (parentSelect).attr ('options');
		var selectItems 		= items[parentSelectOptions[$ (parentSelect).attr ('selectedIndex')].value];
		if (selectItems) {
			for (var i = 0; i < selectItems.length; i++) {
				$ (select).append ('<option value="' + selectItems[i].id + '">' + selectItems[i].title + '</option>');
			}
		}
	}
	*/
	
	function clearSelect (select) {
		while (select.firstChild) {
		    select.removeChild (select.firstChild);
		}
	}
	
	function updateSelect (parentSelect, select, items, clear) {
		
	    for (var i = 0; i < clear.length; i++) {
		    clearSelect (clear[i]);
		}
		
		var selectItems = items[parentSelect.options[parentSelect.selectedIndex].value];
		if (selectItems) {
		    //select.options[0] = new Option (anyStr, 0);
		    for (var i = 0; i < selectItems.length; i++) {
				select.options[i+1] = new Option (selectItems[i].title, selectItems[i].id);
		    }
		}
	}
	
	function updateMaterialBase(index, select) {
		var selectItems = MATERIALS[index];
		if (selectItems) {
		    var len = select.length;
		    for (var i = 0; i < selectItems.length; i++) {
				//select.add(new Option (selectItems[i].title, selectItems[i].id));
				select.options[i+len] = new Option (selectItems[i].title, selectItems[i].id);
		    }
		}
	}
	
	function updateMaterial(parentSelect, select, any) {
		
		// отключаем/включаем кнопки
		if (any == ANY_MATERIAL) {
			upButton.disabled = false;
			downButton.disabled = false;
			saveOrderButton.disabled = false;
		} else {
			upButton.disabled = true;
			downButton.disabled = true;
			saveOrderButton.disabled = true;
		}
		
		// непосредственно изменяем значение в поле
		//alert(parentSelect.length);
		clearSelect (select);
		switch (any) {
			case ANY_PROGRAM: // выводим все!
				for (var k = 1; k < parentSelect.length; k++) {
					var a = parentSelect.options[k].value;
					for (var i = 0; i < MAP_PROGRAM_DISCIPLINE[a].length; i++) {
						for (var j = 0; j < MAP_DISCIPLINE_SECTION[MAP_PROGRAM_DISCIPLINE[a][i]].length; j++) {
							updateMaterialBase(MAP_DISCIPLINE_SECTION[MAP_PROGRAM_DISCIPLINE[a][i]][j], select);
						}
					}	
				}
				break;
			case ANY_DISCIPLINE:
				var a = parentSelect.options[parentSelect.selectedIndex].value;
				for (var i = 0; i < MAP_PROGRAM_DISCIPLINE[a].length; i++) {
					for (var j = 0; j < MAP_DISCIPLINE_SECTION[MAP_PROGRAM_DISCIPLINE[a][i]].length; j++) {
						updateMaterialBase(MAP_DISCIPLINE_SECTION[MAP_PROGRAM_DISCIPLINE[a][i]][j], select);
					}
				}
				break;
			case ANY_SECTION:
				for (var i = 0; i < MAP_DISCIPLINE_SECTION[parentSelect.options[parentSelect.selectedIndex].value].length; i++) {
					updateMaterialBase(MAP_DISCIPLINE_SECTION[parentSelect.options[parentSelect.selectedIndex].value][i], select);
				}
				break;
			case ANY_MATERIAL:
				updateMaterialBase(parentSelect.options[parentSelect.selectedIndex].value, select);
				break;
		}
	}

	// функции подъема строки в выборбоксе вверх и вниз
	
	function down(select) {
	    if (select.selectedIndex < select.length) {
			var tempText = select.options[select.selectedIndex + 1].text;
			var tempValue = select.options[select.selectedIndex + 1].value;
			select.options[select.selectedIndex + 1].text = select.options[select.selectedIndex].text;
			select.options[select.selectedIndex + 1].value = select.options[select.selectedIndex].value;
			select.options[select.selectedIndex].text = tempText;
			select.options[select.selectedIndex].value = tempValue;
			select.selectedIndex = select.selectedIndex + 1;
	    }
	}
	
	function up(select) {
	    if (select.selectedIndex > 0) {
			var tempText = select.options[select.selectedIndex - 1].text;
			var tempValue = select.options[select.selectedIndex - 1].value;
			select.options[select.selectedIndex - 1].text = select.options[select.selectedIndex].text;
			select.options[select.selectedIndex - 1].value = select.options[select.selectedIndex].value;
			select.options[select.selectedIndex].text = tempText;
			select.options[select.selectedIndex].value = tempValue;
			select.selectedIndex = select.selectedIndex - 1;
		}
	}
	
	// работа с блоком материалов
    
	// тут не понятно, как лучше-то реализовать
	function addMaterial() {
	    /*
	    if (sectionsSelect.selectedIndex == -1) {
			alert ('Необходимо выбрать дисциплину');
			return;
	    }
	    */
	    window.location = '<?php echo $this->_links->get('materials.teacher.upload') ?>' ;//+ 
			      //sectionsSelect.options[sectionsSelect.selectedIndex].value + '/';
	}
	
	function removeMaterial () {
	    if (materialsSelect.selectedIndex == -1) {
			alert ('Необходимо выбрать один/несколько материалов');
			return;
	    }
	    
	    var materialDelete = '';
	    var first = true;
	    for (var i = 0; i < materialsSelect.length; i++) {
			if (materialsSelect.options[i].selected) {
				if (first) {
					first = false;
				} else {
					materialDelete += ',';
				}
				materialDelete += materialsSelect.options[i].value;
			}
		}
	    
	    document.forms[0].materialDeleteInfo.value = materialDelete;
	    document.forms[0].action = '<?php echo $this->_links->get('materials.teacher.remove') ?>';
	    document.forms[0].submit();
	
	}
	
	function editMaterial() {
	    if (materialsSelect.selectedIndex == -1) {
			alert ('Необходимо выбрать материал');
			return;
	    }
	
	    window.location = '<?php echo $this->_links->get('materials.teacher.edit') ?>' + 
			      materialsSelect.options[materialsSelect.selectedIndex].value + '/';
	}
	
	function saveMaterialOrder() {
	    var materialOrder = '';
	    for (var i = 0; i < materialsSelect.length; i++) {
			if (i > 0) {
			    materialOrder += ',';
			}
			materialOrder += materialsSelect.options[i].value;
	    }
	    document.forms[0].materialOrderInfo.value = materialOrder;
	    document.forms[0].action = '<?php echo $this->_links->get('materials.teacher.save-order') ?>';
	    document.forms[0].submit();
	}
	
	function downloadMaterial() {
	    if (materialsSelect.selectedIndex == -1) {
			alert ('Необходимо выбрать материал');
			return;
	    }
	    
		//alert (materialsSelect.selectedIndex);
	    window.location = '<?php echo $this->_links->get('materials.download') ?>' + 
			      materialsSelect.options[materialsSelect.selectedIndex].value + '/';      
	}

</script>
<h3>Список материалов</h3>
<nobr>
<form name="filter" method="post">
<input type="hidden" id="materialOrderInfo" name="materialOrderInfo">
<input type="hidden" id="materialDeleteInfo" name="materialDeleteInfo">
	
<table>	
<tr>
<td><select id="programsSelect" class="educationProgramItems" size="1" style="width: 250px;"
	    onchange="updateSelect (programsSelect, disciplinesSelect, DISCIPLINES, [disciplinesSelect, sectionsSelect, materialsSelect]);
		if (programsSelect.selectedIndex != 0) updateMaterial (programsSelect, materialsSelect, ANY_DISCIPLINE);
		else updateMaterial (programsSelect, materialsSelect, ANY_PROGRAM);
		disciplinesSelect.options[0] = new Option ('--Любая дисциплина--', 0);
		disciplinesSelect.options[0].selected = true;
		sectionsSelect.options[0] = new Option ('--Любой раздел--', 0);
		sectionsSelect.options[0].selected = true;">
	<option value="0" selected="selected">--Любое направление--</option>
	<?php foreach ($directions as $i => $direction): ?>
	<option value="<?php echo $direction['program_id']; ?>"><?php echo $direction['title']; ?></option>
	<?php endforeach; ?>
</select></td>
<td><select id="disciplinesSelect" class="educationProgramItems" size="1" style="width: 250px;"
	    onchange="updateSelect (disciplinesSelect, sectionsSelect, SECTIONS, [sectionsSelect, materialsSelect]);
		if (disciplinesSelect.selectedIndex != 0) updateMaterial (disciplinesSelect, materialsSelect, ANY_SECTION);
		else updateMaterial (programsSelect, materialsSelect, ANY_DISCIPLINE);
		sectionsSelect.options[0] = new Option ('--Любой раздел--', 0);
		sectionsSelect.options[0].selected = true;">
	<option value="0" selected="selected">--Любая дисциплина--</option>
	</select></td>
<td><select id="sectionsSelect" class="educationProgramItems" size="1" style="width: 250px;"
	    onchange="
		if (sectionsSelect.selectedIndex != 0) updateMaterial(sectionsSelect, materialsSelect, ANY_MATERIAL);
		else updateMaterial(disciplinesSelect, materialsSelect, ANY_SECTION);"><
	<option value="0" selected="selected">--Любой раздел--</option>
	</select></td>
</tr>
</table>

<table>
<tr>
    <td>
        Материалы
    </td>
</tr>
<tr>
    <td>
        <select multiple="on" id="materialsSelect" class="educationProgramItems" size="20" style="min-width: 800px;"></select>
    </td>
</tr>
<tr>
    <td>
        <input type="button" class="addButton" value="&#x002B;" onclick="addMaterial ();">
        <input type="button" class="editButton" value="&#x270E;" onclick="editMaterial ();">
        <input type="button" class="removeButton" value="&#x2212;" onclick="removeMaterial ();">&nbsp;
        <input type="button" class="upButton" id="upButton" value="&uarr;" onclick="up(materialsSelect)">
        <input type="button" class="downButton" id="downButton" value="&darr;" onclick="down(materialsSelect)">
        <input type="button" value="Сохранить" id="saveOrder" onclick="saveMaterialOrder()">
        <input type="button" value="Загрузить" onclick="downloadMaterial()">
    </td>
</tr>
</table>

</form>
</nobr>

<script type="text/javascript">
	/*
	ЗА ЭТО НАДО РОГЗАМИ СЕЧЬ!!!
	var programsTypeSelect = $('#programsTypeSelect');
	var programsSelect = $('#programsSelect');
	var disciplinesSelect = $('#disciplinesSelect');
	var sectionsSelect = $('#sectionsSelect');
	var materialsSelect = $('#materialsSelect');
	*/
	
	var programsTypeSelect = document.getElementById('programsTypeSelect');
	var programsSelect = document.getElementById('programsSelect');
	var disciplinesSelect = document.getElementById('disciplinesSelect');
	var sectionsSelect = document.getElementById('sectionsSelect');
	var materialsSelect = document.getElementById('materialsSelect');

	
	var upButton = document.getElementById('upButton');
	var downButton = document.getElementById('downButton');
	var saveOrderButton = document.getElementById('saveOrder');

	updateMaterial (programsSelect, materialsSelect, ANY_PROGRAM);
</script>