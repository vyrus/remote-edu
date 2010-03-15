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
	
	$programID		= $this->programID;
	$disciplineID	= $this->disciplineID;
	$sectionID		= $this->sectionID;
	$materials 		= $this->materials;
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
</style>
<script type="text/javascript">
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
	
	function switchProgramsType () {
		clearSelect (programsSelect);
		clearSelect (disciplinesSelect);
		clearSelect (sectionsSelect);
		
		for (var i = 0; i < PROGRAMS[programsTypeSelect.val ()].length; i++) {
			programsSelect.append ('<option value="' + PROGRAMS[programsTypeSelect.val ()][i].id + '">' + PROGRAMS[programsTypeSelect.val ()][i].title + '</option>');
		}
	}
	
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
	
	function setAllCheckboxesStatus (form) {
		$ ('#' + form + ' :checkbox').attr ('checked', $ ("#" + form + " :checkbox[name='all']").attr ('checked'));
	}	
</script>
<h3>Список материалов</h3>
<nobr>
<form name="filter" method="post" action="<?php echo $this->_links->get('admin.materials') ?>">
<select id="programsSelect" name="programsSelect" class="educationProgramItems" onchange="updateSelect (programsSelect, disciplinesSelect, DISCIPLINES, [disciplinesSelect, sectionsSelect]);"><option value="-1" selected="selected">--Любое направление--</option>
<?php foreach ($directions as $i => $direction): ?><option value="<?php echo $direction['program_id']; ?>"<?php if ($direction['program_id'] == $programID): ?> selected="selected"<?php endif; ?>><?php echo $direction['title']; ?></option><?php endforeach; ?>
</select>
<select id="disciplinesSelect" name="disciplinesSelect" class="educationProgramItems" onchange="updateSelect (disciplinesSelect, sectionsSelect, SECTIONS, [sectionsSelect]);"<?php if ($programID == -1): ?> disabled="disabled"<?php endif; ?>><option value="-1">--Любая дисциплина--</option>
<?php if ($programID != -1 && isset ($disciplines[$programID])): ?>
<?php foreach ($disciplines[$programID] as $i => $discipline): ?>
<option value="<?php echo $discipline['discipline_id']; ?>"<?php if ($discipline['discipline_id'] == $disciplineID): ?> selected="selected"<?php endif; ?>><?php echo $discipline['title']; ?></option>
<?php endforeach; ?>
<?php endif; ?>
</select>
<select id="sectionsSelect" name="sectionsSelect" class="educationProgramItems"<?php if ($disciplineID == -1): ?> disabled="disabled"<?php endif; ?>><option value="-1">--Любой раздел--</option>
<?php if ($disciplineID != -1 && isset ($sections[$disciplineID])): ?>
<?php foreach ($sections[$disciplineID] as $i => $section): ?>
<option value="<?php echo $section['section_id']; ?>"<?php if ($section['section_id'] == $sectionID): ?> selected="selected"<?php endif; ?>><?php echo $section['title']; ?></option>
<?php endforeach; ?>
<?php endif; ?>
</select>
<input type="submit" value="отфильтровать" />
</form>
</nobr>
<form id="deleteMaterials" name="deleteMaterials" action="<?php echo $this->_links->get('materials.remove') ?>" method="post">
<table class="materials" border="0" cellspacing="2" cellpadding="0">
<tr class="odd"><td class="checkbox"><input name="all" type="checkbox" onclick="setAllCheckboxesStatus ('deleteMaterials')" /></th><th class="description">Название</th></tr>
<?php if (! empty ($materials)): ?>
<?php foreach ($materials as $i => $material): ?>
<tr<?php if ($i % 2): ?> class="odd"<?php else: ?> class="even"<?php endif; ?>><td class="checkbox"><input name="<?php echo $material['id']; ?>" type="checkbox" /></td><td class="description"><a href="<?php echo $this->_links->get('materials.download', array('material_id' => $material['id'])) ?>"><?php echo $material['description']; ?></a></td></tr>
<?php endforeach; ?>
<?php endif; ?>
</table>
</form>
<a href="javascript:document.deleteMaterials.submit()">удалить выделенные</a>

<script type="text/javascript">
	var programsTypeSelect	= $ ('#programsTypeSelect');
	var programsSelect 		= $ ('#programsSelect');
	var disciplinesSelect 	= $ ('#disciplinesSelect');
	var sectionsSelect 		= $ ('#sectionsSelect');
</script>