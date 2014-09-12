Laravel Iterable Validator
==========================

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

You can also "nest" iterable fields inside the books field. Say you wanted to validate an unknown number of "citations" for each book, and each citation has an "author" field  which is required. The rules for citations must be in the form of an array, and the iterable rules and messages that apply to each individual citation must be under the key 'iterate', like so:

```php
    <?php
    $bookRules = [
        'title' => 'required',
        'author' => 'required',
        'citations' => [
            'array', // assuming we always want the citations to come through as an array
            'iterate' => [
                'rules' => [
                    // use "required_with_parent" so that this field is only required when
                    // an array entry for it actually exists - this is more or less
                    // a new rule that comes with this class
                    'author' => 'required_with_parent'
                ],
                'messages' => [
                    'author.required' => 'What is the citation author\'s name? Geez!'
                ]
            ]
        ]
    ];
```

To register this class as the default validator on your application, put this code snippet any place where the app bootstraps. With the new file structure, I'm not sure where exactly those places would be, though `routes.php` will always be fine. For a 4.2 app, I put mine in `app/start/global.php`;

```php
    <?php
    Validator::resolver(function($translator, $data, $rules, $messages)
      {
          return new ValidationIterator($translator, $data, $rules, $messages);
      });
```

Enjoy!
