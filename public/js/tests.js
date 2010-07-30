var Test = function() {
    this._id = null;
    this._questions = {};
    this._new_questions = {};
    this._last_tmp_id = 0;
};

/**
* Прототип объекта для создания тестов. Управляет формами для внесения данных
* теста и вопросов и собирает из них данные в один итоговый объект.
*/
Test.prototype = {
    _id: null,

    _questions: {},

    _new_questions: {},

    _exam_questions: {},

    _last_tmp_id: 0,

    _inputs_map: {
        theme:          'theme',
        num_questions:  'num_questions',
        errors_limit:   'errors_limit',
        attempts_limit: 'attempts_limit'
    },

    _types_map: {
        'pick-one': function() { return new Question_PickOne(); }
    },

    isIdSet: function() {
        return (null !== this._id);
    },

    setId: function(id) {
        this._id = id;
    },

    getId: function() {
        return this._id;
    },

    _getInput: function(alias) {
        return $('#' + this._inputs_map[alias]);
    },

    /**
    * Добавление в тест нового вопроса с одним правильным ответом.
    */
    addQuestion: function(type, container) {
        /* Создаём новый вопрос и добавляем его форму на страницу */
        var q = new this._types_map[type]();
        q.renderForm(container);

        /* Сохраняем в списке вопросов теста */
        this._new_questions[this._last_tmp_id++] = q;
    },

    showErrors: function(q_category, key, errors) {
        var questions;

        if ('new' == q_category) {
            questions = this._new_questions;
        } else {
            questions = this._questions;
        }

        questions[key].showErrors(errors);
    },

    hideErrors: function() {
        var hider = function(idx, elem) {
            elem.hideErrors();
        };

        $.each(this._new_questions, hider);
        $.each(this._questions, hider);
    },

    setOptions: function(options) {
        for (key in options) {
            this._getInput(key).val(options[key]);
        }
    },

    getOptions: function() {
        /* Собираем параметры теста */
        var data = {
            theme:          this._getInput('theme').val(),
            num_questions:  this._getInput('num_questions').val(),
            errors_limit:   this._getInput('errors_limit').val(),
            attempts_limit: this._getInput('attempts_limit').val()
        };

        return data;
    },

    setQuestions: function(questions, container) {
        var q, q_obj;

        for (key in questions)
        {
            q = questions[key];
            q_obj = this._types_map[q.type]();

            q_obj.setData(q);
            q_obj.renderForm(container);

            this._questions[q.question_id] = q_obj;
        }
    },

    /**
    * Сохранение всех данных теста в одном объекте.
    */
    getQuestions: function() {
        var questions = [], q, key;

        /* Перебираем все вопросы в тесте */
        for (key in this._questions)
        {
            q = this._questions[key];
            /* Сохраняем данные очередного вопроса в массиве */
            q.collectData();
            questions.push(q.getData());
        }

        var q_data = {};

        for (key in this._new_questions)
        {
            q = this._new_questions[key];

            q_data = q.collectData();
            q_data = q.getData();

            delete q_data.question_id;
            q_data.tmp_id = key;

            questions.push(q_data)
        }

        return questions;
    },

    setNewIds: function(ids) {
        var new_questions = this._new_questions,
            old_questions = this._questions;

        var setter = function(idx, elem) {
            var q = new_questions[idx];
            var data = q.getData();

            data.question_id = elem;
            q.setData(data);

            old_questions[data.question_id] = q;
            delete new_questions[idx];
        };

        $.each(ids, setter);
    },

    setExamQuestions: function(questions, container) {
        var exam_questions = this._exam_questions;
        var factory = this._types_map;

        $.each(questions, function(idx, q_data) {
            var q_obj = factory[q_data.type]();

            q_obj.setExamData(q_data);
            q_obj.renderExamForm(container);

            exam_questions[q_data.question_id] = q_obj;
        });
    },

    getExamAnswers: function() {
        var answers = {};

        $.each(this._exam_questions, function(id, q) {
            answers[id] = q.getAnswer();
        });

        alert($.toJSON(answers));
    }
}

var Question_PickOne = function() {
    this._num_answers = 4;
    this._text_input = null;
    this._id_input = null;
    this._answer_inputs = [];
    this._error_targets = {};
    this._data = null;
}

