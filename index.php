<?php

class SormModel
{
    
    private $model = null;
    private $fields = null;

    function __construct()
    {
        $this->model = get_class($this);
        $this->fields = get_object_vars($this);
    }
    
    function search($fields='')
    {
        $select_model_query = "SELECT * FROM $this->model";
        $result = db_query($select_model_query);
        $models = array();
        while($tmp = db_fetch_array($result)) 
        {
            $model = new $this->model;
            foreach ($this->fields as $field => $valu)
            {
                if ($field != 'model' && $field != 'fields')
                    $model->$field = $tmp[$field];
            }
            $models [] = $model;
        }
        return $models;
    }
    
    function get($id)
    {
        $select_model_query = "SELECT * FROM $this->model where id = $id";
        $result = db_query($select_model_query);
        $model = new $this->model;
        $model->id = $id;
        while($tmp = db_fetch_array($result)) 
        {
            foreach ($this->fields as $field => $valu)
            {
                if ($field != 'model' && $field != 'fields')
                    $model->$field = $tmp[$field];
            }
        }
        return $model;
    }
    
    function update()
    {
        
    }
    
    function insert()
    {
        
    }
    
    function delete()
    {
        
    }
}

class Messages extends SormModel
{
    public $id;
    public $content;
    public $updated;
    public $mood;
    public $user = null;
    public $enabled;
}

require_once 'database.php';

$test = new Messages;
$messages = $test->search();
// var_dump($messages);
foreach ($messages as $message)
{
    echo "Message : $message->content <br/>";
}
$test = $test->get(1);
        echo "<hr/>";
//var_dump($test);
echo $test->mood;

//$test->test();

?>
