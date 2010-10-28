/**
* Создание нового "класса".
*
* @link http://dklab.ru/chicken/nablas/40.html
*/
function newClass(child, parent) {
    var new_class = function() {
        /* Если идёт только объявление нового класса, */
        if (new_class.__defining_class__) {
            /* сохраняем родительский класс */
            this.__parent = new_class.prototype;
            /* И выходим */
            return;
        }

        /* Если задан метод-конструктор класса */
        if (new_class.__constructor__) {
            /* Устанавливаем конструктор объекта на функцию класса */
            this.constructor = new_class;
            /* И вызываем метод-конструктор */
            new_class.__constructor__.apply(this, arguments);
        }
    }

    /* Устанавливаем пустой объект прототипом нового класса */
    new_class.prototype = {};

    /* Если задан родительский класс, */
    if (parent) {
        /* устанавливаем флаг, обозначающий объвление класса */
        parent.__defining_class__ = true;
        /* Устанавливаем в качестве прототипа объект родительского класса */
        new_class.prototype = new parent();
        /* И снимаем флаг */
        delete parent.__defining_class__;

        /* Задаём оригинальный конструктор для родителя */
        new_class.prototype.constructor = parent;
        /* И устанавливаем в качестве метода-конструктора конструктор объекта */
        new_class.__constructor__ = parent;
    }

    /* Название метода-конструктор класса */
    var constr_name = '__construct';

    /* Если задан объект со свойствами нового класса */
    if (child)
    {
        /* Копируем свойства в сам класс */
        for (var prop in child) {
            new_class.prototype[prop] = child[prop];
        }

        /* Если задан метод-конструктор, */
        if (child[constr_name] && Object != child[constr_name]) {
            /* запоминаем его */
            new_class.__constructor__ = child[constr_name];
        }
    }

    return new_class;
}

var ajex_fm_active_input;