/**
* Прототип объекта вопроса с одним правильным ответом.
*/
Question_PickOne.prototype = {
    /**
    * Тип вопроса.
    */
    _type: 'pick-one',

    /**
    * Количество возможных ответов на вопрос.
    */
    _num_answers: 4,

    /**
    * CSS-классы для различных элементов формы вопроса.
    */
    _classes: {
        form:   'question-form',
        text:   'question-text',
        radio:  'question-radio',
        answer: 'question-answer',

        examContainer: 'exam-container',
        examForm:      'exam-form',
        examQuestion:  'exam-question',
        examAnswer:    'exam-answer',

        inactiveInput: 'inactive',
        errorTarget:   'e-target'
    },

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

    _error_targets: {},

    _data: null,

    _view: null,

    setData: function(data) {
        this._data = {
            question_id:    data.question_id,
            type:           this._type,
            question:       data.question,
            answers:        data.answers,
            correct_answer: data.correct_answer
        };
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

        data.question_id    = this._id_input.val();
        data.question       = this._question_input.val();
        data.answers        = [];
        data.correct_answer = null;

        if (this._question_input.hasClass(this._classes.inactiveInput)) {
            data.question = '';
        } else {
            data.question = this._question_input.val();
        }

        var radio, answer, value;

        /* Пробегаемся по полям для ввода ответов */
        for (idx in this._answer_inputs)
        {
            radio  = this._answer_inputs[idx].radio;
            answer = this._answer_inputs[idx].answer;

            /* Если radio-button включён, */
            if (radio.attr('checked')) {
                /* то запоминаем этот ответ как правильный  */
                data.correct_answer = idx;
            }

            /* Добавляем в массив текст ответа */

            if (!answer.hasClass(this._classes.inactiveInput)) {
                data.answers.push(answer.val());
            }
        }

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

    renderForm: function(container, q_data) {
        if (undefined === q_data && this.issetData()) {
            q_data = this.getData();
        }

        var div = $('<div></div>');

        /* Создаём контейнер формы */
        var form = $('<form></form>')
                     .addClass(this._classes.form);

        /* Поле ввода для текста вопроса */
        var question = $('<input type="text" value="" />')
                         .data('default', 'Вопрос')
                         .addClass(this._classes.text);

        var e_target = $('<span></span>')
                         .addClass(this._classes.errorTarget)
                         .hide();

        var q_id = $('<input type="hidden" value="" />');

        div.append(form);
        form
            .append(question)
            .append(e_target)
            .append(q_id);

        this._question_input = question;
        this._id_input   = q_id;
        this._error_targets.question = e_target;

        var empty_inputs = [];

        if (undefined !== q_data) {
            question.val(q_data.question);
            q_id.val(q_data.question_id);
        } else {
            empty_inputs.push(question);
            empty_inputs.push(q_id);
        }

        var answers, radio, answer;

        for (var i = 0; i < this._num_answers; i++)
        {
            /* Radio-button для выбора правильного ответа */
            radio = $('<input type="radio" name="correct_answer" />')
                      .addClass(this._classes.radio);

            /* Поле для ввода текста ответа */
            answer = $('<input type="text" value="" />')
                       .data('default', 'Ответ' + (i + 1))
                       .addClass(this._classes.answer);

            if (undefined !== q_data)
            {
                if (i == q_data.correct_answer) {
                    radio.attr('checked', 'checked');
                }
                answer.val(q_data.answers[i]);
            }
            else
            {
                empty_inputs.push(answer);
            }

            /* Сохраняем пару radio-button и поле с ответом */
            this._answer_inputs.push({
                radio: radio,
                answer: answer
            });

             /* И добавляем их к форме */
            form
                .append(radio)
                .append(answer);
        }

        /* Для выбранных полей ввода устанавливаем текстовые подсказки */
        $(empty_inputs).each(this._createInputHinter());

        //alert(div.html());

        /* Добавляем форму на страницу */
        container.append(div);
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
        var target, msg;

        for (target in errors)
        {
            msg = errors[target];
            this._error_targets[target]
                .text(msg)
                .show();
        }
    },

    hideErrors: function() {
        $.each(this._error_targets, function(idx, elem) {
            elem.hide();
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
            $(this).val($(this).data('default'));
            $(this)
                //.data('default', $(this).val())
                .addClass(class_inactive)

                .focus(function() {
                    $(this).removeClass(class_inactive);
                    if ($(this).val() == $(this).data('default') || '') {
                        $(this).val('');
                    }
                })

                .blur(function() {
                    var default_val = $(this).data('default');
                    if ($(this).val() == '') {
                        $(this).addClass(class_inactive);
                        $(this).val($(this).data('default'));
                    }
                });
        };

        return hinter;
    }
}

function inherit(child, parent) {
    var f = function() {};

    f.prototype = parent.prototype;
    child.prototype = new f();
    child.prototype.constructor = child;
    child._parent = parent.prototype
}

var View = function() {/*_*/}

View.prototype = {
    render: function(tpl, data) {
        return $.nano(tpl, data);
    }
};

var View_Question_PickOne = function() {
    this._parent = View_Question_PickOne._parent;
}
inherit(View_Question_PickOne, View);

$.extend(View_Question_PickOne.prototype, {
    _classes: {
        inactiveInput: 'inactive',
        errorTarget:   'e-target'
    }
});

var View_Question_PickOne_Show = function() {
    this._parent = View_Question_PickOne_Show._parent;
    $.extend(this._classes, this._parent._classes);
}
inherit(View_Question_PickOne_Show, View_Question_PickOne);

$.extend(View_Question_PickOne_Show.prototype, {
    _classes: {
        container: 'exam-container',
        form:      'exam-form',
        question:  'exam-question',
        answer:    'exam-answer',
        radio:     'exam-radio',
    },

    _tpl: {
        question: '<li class="{cls.container}">' +
                      '<form class="{cls.form}">' +
                          '<div class="{cls.question}">{text}</div>' +
                              '<span class="{cls.errorTarget}"></span>' +
                              '<input type="hidden" value="{id}" />' +
                              '{answers}' +
                      '</form>' +
                  '</li>',

        answer: '<div class="{cls.answer}">' +
                    '<input type="radio" name="correct_answer" class="{cls.radio}" />' +
                    '{answer}' +
                 '</div>'
    },

    _html: null,

    render: function(data) {
        var answers = '';

        for (idx in data.answers) {
            answers += this._parent.render(this._tpl.answer, {
                cls:    this._classes,
                answer: data.answers[idx]
            });
        }

        data.cls     = this._classes;
        data.answers = answers;

        var html = this._parent.render(this._tpl.question, data);

        this._html = $(html);
        return this._html;
    },

    getRadios: function() {
        return $('.' + this._classes.radio, this._html);
    }
});
