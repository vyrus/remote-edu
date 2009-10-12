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
?>
<link type="text/css" href="/css/ui-lightness/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
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

<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.7.2.custom.min.js"></script>
<script type="text/javascript">
	/*-----------------------------------*/
	/* Данные для диалога выбора раздела */
	/*-----------------------------------*/
	
	var PROGRAMS			= [];
	PROGRAMS['direction']	= [<?php $delimiter = ''; foreach ($directions as $i => $direction): echo $delimiter; ?>{'id':<?php echo $direction['program_id']; ?>,'title':'<?php echo $direction["title"]; $delimiter = ","; ?>'}<?php endforeach; ?>];	
	PROGRAMS['course'] 		= [<?php $delimiter = ''; foreach ($courses as $i => $course): echo $delimiter; ?>{'id':<?php echo $course['program_id']; ?>,'title':'<?php echo $course["title"]; $delimiter = ","; ?>'}<?php endforeach; ?>];

	var DISCIPLINES = [];
	<?php foreach ($disciplines as $i => $specialityDiscipline): ?>
	DISCIPLINES[<?php echo $specialityDiscipline[0]['program_id']; ?>] = [<?php $delimiter = ""; foreach ($specialityDiscipline as $j => $discipline): echo $delimiter ?>{"id":<?php echo $discipline['discipline_id']; ?>,"title":"<?php echo $discipline['title']; ?>"}<?php ; $delimiter =","; endforeach; ?>];
	<?php endforeach; ?>

	var SECTIONS 	= [];
	<?php foreach ($sections as $i => $disciplineSection): ?>
	SECTIONS[<?php echo $disciplineSection[0]['discipline_id']; ?>] = [<?php $delimiter = ""; foreach ($disciplineSection as $j => $section): echo $delimiter ?>{"id":<?php echo $section['section_id']; ?>,"title":"<?php echo $section['title']; ?>"}<?php ; $delimiter =","; endforeach; ?>];
	<?php endforeach; ?>
	
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
	
	/*-----------------------------------------------*/
	/* "Класс" для формы загрузки учебного материала */
	/*-----------------------------------------------*/

	__EducationalMaterial = [];
	
	function EducationalMaterial () {
		this.id 						= __EducationalMaterial.length;
		__EducationalMaterial[this.id] 	= this;
	}
		
	EducationalMaterial.prototype.create = function () {
		var description			= document.createElement ('input');
		description.type		= 'text';
		description.name		= 'description[' + this.id + ']';
		
		var fileReference		= document.createElement ('input');
		fileReference.type		= 'file';
		fileReference.name		= 'fileReference[' + this.id + ']';
		
		var section				= document.createElement ('input');
		section.type			= 'hidden';
		section.name			= 'section[' + this.id + ']';
		
		var sectionDescription	= document.createElement ('input');
		sectionDescription.type	= 'hidden';
		sectionDescription.name	= 'sectionDescription[' + this.id + ']';
		
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
							section
						).parentNode.appendChild (
							sectionDescription
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
		
		var lastEducationalMaterial = $ ('#educationalMaterials div:last-child');
		
		$ ('tr:eq(2) td:last-child a'	, lastEducationalMaterial).attr		('href', 'javascript:__EducationalMaterial[' + this.id + '].openSelectSectionDialog()');
		$ ('tr:lt(3) td:first-child'	, lastEducationalMaterial).addClass ('caption');
		$ ('tr:lt(3) td:last-child'		, lastEducationalMaterial).addClass ('field');
		$ ('tr:eq(3) td'				, lastEducationalMaterial).attr		('colspan', '2');
		$ ('tr:eq(3) td'				, lastEducationalMaterial).addClass	('cancel');
		$ ('tr:eq(3) td a'				, lastEducationalMaterial).attr		('href', 'javascript:__EducationalMaterial[' + this.id + '].destroy()');
	}
	
	EducationalMaterial.prototype.openSelectSectionDialog = function () {
		$ ('#sectionSelectDialog').dialog ('open');
	}
	
	EducationalMaterial.prototype.destroy = function () {
		this.container.parentNode.removeChild (this.container);
	}
	
	function createEducationalMaterial () {
		(new EducationalMaterial ()).create ();		
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
<form id="educationalMaterials" name="educationalMaterials" method="post" action="/upload_materials" enctype="multipart/form-data">
</form>
<a href="javascript:createEducationalMaterial()">добавить материал</a>
<a href="javascript:document.educationalMaterials.submit()">загрузить</a>

<script type="text/javascript">
	var programsTypeSelect	= $ ('#programsTypeSelect');
	var programsSelect 		= $ ('#programsSelect');
	var disciplinesSelect 	= $ ('#disciplinesSelect');
	var sectionsSelect 		= $ ('#sectionsSelect');

	$ ('#sectionSelectDialog').dialog (
		{
			autoOpen	: false,
			buttons		: {
				'Выбрать': function () {$ ('#sectionSelectDialog').dialog ('close');}
			},
			draggable 	: false,
			modal		: true,
			resizable	: false,
			title 		: 'Выбор раздела',
			width 		: 'auto'
		}
	);
	createEducationalMaterial ();
</script>