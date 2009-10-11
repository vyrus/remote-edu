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
	__EducationalMaterial = [];
	
	function EducationalMaterial () {
		this.id 						= __EducationalMaterial.length;
		__EducationalMaterial[this.id] 	= this;
	}
	
	function openSectionSelectDialog () {
		
	}
		
	EducationalMaterial.prototype.create = function () {
		var description		= document.createElement ('input');
		description.type	= 'text';
		description.name	= 'description[' + this.id + ']';
		
		var fileReference	= document.createElement ('input');
		fileReference.type	= 'file';
		fileReference.name	= 'fileReference[' + this.id + ']';
		
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
							document.createTextNode ('Выбрать раздел')
						).parentNode
					).parentNode.appendChild (
						document.createElement ('td').appendChild (
							document.createElement ('a').appendChild (
								document.createTextNode ('выбрать')
							).parentNode
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
		
		$ ('#educationalMaterials div:last-child tr:eq(2) td:last-child a').attr ('href', "javascript:$('#sectionSelectDialog').dialog('open')");
		$ ('#educationalMaterials div:last-child tr:lt(3) td:first-child').addClass ('caption');
		$ ('#educationalMaterials div:last-child tr:lt(3) td:last-child').addClass ('field');
		$ ('#educationalMaterials div:last-child tr:eq(3) td').attr ('colspan', '2');
		$ ('#educationalMaterials div:last-child tr:eq(3) td').addClass ('cancel');
		$ ('#educationalMaterials div:last-child tr:eq(3) td a').attr ('href', 'javascript:__EducationalMaterial[' + this.id + '].destroy()');
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
	<td><select id="programsSelect" class="educationProgramItems" size="10" onchange="updateDisciplinesList ();">
	</select></td>
	<td><select id="disciplinesSelect" class="educationProgramItems" size="10" onchange="updateSectionsList ();"></select></td>
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
	$ ('#sectionSelectDialog').dialog (
		{
			autoOpen	: false,
			modal		: true,
			resizable	: false,
			title 		: 'Выбор раздела',
			width 		: 'auto',
			draggable 	: false,
			buttons		: {
				'Выбрать': function () {$ ('#sectionSelectDialog').dialog ('close');}
			}
		}
	);
	createEducationalMaterial ();
</script>