/**
* Прототип объекта для создания тестов.
*/
Test = {
    /**
    * Идентификатор теста.
    */
    _id: null,

    /**
    * Список вопросов, сохранённых в базе (при редактировании теста).
    */
    _questions: {},

    /**
    * Список новых введённых запросов (при редактировании теста).
    */
    _new_questions: {},

    /**
    * Список выводимых слушателю вопросов (при сдачи теста).
    */
    _exam_questions: {},

    /**
    * Последнее значение временного идентификатора для новых вопросов.
    */
    _last_tmp_id: -1,

    /**
    * Карта типов вопросов в factory function для создания объектов вопросов.
    */
    _types_map: {
        'pick-one': function() { return new Question_PickOne(); }
    },

    /**
    * Объект представления для работы с формой опций теста.
    */
    _view: null,

    /**
    * Конструктор класса.
    *
    * @param  string optsFormId Идентификатор формы опций на странице.
    * @return void
    */
    __construct: function(optsFormId) {
        /* Инициализируем объект представления для формы опций теста */
        this._view = new View_Test_Options(optsFormId);
    },

    /**
    * Проверяет, установлен ли идентификатор теста.
    *
    * @return boolean
    */
    isIdSet: function() {
        return (null !== this._id);
    },

    /**
    * Установка идентификатора теста.
    *
    * @param  int
    * @return void
    */
    setId: function(id) {
        this._id = id;
    },

    /**
    * Получение идентификатор теста.
    *
    * @return int
    */
    getId: function() {
        return this._id;
    },

    /**
    * Добавление в тест нового вопроса.
    *
    * @param  string type      Тип вопроса.
    * @param  object container Контейнер на странице, в который будет добавлена форма вопроса.
    * @return void
    */
    addQuestion: function(type, container) {
        /* Создаём объект вопроса */
        var q = new this._types_map[type]();
        /* Устанавливаем временный идентификатор */
        q.setTmpId(++this._last_tmp_id);
        /* Добавляем его форму на страницу */
        q.renderForm(container, undefined, true);

        /* И сохраняем в списке вопросов теста */
        this._new_questions[this._last_tmp_id] = q;
    },

    /**
    * Отображение ошибок в форме вопроса.
    *
    * @param  string q_category Категория вопроса: "new" - новые, ещё не сохранённые вопросы, "old" - старые.
    * @param  int    id         Идентификатор вопроса.
    * @param  object errors     Список ошибок.
    * @return void
    */
    showErrors: function(q_category, id, errors) {
        var questions;

        /* Выбираем в зависимости от категории список вопросов */
        if ('new' == q_category) {
            questions = this._new_questions;
        } else {
            questions = this._questions;
        }

        /* Находим объект вопроса и вызываем его метод для вывода ошибок */
        questions[id].showErrors(errors);
    },

    /**
    * Скрывает надписи ошибок.
    *
    * @return void
    */
    hideErrors: function() {
        /* Создаём функцию для обработки элементов списка */
        var hider = function(id, q) {
            /* Прячем ошибки в очередном вопросе */
            q.hideErrors();
        };

        /* Проходим по обоим спискам вопросов нашей функцией */
        $.each(this._new_questions, hider);
        $.each(this._questions, hider);
    },

    /**
    * Установка значений опций теста. Эти значения будут сохранены в полях ввода
    * формы опций.
    *
    * @param  object options Список значений вида {option: value}.
    * @return void
    */
    setOptions: function(options) {
        /* Перебираем переданный список */
        for (key in options) {
            /* Через отображение */
            this._view
                /* находим поле ввода по ключу опции */
                .getInput(key)
                    /* и вводим в него значение опции */
                    .val(options[key]);
        }
    },

    /**
    * Получение значений опций теста.
    *
    * @return object
    */
    getOptions: function() {
        var data = {},
            /* Задаём список ключей, по которым будем находть значения опций */
            opts = ['theme', 'num_questions', 'errors_limit', 'attempts_limit'],
            opt;

        /* Перебираем список ключей */
        for (var idx in opts) {
            /* Берём очередный ключ опции */
            opt = opts[idx];
            /* И запоминаем значение из соответствующего input'а */
            data[opt] = this._view.getInput(opt).val();
        }

        return data;
    },

    /**
    * Добавление сохранённых вопросов из базы в тест.
    *
    * @param  array  Список вопросов.
    * @param  object Контейнер, в которой добавлять формы вопросов.
    * @return void
    */
    setQuestions: function(questions, container) {
        var q, q_obj;

        /* Перебираем список вопросов */
        for (key in questions)
        {
            /* Берём данные очередного вопроса */
            q = questions[key];
            /* Создаём объект соответствующего типа вопроса */
            q_obj = this._types_map[q.type]();

            /* Сохраняем в объекте данные вопроса */
            q_obj.setData(q);
            /* Выводим форму вопроса на страницу */
            q_obj.renderForm(container);

            /* И запоминаем новый объект вопроса в списке */
            this._questions[q.question_id] = q_obj;
        }
    },

    /**
    * Получение данных всех вопросов теста.
    *
    * @return array
    */
    getQuestions: function() {
        var questions = [], q, key;

        /* Перебираем старые вопросы */
        for (key in this._questions)
        {
            /* Берём очередной вопрос */
            q = this._questions[key];
            /* Собираем его данные из формы */
            q.collectData();
            /* И сохраняем в итоговый список */
            questions.push(q.getData());
        }

        var q_data = {};

        /* Перебираем новые вопросы */
        for (key in this._new_questions)
        {
            /* Берём очередной объект вопроса */
            q = this._new_questions[key];

            /* Собираем его данные из формы и получаем их */
            q_data = q.collectData();
            q_data = q.getData();

            /* Удаляем атрибут идентификатора, он ещё не задан, так как вопрос
            не сохранён */
            delete q_data.question_id;
            /* Присваиваем временный идентификатор - ключ в списке вопросов */
            q_data.tmp_id = key;

            /* Заносим данные в итоговый список */
            questions.push(q_data);
        }

        return questions;
    },

    /**
    * Удаление вопроса из теста
    *
    * @param  string q_category Категория вопроса: "new" - новые вопросы, "old" - старые.
    * @param  int    id         Идентификатор вопроса.
    * @return void
    */
    deleteQuestion: function(q_category, id) {
        /* В зависимости от категории выбираем список вопросов */
        var questions = ('new' == q_category ?
                            this._new_questions :
                            this._questions);

        /* Удаляем форму вопроса */
        questions[id].deleteForm();
        /* И сам объект вопроса */
        delete questions[id];
    },

    /**
    * Установка настоящих идентификаторов для новых вопросов взамен временных.
    *
    * @param  array ids Список вида {tmp_id: new_id}.
    * @return void
    */
    setNewIds: function(ids) {
        /* Сохраняем указатели на списки вопросов в области видимости функции */
        var new_questions = this._new_questions,
            old_questions = this._questions;

        /* Задаём функцию для установки новых идентификаторов */
        var setter = function(tmp_id, new_id) {
            /* Находии нужный вопрос */
            var q = new_questions[tmp_id];
            /* Берём его данные */
            var data = q.getData();

            /* Устанавливаем идентификатор */
            data.question_id = new_id;
            q.setData(data);
            /* И удаляем временный идентификатор */
            q.deleteTmpId();

            /* Скрываем форму вопроса */
            q.hide();

            /* Переносим объект вопроса в список со старыми вопросами */
            old_questions[data.question_id] = q;
            /* И удаляем его из списка новых вопросов */
            delete new_questions[tmp_id];
        };

        /* Обрабатываем список идентификаторов нашей функцией */
        $.each(ids, setter);
    },

    /**
    * Установка вопросов для тестирования.
    *
    * @param  array questions Список вопросов.
    * @param  array container Контейнер на странице, в который будут добавляться вопросы.
    * @return void
    */
    setExamQuestions: function(questions, container) {
        /* Сохраняем ссылки на необходимые атрибуты объекта в локальной области
        видимости, чтобы к ним можно было обратиться из замыкания */
        var exam_questions = this._exam_questions;
        var factory = this._types_map;

        /* Перебираем список вопросов */
        $.each(questions, function(q_id, q_data) {
            /* Создаём объект вопроса нужного типа */
            var q_obj = factory[q_data.type]();

            /* Устанавливаем идентификатор вопроса */
            q_data.question_id = q_id;
            /* И запоминаем в объекте данные вопроса */
            q_obj.setExamData(q_data);

            /* Выводим на страницу фому с вопросом */
            q_obj.renderExamForm(container);

            /* И сохраняем объект вопроса в списке вопросов тестирования */
            exam_questions[q_id] = q_obj;
        });
    },

    getExamAnswers: function() {
        var answers = {};

        $.each(this._exam_questions, function(id, q) {
            answers[id] = q.getAnswer();
        });

        return answers;
    },

    disableRadios: function() {
        $.each(this._exam_questions, function(idx, q) {
            q.disableRadios();
        });
    },

    displayCorrectness: function(results) {
        var questions = this._exam_questions;

        var create_func = function(correctness) {
            return function(idx, q_id) {
                questions[q_id].setCorrectness(correctness);
            }
        }

        $.each(results.correct,    create_func(true));
        $.each(results.incorrect,  create_func(false));
        $.each(results.unanswered, create_func(false));
    },

    showAll: function() {
         var show = function(id, q) {
            q.show();
        };

        this._eachQuestion(show);
    },

    hideAll: function() {
         var hide = function(id, q) {
            q.hide();
        };

        this._eachQuestion(hide);
    },

    toggleAll: function() {
        var toggle = function(id, q) {
            q.toggle();
        };

        this._eachQuestion(toggle);
    },

    _eachQuestion: function(func) {
        $.each(this._new_questions, func);
        $.each(this._questions, func);
    }
}
Test = newClass(Test);

