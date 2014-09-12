Laravel Iterable Validator
==========================

Extends Laravel's default validation class to allow you to recursively iterate through indexed array input

This adds an "iterate" method to Laravel's default validator. Say you're expecting input with an array of books:

```php
    <?php
    $input = [
        'customer' => 'Patrick Noonan',
        'books' => [
            [
                'title' => 'The Hobbit',
                'author' => 'Tolkien'
            ],
            [
                'title' => 'The Left Hand of Darkness',
                'author' => 'LeGuin'
            ]
        ]
    ];
```

You want the author and title to be required for each book, as well as the customer name. Add your non-iterating rules like normal, and pass your iterating rules and optional messages via the iterates method:

```php
    <?php
    $rules = ['customer' => 'required'];
    $bookRules = [
        'title' => 'required',
        'author' => 'required'
    ];
    $bookMessages = [
        'title.required' => 'You forgot the dang title! What am I even supposed to do with that?'
    ];

    $validator = Validator::make($input, $rules);
    $validator->iterates('books', $bookRules, $bookMessages);

    if ($validator->passes()) {
        //do your thing
    }
```

To register this as the default validator on your application, put this code snippet any place where the app bootstraps. With the new file structure, I'm not sure where exactly those places would be, though `routes.php` will always be fine. For a 4.2 app, I put mine in `app/start/global.php`;

```php
    <?php
    Validator::resolver(function($translator, $data, $rules, $messages)
      {
          return new ValidationIterator($translator, $data, $rules, $messages);
      });
```

Enjoy!
