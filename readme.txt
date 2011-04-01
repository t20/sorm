SORM - Simple ORM for PHP

Version : 0.1
Author : Bharadwaj
License: MIT License [http://en.wikipedia.org/wiki/MIT_License]
Copyright (C) 2011 by Bharadwaj


What is SORM?
=============
SORM is a simple ORM for PHP. This library makes it easy for you to interact with the database. SORM does not provide the database layer, it simply provides CRUD operations. 


Designing the model Class:
==========================
SORM relies on a few conventions.
1. Your table name and class name are the same.
2. Your column names match the class variables.
3. You have a good name for a primary key. As of now, SORM can pick up from id, <tablename>_id and TablenameId. 
Example : If you have a class called Employee, your primary key should be one among the three - id, employee_id, EmployeeId (case insensitive).
It is recommended to have a auto_increment primary key, although other models ll work.


How to use SORM?
================
In your class/model files:
   Step 1: Include Sorm.php
   Step 2: Extend your class from SormModel. No constructors, no additional variables.

In your program:
Step 1: Include database.php or similar. 
Step 2: You can now perform simple CRUD operations.

SORM Functions:

1. get($id)
2. insert()
3. delete()
4. Update()
5. search()
6. count()

All examples below use this class.

require 'sorm.php';

class Employee extends SormModel
{
    public $id;
    public $firstName;
    public $lastName;
    public $department_id;
    public $salary;
    public $enabled = 1;
    public $bonus_percentage = 10;
}

1. get($id)
Fetches an object where primary_key equals id. The primary key is automatically detected from the convention. Example :

    $employee = new Employee;
    $employee = $employee->get(20);
    echo $employee->firstName;
    // This returns an employee object with primary key (in this case id) 20


2. insert()
Inserts an instance into the database. Default values from your class are also picked up automatically. Returns the id inserted on success. (False on failure).

    $emp = new Employee;
    $emp->firstName = "John";
    $emp->lastName = "somename";
    $emp->department_id = 10;
    $emp->salary = 5000;
    // No need to set enabled and bonus_percentage
    $ret = $emp->insert();
    echo $ret; // should echo auto increment id


3. delete()
Deletes by primary key. returns true if record is deleted. False otherwise.

    $emp = new Employee;
    $emp = $emp->get(21);
    $ret = $emp->delete();


4. update()
Updates a single record, based on primary key.
Returns true on success.

    $emp = new Employee;
    $emp = $emp->get(21);
    $emp->salary = 6000;
    $ret = $emp->udpate();
    echo $ret; // should return true on success.


5. search()
Also : search($select_fields)
Set the search params in the model. Call search on the model.
You may also pass in optional select fields as an array.

    $emp = new Employee;
    $emp->department_id = 10; //set search params in the model.
    $emps = $emp->search(); // search for all employees with department_id 10
    foreach ($emps as $e)
    {
        echo "$e->firstName";
    }
    
    $emp->enabled = 1;
    $emps = $emp->search(); 
    // search for all employees with department_id 10 and enabled = 1
    foreach ($emps as $e)
    {
        echo "$e->firstName";
    }
    
    // If you want just the first name from the table, 
    // you can pass in the select_fields optional param. 
    $emps = $emp->search(array('firstName'))


6. count()
Very similar to search. Set the search params in the model. Call count().
Returns int value of the query result.

    $emp = new Employee;
    $emp->department_id = 10; //set search params in the model.
    $ret = $emp->count(); // count of all employees with department_id 10
    echo "Department 10 headcount : $ret";

    $emp->enabled = 1;
    // count all employees with department_id 10 and enabled = 1
    $ret = $emp->count(); 
    echo "Department 10 Enabled headcount : $ret";


I am currently working on Multiple update and multiple Delete.


If you find bugs, have suggestions, or ideas, please feel free to reach me. 
https://github.com/inbox/new/teraom
Thanks.