/**
* Прототип объекта вопроса с одним правильным ответом.
*/
Question_PickOne = {
    /**
    * Тип вопроса.
    */
    _type: 'pick-one',

    /**
    * Количество возможных ответов на вопрос.
    */
    _num_answers: 4,

    /**
    * Объект с текстом вопроса.
    */
    _question_input: null,

    _id_input: null,

    /**
    * Radio-button'ы и поля для ввода ответов.
    */
    _answer_inputs: [],

    _exam_answer_inputs: [],

    _data: null,

    _tmp_id: null,

    _view: null,

    __construct: function() {
        this._num_answers = 4;
        this._text_input = null;
        this._id_input = null;
        this._answer_inputs = [];
        this._error_targets = {};
        this._data = null;
    },

    setData: function(data) {
        this._data = {
            question_id:    data.question_id,
            type:           this._type,
            question:       data.question,
            answers:        data.answers,
            correct_answer: data.correct_answer
        };
    },

    setTmpId: function(id) {
        this._tmp_id = id;
    },

    getTmpId: function() {
        return this._tmp_id;
    },

    deleteTmpId: function() {
        this._tmp_id = null;
    },

    /**
    * Выборка данных вопроса из формы и сохранение их в одном массиве.
    */
    getData: function() {
        return $.extend({}, this._data);
    },

    issetData: function() {
        return (null !== this._data);
    },

    collectData: function() {
        var data = {};

        var id_input       = this._view.getIdInput(),
            question_input = this._view.getQuestionInput(),
            answer_inputs  = this._view.getAnswerInputs();

        data.question_id    = id_input.val();
        data.question       = question_input.val();
        data.answers        = [];
        data.correct_answer = null;

        if (this._view.isInputActive(question_input)) {
            data.question = question_input.val();
        } else {
            data.question = '';
        }

        var radio, answer, value, view = this._view;

        $.each(answer_inputs, function(idx, pair)
        {
            if ($(pair.radio).attr('checked')) {
                data.correct_answer = idx;
            }

            if (view.isInputActive($(pair.text))) {
                data.answers.push($(pair.text).val());
            }
        });

        this.setData(data);
    },

    setExamData: function(data) {
        this._data = {
            question_id: data.question_id,
            type:        this._type,
            question:    data.question,
            answers:     data.answers
        };
    },

    renderForm: function(container, q_data, new_question) {
        if (undefined === q_data && this.issetData()) {
            q_data = this.getData();
        }

        var view = new View_Question_PickOne_Edit();
        var html = view.render(this, {
            num_answers: this._num_answers,
            q:           q_data
        });

        this._view = view;

        if (true == new_question) {
            html.hide();
        }

        //alert(html.html());
        container.append(html);

        if (true == new_question) {
            html.show('fast');
            $.scrollTo(html, 200);
        }

        view.onAppend();
    },

    deleteForm: function() {
        this._view.destroy();
    },

    renderExamForm: function(container, q_data) {
        if (undefined === q_data && this.issetData()) {
            q_data = this.getData();
        }

        var view = new View_Question_PickOne_Show();
        var html = view.render({
            id:      q_data.question_id,
            text:    q_data.question,
            answers: q_data.answers
        });

        this._view = view;

        //alert(html.html());
        container.append(html);
    },

    getAnswer: function() {
        var selected_idx = null;

        $.each(this._view.getRadios(), function(idx, radio) {
            if ($(radio).attr('checked')) {
                selected_idx = idx;
            }
        })

        return selected_idx;
    },

    showErrors: function(errors) {
        var target;

        for (target in errors) {
            this._view.showError(target, errors[target]);
        }
    },

    hideErrors: function() {
        this._view.hideErrors();
    },

    disableRadios: function() {
        this._view.disableRadios();
    },

    setCorrectness: function(correctness) {
        this._view.setCorrectness(correctness);
    },

    show: function() {
        this._view.show();
    },

    hide: function() {
        this._view.hide();
    },

    toggle: function() {
        this._view.toggle();
    }
}
Question_PickOne = newClass(Question_PickOne);

