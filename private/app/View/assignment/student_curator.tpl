<link type="text/css" href="/css/ui-lightness/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.7.2.custom.min.js"></script>

<?php
	$teachers = $this->teachers;
	$students = $this->students;
?>
<script type="text/javascript">
	var teachers = {
<?php foreach ($teachers as $i => $teacher): ?>
		'<?php echo $i; ?>':{'name':'<?php echo $teacher['name']; ?>', 'surname':'<?php echo $teacher['surname']; ?>', 'patronymic':'<?php echo $teacher['patronymic']; ?>'},
<?php endforeach; ?>
	};
	var students = {
<?php foreach ($students as $i => $student): ?>
		'<?php echo $i; ?>':{'name':'<?php echo $student['name']; ?>', 'surname':'<?php echo $student['surname']; ?>', 'patronymic':'<?php echo $student['patronymic']; ?>', 'curator':<?php echo ($student['curator'] !== NULL ? $student['curator'] : 'null'); ?>,'changed':false},
<?php endforeach; ?>		
	}

	function getStudentsSelectItem(id) {
		return students[id];
	}
	
	function filterStudentsList() {
		var filterVal = filter.val().toLowerCase();
		var retval = {};

		if (! filterVal.length) {
			return students;
		}
		$.each(students,
			function (key, value) {
				if (value.name.toLowerCase().search(filterVal) != -1 ||
						value.surname.toLowerCase().search(filterVal) != -1 ||
						value.patronymic.toLowerCase().search(filterVal) != -1) {
					retval[key] = value;
				}
			}
		);
		
		return retval;
	}

	function assignCurator() {
		if (teachersSelect.attr('selectedIndex') == -1) {
			alert ('Необходимо выбрать преподавателя');
			return;
		}
		
		var studentsSelectItem = getStudentsSelectItem(studentsSelect.val());
		selectTeacherDialog.dialog('close');
		studentsSelectItem.curator = teachersSelect.val();
		studentsSelectItem.changed = true;
		updateStudentsSelect();
	}

	function showSelectTeacherDialog() {
		var selectedItem = studentsSelect.attr('selectedIndex');
		if (selectedItem == -1) {
			return;
		}
		
		selectTeacherDialog.dialog(
			'option',
			'buttons',
			{
				'Выбрать': assignCurator,
			}
		);
		teachersSelect.attr('selectedIndex', -1);
		selectTeacherDialog.dialog('open');
	}

	function fillTeachersSelect() {
		$.each(teachers, function (key, value) {teachersSelect.append(new Option(
			value.surname + ' ' + value.name + ' ' + value.patronymic, key))});
	}
	
	function updateStudentsSelect() {
		var studentsList;
		
		if (arguments.length) {
			studentsList = arguments[0];
		}
		else {
			studentsList = students;
		}
		studentsSelect.empty();
		$.each(studentsList, function (key, value) { studentsSelect.append(new Option(
			value.surname + ' ' + value.name + ' ' + value.patronymic + '(' + 
			(value.curator === null ? 'куратор не назначен' :
			teachers[value.curator].surname + ' ' + teachers[value.curator].name.substr(0, 1)
			+ '.' + teachers[value.curator].patronymic.substr(0, 1)) +  ')', key)); });
	}

	function changeButtonCaption() {
		var studentsSelectItem = getStudentsSelectItem(studentsSelect.val());		
		assignButton.text((studentsSelectItem.curator === null ? 'назначить' : 'переназначить') +  ' куратора');
	}
	
	function filterFieldKeyup() {
		updateStudentsSelect(filterStudentsList());
	}
	
	function submitChanges() {
		var form = $('#changesForm');
		var hiddens = '';
		$.each(students, function (key, value) { if (value.changed) {hiddens += '<input type="hidden" name="' + key + '" value="' + value.curator + '" />';} });
		
		if (hiddens.length) {
			form.append(hiddens);
			form.submit();
		}
		else {
			alert('Не внесено никаких изменений');
		}
	}	
</script>
<h3>Кураторы студентов</h3>
Поиск: <input id="filter" type="text" onkeyup="filterFieldKeyup()" /><br />
<select id="students" size="10" onchange="changeButtonCaption()" style="min-width: 200px;"></select>
<div id="selectTeacherDialog"><select id="teachersSelect" size="10"></select></div>
<br /><a id="assignButton" href="javascript:showSelectTeacherDialog()">назначить куратора</a>&nbsp;<a href="javascript:submitChanges()">сохранить изменения</a>
<form id="changesForm" action="/assignment/students_curator" method="post"></form>
<script type="text/javascript">
	var studentsSelect = $('#students');
	var selectTeacherDialog	= $('#selectTeacherDialog');
	var teachersSelect = $('#teachersSelect');
	var filter = $('#filter');
	var assignButton = $('#assignButton');
	
	selectTeacherDialog.dialog(
		{
			autoOpen: false,
			draggable : false,
			modal: true,
			resizable: false,
			title: 'Выбор куратора',
			width: 'auto'
		}
	);
	fillTeachersSelect();	
	updateStudentsSelect();
</script>