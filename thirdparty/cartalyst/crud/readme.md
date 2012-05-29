## CRUD Model for Laravel
--------------------------
### Description

Crud is a pre-built model with create, read, update, and delete functionality as well as validation and a few other helper methods built into it.  This model was created for those who want some basic CRUD functionality and do not wish to use a full on ORM, such as Eloquent.

### Installation

Download or clone into your application/models/ folder, or libraries if you wish, just make sure it gets loaded.

### Usage

The best way to use Crud is to make a new Model and extend Crud.

```php
class User extends Crud {}
```

By default, Crud will set your primary key to 'id' and your table name to the plural form of the class name via Laravels Str::plural.  If you wish to have a different primary key or table, all you need to to do is override a couple of properties.

```php
public static $key = 'my_primary_key';

protected static $table = 'my_users_table';
```

We will get into some other properties you can change a little later on to increase the functionality of the model.

#### Create

To create a user, all you need to do is instantiate a new instance of the model with the data you wish to add and save it. If you pass a primary key, you need to set the model to new, by setting a 2nd paramter to User() as true, are calling is_new(true).  When passing a primary key, CRUD assumes you want to update, this will override that assumption.

```php
// create a user object
$user = new User(array(
	'first_name' => 'John',
	'last_name'  => 'Doe',
	'email'      => 'john.doe@crud.com'
));

// or
$user = new User();
$user->first_name = 'John',
$user->last_name  = 'Doe',
$user->email      = 'john.doe@crud.com';

// or when passing a primary key
$user = new User(array(
	'id'         => 10
	'first_name' => 'John',
	'last_name'  => 'Doe',
	'email'      => 'john.doe@crud.com'
), true);

// or
$user = new User();
$user->id         = 10
$user->first_name = 'John',
$user->last_name  = 'Doe',
$user->email      = 'john.doe@crud.com';
$user->is_new(true);

// now save it
$user->save();
```

save() will return the result of Laravel's DB::insert() method;

#### Read

There are a few ways to retrieve data using Crud.

To find a specific model/record, use the find() method.  This method takes 2 parameters, the primary key value and an array of columns to select.  The columns paramter is optional and will default to '*'.

```php
// find all columns where the users primary key is equal to 5
$user = User::find(5);

// finds the users first and last where the primary key is equal to 3
$user = User::find(3, array('first_name', 'last_name'));
```

If you wish to retrieve all models/records in the table, you can use the all() method.

```php
$users = User::all();
```

Lastly, if you want to retrieve records with your own query, you can either create your own method and call it, or use the query() method, which starts your query for you with the DB connection and table pre-set.

```php
$users = User::query()->where('email', '=', 'john.doe@crud.com')->get();

// or within crud add
public static function find_by_email($email, $columns = array('*'))
{
	return static::$query()->where('email', '=', $email)->get($columns);
}

// then call
$users = User::find_by_email('john.doe@crud.com');
```

You can also retrive a count by calling the count method(), which takes 2 optional parameters: column and distinct.
```php
$count = User::count();

// or
$count = User::count('first_name', true); // counts distinct first_name's
```

#### Update

Updating works in the same way as creating, except you also need to pass the primary key value. When passing a primary key value, CRUD will automatically assume that you want to update.

```php
// create a user object
$user = new User(array(
	'id'    => 5
	'email' => 'john.doe@update.com' // update the email
));

// or
$user = new User();
$user->id    = 5,
$user->email = 'john.doe@update.com';

// or
$user = User::find(5);
$user->email = 'john.doe@update.com';

// now save it
$user->save();
```
Note: If you try to update a model and no primary key is set, an exception will be thrown.

#### Delete

Deleting a model is as simple as calling delete on an existing Crud model that contains a key.

```php
$user = new User(array(
	'id' => 5
));

// or
$user = User::find(5);

$user->delete();
```
Note: If you try to delete a model and no primary key is set, an exception will be thrown.

### Advanced Settings

As well as the primary key and table settings mentioned above, you can also set a connection, sequence, timestamps and validation rules for the model.  Connection and Sequence are straight forward, just set them to the value you wish.

#### Timestamps

The timestamp setting is a boolean value to automatically create created_at, and updated_at value for your table.  Make sure these fields are present in your table if you wish to use the timestamps setting.

#### Rules / Validation

To set validation rules, set this property to a array of validation rules normally.  Crud will automatically run the validation and return false if it fails.

```php
protected static $rules = array(
	'first_name' => 'required',
	'last_name'  => 'required',
	'email'      => 'required|email'
)
```
If validation fails, you would probably want to retrieve the validation object so you can set error messages.  Well to do this, you just need to call the validation method.

```php
$user->save(); // this failed due to validation

$validation = $user->validation(); // retrieves the validation object

// now you can do any standard validation command.
$errors = $validation->errors->all();
```

### Override Helper Methods

Before and After every primary action (find, insert, update, delete, validation) there is an associated helper function you can override to maniuplate any data or response you wish to fit your application. Example: 'protected function before_insert()'. There is also a prep_attributes() method if you wish change attribute values after validation runs, before you insert them into the database.

### Other methods

```php
$model = new Crud();

$model->fill($array); add/replace model attributes with the passed array.

$model->attributes(); // returns an array of all the data/attributes in the model.

$model->validation(); // return the validation object if one exists

$model->is_new($bool); // sets the model to update or insert; true = insert, false = update (primary key needed)

User::table(); // returns the table name

User::query(); // returns fluent object to build off of with DB::connection and table already on it. DB::connection()->table();

User::count($column, $distinct); // returns a count (string, bool)
```
