<?php

// SORM is Simple ORM for PHP
// For documentation, examples, license please see readme.txt
/*
// License: MIT License [http://en.wikipedia.org/wiki/MIT_License]
//
// Copyright (C) 2010 by Bharadwaj
// 
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
// 
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.

*/

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
    
    function search($select_fields='', $is_count=false)
    {
        $select_fields_list = ($select_fields) ? implode(" , " , $select_fields) : ' * ';
        $select_model_query = "SELECT $select_fields_list FROM `$this->model`";
        $where_fields = $this->get_set_fields();
        $where_clauses = array();
        foreach ($where_fields as $f => $v)
        {
            $where_clauses [] = "`$v` = '" . $this->$v . "'";
        }
        $select_model_query .= " WHERE " . implode(" AND " , $where_clauses);
        $result = db_query($select_model_query);
        if ($is_count)
        {
            $temp = db_fetch_array($result);
            return $temp[$select_fields[0]];
        }
        $models = array();
        $selected_fields = ($select_fields) ? array_flip($select_fields) : $this->fields;
        while($tmp = db_fetch_array($result)) 
        {
            $model = new $this->model;
            foreach ($selected_fields as $field => $value)
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
        return ($result) ? mysql_affected_rows() : false;
    }
    
    function insert()
    {
        $insert_model_query = "INSERT INTO $this->model ";
        $all_fields = $this->get_set_fields();
        //var_dump($all_fields);
        $insert_model_query .= "( " .implode(" , " , $all_fields) . " )";
        $field_values = array();
        foreach ($all_fields as $f => $v)
        {
            $field_values [] = "'". $this->$v . "'";
        }
        $insert_model_query .= " VALUES (" . implode(" , " , $field_values) . ")";
        $result = db_query($insert_model_query);
        return ($result) ? mysql_insert_id() : false;
    }
    
    function delete()
    {
        $delete_model_query = "DELETE FROM $this->model ";
        $p = $this->primary_key;
        $id = $this->$p;
        $where_clause = " WHERE $this->primary_key = $id";
        $delete_model_query .= $where_clause;
        $result = db_query($delete_model_query);
        return ($result) ? mysql_affected_rows() : false;
    }

    function updateAll()
    {
        #code ...
    }
    
    function deleteAll()
    {
        var_dump(get_object_vars($this));
        $delete_model_query = "DELETE FROM $this->model ";
        $where_fields = $this->get_set_fields();
        $where_clauses = array();
        foreach ($where_fields as $f => $v)
        {
            $where_clauses [] = "`$v` = '" . $this->$v . "'";
        }
        $delete_model_query .= " WHERE " . implode(" AND " , $where_clauses);
    }

    function count()
    {
        return $this->search(array('COUNT(*)'), true);
    }

    private function get_set_fields()
    {
        $out = array();
        foreach ($this->fields as $field => $value)
        {
            if($this->$field !== null)
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
            if (stristr($field, $this->model . "_id" ) ||
                stristr($field, $this->model . "id" )
                )
            {
                $this->primary_key = $field;
                return;
            }
        }
    }

    public function __set($name, $value)
    {
        //$this->data[$name] = $value;
    }

}

?>
