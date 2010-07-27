var Test = function() {
    this.questions = [];
};

/**
* Прототип объекта для создания тестов. Управляет формами для внесения данных
* теста и вопросов и собирает из них данные в один итоговый объект.
*/
Test.prototype = {
    questions: [],

    _id: null,

    _inputs_map: {
        theme:          'theme',
        num_questions:  'num_questions',
        errors_limit:   'errors_limit',
        attempts_limit: 'attempts_limit'
    },

    _questions_map: {
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
    addPickOne: function(container) {
        /* Создаём новый вопрос и добавляем его форму на страницу */
        var q = new Question_PickOne();
        q.addForm(container);

        /* Сохраняем в списке вопросов теста */
        this.questions.push(q);
    },

    setOptions: function(options) {
        for (key in options) {
            this._getInput(key).val(options[key]);
        }
    },

    /**
    * @todo rename to getOptions
    */
    saveOptions: function() {
        /* Собираем параметры теста */
        var data = {
            test_id:        this.getId(),
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
            q_obj = this._questions_map[q.type]();

            q_obj.setData(q);
            q_obj.addForm(container);

            this.questions.push(q_obj);
        }
    },

    /**
    * Сохранение всех данных теста в одном объекте.
    */
    saveQuestions: function() {
        var questions = [], q;

        /* Перебираем все вопросы в тесте */
        for (key in this.questions)
        {
            q = this.questions[key];
            /* Сохраняем данные очередного вопроса в массиве */
            questions.push(q.save());
        }

        return questions;
    }
}

var Question_PickOne = function() {
    this.question = '';
    this.answers = [];
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
    classes: {
        form:   'question-form',
        text:   'question-text',
        radio:  'question-radio',
        answer: 'question-answer',

        inactiveInput: 'inactive'
    },

    /**
    * Объект с текстом вопроса.
    */
    text: null,

    /**
    * Radio-button'ы и поля для ввода ответов.
    */
    answers: [],

    data: {},

    setData: function(data) {
        /* Начинаем заполнять массив данными - типом вопроса и текстом */
        this.data = {
            id: data.id,
            type: data.type,
            text: data.question,
            answers: [],
            correct_answer: data.correct_answer
        };

        var answer;

        /* Пробегаемся по полям для ввода ответов */
        for (idx in data.answers)
        {
            answer = data.answers[idx];
            this.data.answers.push(answer);
        }
    },

    renderForm: function(container) {
        //
    },

    /**
    * Добавление формы вопроса на страницу в конец указанного контейнера.
    */
    addForm: function(container) {
        var inputs = [], radio, answer, q_id;

        /* Создаём контейнер формы */
        var form = $('<form></form>')
                     .addClass(this.classes.form);

        /* Поле ввода для текста вопроса */
        var text = $('<input type="text" value="Вопрос" />')
                     .addClass(this.classes.text);

        q_id = $('<input type="hidden" value="" />');

        if (undefined !== this.data.type) {
            text.val(this.data.text);
            q_id.val(this.data.id);
        } else {
            inputs.push(text);
        }

        /* Запоминаем поле, чтобы потом взять из него текст вопроса */
        this.text = text;
        form
            .append(text)
            .append(q_id);

        /* Создаём input'ы для ввода ответов */
        for (var i = 0; i < this._num_answers; i++)
        {
            /* Radio-button для выбора правильного ответа */
            radio = $('<input type="radio" name="correct_answer" />')
                      .addClass(this.classes.radio);

            /* Поле для ввода текста ответа */
            answer = $('<input type="text" value="Ответ ' + (i + 1) + '" />')
                       .addClass(this.classes.answer);

            if (undefined !== this.data.type) {
                if (i == this.data.correct_answer) {
                    radio.attr('checked', 'checked');
                }

                answer.val(this.data.answers[i]);
            } else {
                inputs.push(answer);
            }

            /* Сохраняем пару radio-button и поле с ответом */
            this.answers.push({
                radio: radio,
                answer: answer
            });

            /* И добавляем их к форме */
            form
                .append(radio)
                .append(answer);
        }

        /* Для выбранных полей ввода устанавливаем текстовые подсказки */
        $(inputs).each(this._createInputHinter());

        //alert(form.html());

        /* Добавляем форму на страницу */
        container.append(form);
    },

    /**
    * Выборка данных вопроса из формы и сохранение их в одном массиве.
    */
    save: function() {
        var data, radio, answer, correct_answer = null;

        /* Начинаем заполнять массив данными - типом вопроса и текстом */
        data = {
            id: this.text.next().val(),
            type: this._type,
            text: this.text.val(),
            answers: [],
            correct_answer: null
        };

        /* Пробегаемся по полям для ввода ответов */
        for (idx in this.answers)
        {
            radio  = this.answers[idx].radio;
            answer = this.answers[idx].answer;

            /* Если radio-button включён, */
            if (radio.attr('checked')) {
                /* то запоминаем этот ответ как правильный  */
                correct_answer = idx;
            }

            /* Добавляем в массив текст ответа */
            data.answers.push(answer.val());
        }

        /* И добавляем в массив номер правильного ответа */
        data.correct_answer = correct_answer;

        return data;
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
        var class_inactive = this.classes.inactiveInput;

        var hinter = function() {
            $(this)
                .data('default', $(this).val())
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