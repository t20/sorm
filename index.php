<?php

class SormModel
{
    
    private $model = null;
    public $primary_key = null;
    private $fields = null;

    function __construct()
    {
        $this->model = get_class($this);
        //$this->fields = get_object_vars($this);
        $this->fields = get_class_vars(get_class($this));
        $superclass_fields = get_class_vars(__CLASS__);
        $this->fields = array_diff_assoc($this->fields, $superclass_fields);
        $this->get_primary_key();
    }
    
    function search($fields='')
    {
        $select_model_query = "SELECT * FROM `$this->model`";
        $where_fields = $this->get_set_fields();
        // var_dump($where_fields);
        $where_clauses = array();
        foreach ($where_fields as $f => $v)
        {
            $where_clauses [] = "`$v` = '" . $this->$v . "'";
        }
        $select_model_query .= " WHERE " . implode(" AND " , $where_clauses);
        $result = db_query($select_model_query);
        $models = array();
        while($tmp = db_fetch_array($result)) 
        {
            $model = new $this->model;
            foreach ($this->fields as $field => $value)
            {
                    $model->$field = $tmp[$field];
            }
            $models [] = $model;
        }
        return $models;
    }
    
    function get($id)
    {
        $select_model_query = "SELECT * FROM $this->model where $this->primary_key = $id";
        $result = db_query($select_model_query);
        $model = new $this->model;
        $model->id = $id;
        while($tmp = db_fetch_array($result)) 
        {
            foreach ($this->fields as $field => $valu)
            {
                    $model->$field = $tmp[$field];
            }
        }
        return $model;
    }
    
    function update()
    {
        $update_model_query = "UPDATE `$this->model` SET ";
        $where_fields = $this->get_set_fields();
        $set_clauses = array();
        foreach ($where_fields as $f => $v)
        {
            $set_clauses [] = "`$v` = '" . $this->$v . "'";
        }
        $update_model_query .= implode(" , " , $set_clauses);
        $p = $this->primary_key;
        $id = $this->$p;
        $where_clause = " WHERE $this->primary_key = $id";
        $update_model_query .= $where_clause;
        $result = db_query($update_model_query);
        return $result;
    }
    
    function insert()
    {
        $insert_model_query = "INSERT INTO $this->model ";
        $all_fields = $this->get_set_fields();
        var_dump($all_fields);
        $insert_model_query .= "( " .implode(" , " , $all_fields) . " )";
        $field_values = array();
        foreach ($all_fields as $f => $v)
        {
            $field_values [] = "'". $this->$v . "'";
        }
        $insert_model_query .= " VALUES (" . implode(" , " , $field_values) . ")";
        echo "$insert_model_query";
        $result = db_query($insert_model_query);
        return $result;
    }
    
    function delete()
    {
        $delete_model_query = "DELETE FROM $this->model ";
        $p = $this->primary_key;
        $id = $this->$p;
        $where_clause = " WHERE $this->primary_key = $id";
        $delete_model_query .= $where_clause;
        echo "$delete_model_query";
        $result = db_query($delete_model_query);
        return $result;
    }

    function count()
    {
        # code...
    }

    private function get_set_fields()
    {
        $out = array();
        foreach ($this->fields as $field => $value)
        {
            if ($field != 'model' && $field != 'fields')
            if($this->$field)
                $out[] = $field;
        }
        return $out;
    }

    private function get_primary_key()
    {
        if (in_array("id", array_keys($this->fields)))
        {
            $this->primary_key = 'id';
            return;
        }
        foreach ($this->fields as $field => $value)
        {
            if ($field != 'model' && $field != 'fields')
            {
                if (stristr($field, $this->model . "_id" ) ||
                    stristr($field, $this->model . "id" )
                    )
                    {
                        $this->primary_key = $field;
                        return;
                    }
            }
        }
    }
}

class Messages extends SormModel
{
    public $id;
    public $content;
    public $updated;
    public $mood;
    public $user = null;
    public $enabled = 1;
}

class Seminar extends SormModel
{
    public $SeminarId;
    public $title;
    public $abstract;
    public $seminar_date;
    public $speaker;
    public $speaker_bio;
}

require_once 'database.php';

$test = new Messages;
$test->enabled = 1;
$test->mood = 'great';
// if($test->content)
//     echo "mood set";
// else
//     echo "mood not set";
$messages = $test->search();
// var_dump($messages);
foreach ($messages as $message)
{
    echo "<br/>Mood : $message->mood Message : $message->content <br/>";
}
$test = $test->get(1);
        echo "<hr/>";
//var_dump($test);
$test->content = 'new content baby22';
$test->update();
echo $test->mood;

$sem = new Seminar;
echo $sem->primary_key;

$newM = new Messages;
$newM->content = 'testing insert';
$newM->mood = 'good';
$newM->insert();

$newM = new Messages;
$newM = $newM->get(11);
$newM->delete();
// $db = new mysqli('localhost', 'barath', 'barath123', 'vallpress');

?>
