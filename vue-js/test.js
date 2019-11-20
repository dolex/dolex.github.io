
// Create a quiz object with a title and [n] questions.
// A question has one or more answer, and one or more is valid.
var quiz = {
  title: 'Irregular Verbs',
  questions: [
    {
      text: "be /bē/",
      responses: [
        {text: 'Придти'},
        {text: 'Быть', correct: true},
      ]
    }, {
      text: "beat /bit'/",
      responses: [
        {text: 'Бить', correct: true},
        {text: 'Ударить'},
      ]
    }, {
      text: "become /biˈkəm/",
      responses: [
        {text: 'Встать'},
        {text: 'Становиться', correct: true},
      ]
    }, {
      text: "begin /biˈgin/",
      responses: [
        {text: 'Начинать', correct: true},
        {text: 'Начал'},
      ]
    }, {
      text: "bleed /blēd/",
      responses: [
        {text: 'Кривлятся'},
        {text: 'Кровоточить', correct: true},
      ]
    }, {
      text: "blow /blō/",
      responses: [
        {text: 'Выдувать'},
        {text: 'Дуть', correct: true},
      ]
    }, {
      text: "break /brāk/",
      responses: [
        {text: 'Ломать', correct: true},
        {text: 'Взломать'},
      ]
    }, {
      text: "bring /briNG/",
      responses: [
        {text: 'Нести'},
        {text: 'Приносить', correct: true},
      ]
    }, {
      text: "build /bild/",
      responses: [
        {text: 'Строить', correct: true},
        {text: 'Сделать'},
      ]
    }, {
      text: "burn /bərn/",
      responses: [
        {text: 'Испарить'},
        {text: 'Жечь', correct: true},
      ]
    }, {
      text: "burst /bərst/",
      responses: [
        {text: 'Взрыв', correct: true},
        {text: 'Разрушить'},
      ]
    }, {
      text: "buy /bī/",
      responses: [
        {text: 'Купить', correct: true},
        {text: 'Продать'},
      ]
    }, {
      text: "catch /kaCH,keCH/",
      responses: [
        {text: 'Уснуть'},
        {text: 'Ловить', correct: true},
      ]
    }, {
      text: "choose /kaCH,keCH/",
      responses: [
        {text: 'Положить'},
        {text: 'Выбирать', correct: true},
      ]
    }, {
      text: "come /kəm/",
      responses: [
        {text: 'Приходить', correct: true},
        {text: 'Уходить'},
      ]
    }, {
      text: "cost /kôst/",
      responses: [
        {text: 'Убрать'},
        {text: 'Стоить', correct: true},
      ]
    }, {
      text: "creep /krēp/",
      responses: [
        {text: 'Нести'},
        {text: 'Ползать', correct: true},
      ]
    }, {
      text: "cut /kət/",
      responses: [
        {text: 'Свернуть'},
        {text: 'Резать', correct: true},
      ]
    }, {
      text: "do /do͞o,dō/",
      responses: [
        {text: 'Делать', correct: true},
        {text: 'Класть'},
      ]
    }, {
      text: "draw /drô/",
      responses: [
        {text: 'Тянуть'},
        {text: 'Рисовать', correct: true},
      ]
    }, {
      text: "dream /drēm/",
      responses: [
        {text: 'Спать'},
        {text: 'Мечтать', correct: true},
      ]
    }
  ],
  data: {
    checked: true
  },
};

new Vue({
  el: '#app',
  data: {
    quiz: quiz,
    questionIndex: null,
    userResponses: [],
    // Store current question index
    questionIndex: 0,
    // An array initialized with "false" values for each question
    // It means: "did the user answered correctly to the question n?" "no".
    userResponses: Array(quiz.questions.length).fill(false)
  },
  // The view will trigger these methods on click
  methods: {
    //   onChange:function(){
      //     checked=false;
      //     return this.userResponses.filter(function(val) { return val }).length;
      //  },
      // Go to next question
      next: function() {
        this.questionIndex++;
      },
      // Go to previous question
      prev: function() {
        this.questionIndex--;
      },
      // Return "true" count in userResponses
      score: function() {
      return this.userResponses.filter(function(val) { return val }).length;
    }
  }
});