View = {
    render: function(tpl, data) {
        return $.nano(tpl, data);
    }
};
View = newClass(View);

View_Test_Options = {
    _inputs_map: {
        theme:          'theme',
        num_questions:  'num_questions',
        errors_limit:   'errors_limit',
        attempts_limit: 'attempts_limit'
    },

    _html: null,

    __construct: function(formId) {
        this._html = $(formId);
    },

    getInput: function(alias) {
        return $('#' + this._inputs_map[alias], this._html);
    }
};
View_Test_Options = newClass(View_Test_Options, View);

View_Question_PickOne = {
    _classes: {
        inactiveInput: 'inactive',
        errorTarget:   'e-target'
    },

    _error_targets: {},

    __construct: function() {
        this._error_targets = {};
    },

    isInputActive: function(input) {
        return !input.hasClass(this._classes.inactiveInput);
    },

    showError: function(target, msg) {
        $(this._error_targets[target])
            .text(msg)
            .show();
    },

    hideErrors: function() {
        $.each(this._error_targets, function(idx, elem) {
            $(elem).hide();
        });
    }
};
View_Question_PickOne = newClass(View_Question_PickOne, View);

View_Question_PickOne_Edit = {
    _classes: {
        form:            'question-form',
        id:              'question-id',
        question:        'question-text',
        radio:           'question-radio',
        answer:          'question-answer',
        answer_cntr:     'answer-container',
        error_target_td: 'error-target',

        wrapper:         'question-wrapper',
        collapsed:       'question-collapsed',
        expanded:        'question-expanded',
        lnk_toggle:      'lnk-toggle-question',
        lnk_delete:      'lnk-delete-question',
        lnk_add_img:     'lnk-add-img'
    },

    _tpl: {
        question: '<div class="{cls.wrapper}">' +
                       '<a href="" class="{cls.lnk_toggle}">Скрыть</a>&nbsp;' +
                       '<a href="" class="{cls.lnk_delete}">Удалить</a>&nbsp;' +
                       '<a href="" class="{cls.lnk_add_img}">Вставить изображение</a>' +

                       '<div class="{cls.expanded}">' +
                           '<form class="{cls.form}">' +
                               '<textarea class="{cls.question}">{q.question}</textarea>' +
                               '<span class="{cls.errorTarget}"></span>' +
                               '<input type="hidden" class="{cls.id}" value="{q.question_id}" />' +
                               '{answers}' +
                           '</form>' +
                       '</div>' +

                       '<div class="{cls.collapsed}" style="display: none;"></div>' +
                   '</div>',

        answer: '<table class="{cls.answer_cntr}">' +
                    '<tr>' +
                        '<td>' +
                            '<input type="radio" class="{cls.radio}" name="correct_answer" {checked}/>' +
                        '</td>' +

                        '<td>' +
                            '<textarea class="{cls.answer}">{answer}</textarea>' +
                        '</td>' +

                        '<td class="{cls.error_target_td}">' +
                            '<span class="{cls.errorTarget}"></span>' +
                        '</td>' +
                    '</tr>' +
                '</table>'
    },

    _html: null,

    _collapsed: false,

    _q_obj: null,

    __construct: function() {
        this.__parent.__construct.call(this);
        $.extend(this._classes, this.__parent._classes);
    },

    _get: function(elem_class) {
        return $('.' + this._classes[elem_class], this._html);
    },

    getIdInput: function() {
        return this._get('id');
    },

    getQuestionInput: function() {
        return this._get('question');
    },

    getAnswerInputs: function() {
        var radios = this._get('radio');
        var text_fields = this._get('answer');

        var pairs = [];
        radios.each(function(idx, radio) {
            pairs.push({
                radio: radio,
                text:  text_fields.get(idx)
            });
        });

        return pairs;
    },

    getToggleLink: function() {
        return this._get('lnk_toggle');
    },

    getDeleteLink: function() {
        return this._get('lnk_delete');
    },


    getAddImgLink: function() {
        return this._get('lnk_add_img');
    },

    getExpandedDiv: function() {
        return this._get('expanded');
    },

    getCollapsedDiv: function() {
        return this._get('collapsed');
    },

    render: function(q_obj, data) {
        var answers = '', checked;
        this._q_obj = q_obj;

        for (var i = 0; i < data.num_answers; i++) {
            checked = false;
            if (undefined != data.q) {
                if (i == data.q.correct_answer) {
                    checked = true;
                }
            }

            answers += this.__parent.render(this._tpl.answer, {
                cls:     this._classes,
                answer:  (undefined != data.q ? data.q.answers[i] : ''),
                checked: (checked ? 'checked ' : '')
            });
        }

        data.cls     = this._classes;
        data.answers = answers;

        if (undefined == data.q) {
            data.q = {
                question_id: '',
                question:    ''
            };
        }

        var html = this.__parent.render(this._tpl.question, data);
        this._html = $(html);

        if (undefined == data.q.answers)
        {
            var hinter = this._createInputHinter(), pairs, q_input,
                empty_inputs = [];

            pairs = this.getAnswerInputs();

            $.each(pairs, function(idx, pair) {
                $(pair.text).data('hint', 'Ответ ' + (idx + 1));
                empty_inputs.push(pair.text);
            });

            q_input = this.getQuestionInput().data('hint', 'Вопрос');

            empty_inputs.push(q_input);
            $.each(empty_inputs, hinter);
        }
        else
        {
            var error_targets = this._error_targets;
            var answer_targets = $('td .' + this._classes.errorTarget,
                                   this._html);

            $.each(answer_targets, function(idx, target) {
                error_targets['answer_' + idx] = target;
            });
        }

        this._error_targets.question =
            $('.' + this._classes.errorTarget, this._html).get(0);

        var v = this;
        $(this.getToggleLink()).click(function() { return v.toggle.apply(v); });
        $(this.getDeleteLink()).click(function() { return v.onDelete.apply(v); });
        $(this.getAddImgLink()).click(function(e) {
            ajex_fm_active_input = v.getQuestionInput();
            AjexFileManager.open({ returnTo: 'ajex_fm_insert' });
            return false;
        });

        return this._html;
    },

    destroy: function() {
        this._html.hide('fast', function() { $(this).remove(); });
    },

    onDelete: function(question_id) {
        var tmp_id = this._q_obj.getTmpId();

        if (null != tmp_id) {
            deleteQuestion('new', tmp_id);
        }
        else {
            var data = this._q_obj.getData();
            deleteQuestion('old', data.question_id);
        }

        return false;
    },

    show: function() {
        $(this.getExpandedDiv()).show('fast');
        $(this.getCollapsedDiv()).hide('fast');
        $(this.getToggleLink()).text('Скрыть');

        this._collapsed = false;
    },

    hide: function() {
        var question = $(this.getQuestionInput())
                         .val()
                         .substr(0, 100)
                         .replace(/</g, '&lt;')
                         .replace(/>/g, '&gt;');

        $(this.getExpandedDiv()).hide('fast');
        $(this.getCollapsedDiv()).html(question).show('fast');
        $(this.getToggleLink()).text('Показать');

        this._collapsed = true;
    },

    toggle: function() {
        this._collapsed ? this.show() : this.hide();
        return false;
    },

    onAppend: function() {
        var question = $(this.getQuestionInput());

        question.resizable({
            handles: 'se',
            minHeight: question.outerHeight(),
            minWidth: question.outerWidth()
        });

        $.each(this.getAnswerInputs(), function (idx, pair){
            var answer = $(pair.text);

            answer.resizable({
                handles: 'se',
                minHeight: answer.outerHeight(),
                minWidth: answer.outerWidth()
            });
        });
    },

    /**
    * Создание функции для установки подсказок в поля ввода. В качестве текста
    * подсказки используется текущее значение поля.
    */
    _createInputHinter: function() {
        /**
        * Заносим в переменную из области видимости функции класс неактивного
        * input'а, чтобы он был доступен внутри замыкания.
        */
        var class_inactive = this._classes.inactiveInput;

        var hinter = function() {
            $(this).val($(this).data('hint'));
            $(this)
                //.data('default', $(this).val())
                .addClass(class_inactive)

                .focus(function() {
                    if (
                        $(this).val() == $(this).data('hint') &&
                        $(this).hasClass(class_inactive)
                    ) {
                        $(this).val('');
                        $(this).removeClass(class_inactive);
                    }
                })

                .blur(function() {
                    var default_val = $(this).data('hint');
                    if ($(this).val() == '') {
                        $(this).addClass(class_inactive);
                        $(this).val($(this).data('hint'));
                    }
                });
        };

        return hinter;
    }
};
View_Question_PickOne_Edit = newClass(View_Question_PickOne_Edit,
                                      View_Question_PickOne);

