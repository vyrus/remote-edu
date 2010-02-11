<?php
	$teachers = $this->teachers;
	$disciplines = $this->disciplines;
	$courses = $this->courses;
?>
<script type="text/javascript">
	var teachers = {
<?php foreach ($teachers as $i => $teacher): ?>
		'<?php echo $i; ?>':{'name':'<?php echo $teacher['name']; ?>', 'surname':'<?php echo $teacher['surname']; ?>', 'patronymic':'<?php echo $teacher['patronymic']; ?>'},
<?php endforeach; ?>
	};
	var disciplines = {
<?php foreach ($disciplines as $i => $discipline): ?>
		'<?php echo $discipline['discipline_id']; ?>':{'title':'<?php echo $discipline['title']; ?>','responsibleTeacher':<?php echo ($discipline['responsible_teacher'] !== NULL ? $discipline['responsible_teacher'] : 'null'); ?>,'changed':false},
<?php endforeach; ?>
	};
	var courses = {
<?php foreach ($courses as $i => $course): ?>
		'<?php echo $course['program_id']; ?>':{'title':'<?php echo $course['title']; ?>','responsibleTeacher':<?php echo ($course['responsible_teacher'] !== NULL ? $course['responsible_teacher'] : 'null'); ?>,'changed':false},
<?php endforeach; ?>
	};
	
	function getRespobsibleSelectItem(id) {
		return (id.search('discipline') != -1 ? disciplines[id.substr(10)] : courses[id.substr(6)]);
	}
	
	function assignResponsibleTeacher() {
		if (teachersSelect.attr('selectedIndex') == -1) {
			alert ('Необходимо выбрать преподавателя');
			return;
		}
		
		var respobsibleSelectItem = getRespobsibleSelectItem(responsibleTeachersSelect.val());
		selectTeacherDialog.dialog('close');
		respobsibleSelectItem.responsibleTeacher = teachersSelect.val();
		respobsibleSelectItem.changed = true;
		updateResponsibleTeachersSelect();
	}
	
	function showSelectTeacherDialog() {
		var selectedItem = responsibleTeachersSelect.attr('selectedIndex');
		if (selectedItem == -1) {
			return;
		}
		
		selectTeacherDialog.dialog(
			'option',
			'buttons',
			{
				'Выбрать': assignResponsibleTeacher,
			}
		);
		teachersSelect.attr('selectedIndex', -1);
		selectTeacherDialog.dialog('open');
	}
	
	function fillTeachersSelect() {
		$.each(teachers, function (key, value) {teachersSelect.append(new Option(
			value.surname + ' ' + value.name + ' ' + value.patronymic, key))});
	}
	
	function updateResponsibleTeachersSelect() {
		responsibleTeachersSelect.empty();
		
		var listContent = [];
		
		switch (filterSelect.val()) {
		case 'courses':
			$.each(courses, function (key, value) {responsibleTeachersSelect.append(
				new Option(value.title + '(' + (value.responsibleTeacher === null ?
				'ответственный не назначен' : teachers[value.responsibleTeacher].surname + ' ' +
				teachers[value.responsibleTeacher].name.substr(0, 1) + '.' +
				teachers[value.responsibleTeacher].patronymic.substr(0, 1)) + ')', 'course' + key))});				
			break;
		case 'disciplines':
			$.each(disciplines, function (key, value) {responsibleTeachersSelect.append(
				new Option(value.title + '(' + (value.responsibleTeacher === null ?
				'ответственный не назначен' : teachers[value.responsibleTeacher].surname + ' ' +
				teachers[value.responsibleTeacher].name.substr(0, 1) + '.' +
				teachers[value.responsibleTeacher].patronymic.substr(0, 1)) + ')', 'discipline' + key))});				
			break;
		default:
			$.each(disciplines, function (key, value) {responsibleTeachersSelect.append(
				new Option(value.title + '(' + (value.responsibleTeacher === null ?
				'ответственный не назначен' : teachers[value.responsibleTeacher].surname + ' ' +
				teachers[value.responsibleTeacher].name.substr(0, 1) + '.' +
				teachers[value.responsibleTeacher].patronymic.substr(0, 1)) + ')', 'discipline' + key))});
			$.each(courses, function (key, value) {responsibleTeachersSelect.append(
				new Option(value.title + '(' + (value.responsibleTeacher === null ?
				'ответственный не назначен' : teachers[value.responsibleTeacher].surname + ' ' +
				teachers[value.responsibleTeacher].name.substr(0, 1) + '.' +
				teachers[value.responsibleTeacher].patronymic.substr(0, 1)) + ')', 'course' + key))});				
			break;
		}		
	}
	
	function changeButtonCaption() {
		var respobsibleSelectItem = getRespobsibleSelectItem(responsibleTeachersSelect.val());
		
		assignButton.text((respobsibleSelectItem.responsibleTeacher === null ? 'назначить' : 'переназначить') +  ' ответсвенного');
	}
	
	function submitChanges() {
		var form = $('#changesForm');
		var hiddens = '';
		$.each(disciplines, function (key, value) { if (value.changed) {hiddens += '<input type="hidden" name="disciplines[' + key + ']" value="' + value.responsibleTeacher + '" />';} });
		$.each(courses, function (key, value) { if (value.changed) {hiddens += '<input type="hidden" name="courses[' + key + ']" value="' + value.responsibleTeacher + '" />';} });
		
		if (hiddens.length) {
			form.append(hiddens);
			form.submit();
		}
		else {
			alert('Не внесено никаких изменений');
		}
	}
</script>
<h3>Ответсвенные по дисциплинам/курсам</h3>
Отображать<select id="filter" onchange="updateResponsibleTeachersSelect();"><option value="courses">Курсы</option><option value="disciplines">Дисциплины</option><option value="all" selected="selected">Все</option></select><br />
<select id="responsibleTeachers" size="10" onchange="changeButtonCaption()" style="min-width: 200px;"></select>
<br /><a id="assignButton" href="javascript:showSelectTeacherDialog()">назначить ответсвенного</a>&nbsp;<a href="javascript:submitChanges()">сохранить изменения</a>
<div id="selectTeacherDialog"><select id="teachersSelect" size="10"></select></div>
<form id="changesForm" action="/assignment/responsible_teacher" method="post"></form>
<script type="text/javascript">
	var responsibleTeachersSelect = $('#responsibleTeachers');
	var teachersSelect = $('#teachersSelect');
	var filterSelect = $('#filter');
	var selectTeacherDialog	= $('#selectTeacherDialog');
	var assignButton = $('#assignButton');
	
	selectTeacherDialog.dialog(
		{
			autoOpen: false,
			draggable : false,
			modal: true,
			resizable: false,
			title: 'Выбор ответсвенного',
			width: 'auto'
		}
	);
	fillTeachersSelect();
	updateResponsibleTeachersSelect();
</script>