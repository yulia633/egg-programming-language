![linter and tests](https://github.com/yulia633/egg-programming-language/workflows/linter%20and%20tests/badge.svg)

>In development

# Cинтаксический анализатор PHP

### О коде

Универсальный высокоуровневый математический и функциональный язык программирования,
реализованный на **php** по мотивам книги Майрейна Хавербеке "Выразительный Javascript".

---

### Установка

* `git clone`
* `cd egg-programming-language-main`
* `make install`
* `make test`

---

## Примеры

### Циклы
```php
run("do(define(total, 0),",
    "   define(count, 1),",
    "   while(<(count, 11),",
    "         do(define(total, +(total, count)),",
    "             define(count, +(count, 1)))),",
    "   print(total))");
	// -> 55
```

### Функции
```php
run(`
do(define(plusOne, fun(a, +(a, 1))),
   print(plusOne(10)))
`);
// → 11
```

```php
run(`
do(define(pow, fun(base, exp,
     if(==(exp, 0),
        1,
        *(base, pow(base, -(exp, 1)))))),
   print(pow(2, 10)))
`);
// → 1024
```

### Массивы
```php
run(`
do(define(sum, fun(array,
     do(define(i, 0),
        define(sum, 0),
        while(<(i, length(array)),
          do(define(sum, +(sum, element(array, i))),
             define(i, +(i, 1)))),
        sum))),
   print(sum(array(1, 2, 3))))
`);
//-> 6
```

### Комментарии
```php
print_r(parse("# hello\nx"));
// → {type: "word", name: "x"}

print_r(parse("a # one\n   # two\n()"));
// → {type: "apply",
//    operator: {type: "word", name: "a"},
//    args: []}
```

### Область видимости
```php
run(`
do(define(x, 4),
   define(setx, fun(val, set(x, val))),
   setx(50),
   print(x))
`);
// → 50

//run(`set(quux, true)`);
// → Some kind of ReferenceError
```