View_Question_PickOne_Show = {
    _classes: {
        container:   'exam-container',
        form:        'exam-form',
        question:    'exam-question',
        answer_cntr: 'exam-answer-container',
        radio_td:    'exam-radio-td',
        radio:       'exam-radio',
        label_td:    'exam-label-td',
        correctness: 'exam-correctness'
    },

    _tpl: {
        question: '<li class="{cls.container}">' +
                      '<form class="{cls.form}">' +
                          '<div class="{cls.question}">{text}</div>' +
                              '<span class="{cls.errorTarget}"></span>' +
                              '{answers}' +
                      '</form>' +
                  '</li>',

        answer:  '<table class="{cls.answer_cntr}">' +
                    '<tr>' +
                        '<td class="{cls.radio_td}">' +
                            '<input type="radio" name="correct_answer" id="{id}" class="{cls.radio}" />' +
                        '</td>' +

                        '<td class="{cls.label_td}">' +
                            '<label for="{id}">{answer}</label>' +
                        '</td>' +
                    '</tr>' +
                '</table>'
    },

    _html: null,

    __construct: function() {
        this.__parent.__construct.call(this);
        $.extend(this._classes, this.__parent._classes);
    },

    render: function(data) {
        var answers = '';

        for (idx in data.answers) {
            answers += this.__parent.render(this._tpl.answer, {
                cls:    this._classes,
                answer: data.answers[idx],
                id:     'answer_' + data.id + '_' + idx
            });
        }

        data.cls     = this._classes;
        data.answers = answers;

        var html = this.__parent.render(this._tpl.question, data);

        this._html = $(html);
        return this._html;
    },

    getRadios: function() {
        return $('.' + this._classes.radio, this._html);
    },

    getContainer: function() {
        return this._html;
    },

    disableRadios: function() {
        this.getRadios().each(function(idx, radio) {
            $(radio).attr('disabled', 'disabled');
        });
    },

    setCorrectness: function(correctness) {
        var img = (correctness ? 'plus' : 'minus');
        this.getContainer().css('list-style-image',
                                'url(/images/' + img  + '.png)');
    }
};
View_Question_PickOne_Show = newClass(View_Question_PickOne_Show,
                                      View_Question_PickOne);

function ajex_fm_insert(file_path) {
    code = '<img src="' + file_path + '" />';

    value = ajex_fm_active_input.text();
    value += code;
    ajex_fm_active_input.text(value);

    ajex_fm_active_input = null;
}