<?php

namespace MarryMary\Artifact;

include "Artifact.php";

class Typer{
    public function get_type(String $Target){
        $integer = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
        $bool = ["True", "False", "true", "false"];

        $analyzer = mb_str_split($Target);
        $already_int = False;
        $already_float = False;
        $dump_bool = False;
        $bool_strings = "";
        $types_flag = "String";
        $count = 0;

        foreach ($analyzer as $analyze){
            if(in_array($analyze, $integer)){
                if(!$already_int && $analyze == "0"){
                    $already_int = False;
                    $types_flag = "String";
                }else{
                    $already_int = True;
                    $types_flag = "Integer";
                }
            }else if($analyze == "."){
                if($already_int && !$already_float){
                    $already_float = True;
                    $types_flag = "Float";
                }else{
                    $already_float = False;
                    $types_flag = "String";
                }
            }else if($analyze == "T" || $analyze == "t" || $analyze == "F" || $analyze == "f"){
                $dump_bool = True;
                $bool_strings .= $analyze;
                $count++;
            }else if($dump_bool && $count == 5 || $dump_bool && $analyze == "e"){
                $bool_strings .= $analyze;
                $count++;
                if(in_array($bool_strings, $bool)){
                    $types_flag = "Boolean";
                }else{
                    $types_flag = "String";
                }

                $dump_bool = False;
                $count = 0;
            }else if($dump_bool){
                $bool_strings .= $analyze;
                $count++;
            }else{
                $types_flag = "String";
            }
        }

        return $types_flag;
    }

    public function exchange_autotype(String $Target){
        $res = $this->get_type($Target);
        if($res == "Integer"){
            return (int)$Target;
        }else if($res == "Float"){
            return (float)$Target;
        }else if($res == "Boolean"){
            return (bool)$Target;
        }else{
            return (string)$Target;
        }
    }

    public function trim_r(String $target, String $excision = " ", Int $limit = 0){
        $analyzer = mb_str_split($target);
        $trim = True;
        $prepared = "";
        $counter = 0;

        foreach(array_reverse($analyzer)  as $analyze){
            if(0 < $limit && $limit == $counter){
                $trim = False;
                $prepared .= $analyze;
            }else if($excision == $analyze && $trim){
                $counter++;
            }else{
                $trim = False;
                $prepared .= $analyze;
                $counter++;
            }
        }

        return strrev($prepared);
    }

    public function trim_l(String $target, String $excision = " ", Int $limit = 0){
        $analyzer = mb_str_split($target);
        $trim = True;
        $prepared = "";
        $counter = 0;

        foreach($analyzer as $analyze){
            if(0 < $limit && $limit == $counter){
                $trim = False;
                $prepared .= $analyze;
            }else if($excision == $analyze && $trim){
                $counter++;
            }else{
                $trim = False;
                $prepared .= $analyze;
                $counter++;
            }
        }

        return $prepared;
    }

    public function trim_all(String $target, String $excision = " ", Int $limit = 0){
        return $this->trim_r($this->trim_l($target, $excision, $limit), $excision, $limit);
    }

    public function print(...$target){
        $echo = "";
        foreach($target as $t){
            $echo .= $t." ";
        }
        echo $this->trim_r($echo, " ", 1);
    }

    public function exchange_array(String $target){
        $analyzer = mb_str_split($this->trim_r($this->trim_l($this->trim_all($target), "[", 1), "]", 1));
        $new_list = [];
        $nest = "";
        $read_nest = False;
        $already_insert = False;
        $nest_count = 0;
        $element = "";

        foreach($analyzer as $analyze){
            if($analyze == " "){
                continue;
            }elseif($analyze == "," && !$read_nest){
                if($already_insert){
                    $already_insert = False;
                }else{
                    $check_assoc = explode("=>", $element);

                    if($element != "" && count($check_assoc) == 2){
                        if(is_array($element)){
                            $new_list[$check_assoc[0]] = $check_assoc[1];
                        }else{
                            $new_list[$check_assoc[0]] = $this->exchange_autotype($check_assoc[1]);
                        }
                    }else{
                        if(is_array($element)){
                            array_push($new_list, $element);
                        }else{
                            array_push($new_list, $this->exchange_autotype($element));   
                        }
                    }
                }
                $element = "";
            }elseif($analyze == "[" && $element == "" && $nest_count == 0 && !$read_nest){
                $read_nest = True;
                $nest .= $analyze;
            }elseif($analyze == "]" && $nest != "" && $read_nest &&$nest_count == 0){
                $read_nest = False;
                $nest .= $analyze;
                $instance = new Typer();
                $res = $instance->exchange_array($nest);
                array_push($new_list, $res);
                $already_insert = True;
                $read_nest = False;
                $nest_count = 0;
            }elseif($read_nest && $analyze == "["){
                $nest .= $analyze;
                $nest_count++;
            }elseif($read_nest && $analyze == "]"){
                $nest.= $analyze;
                $nest_count--;
            }else if($read_nest){
                $nest .= $analyze;
            }else{
                $element .= $analyze;
            }
        }

        if($element != ""){
            $check_assoc = explode("=>", $element);

            if($element != "" && count($check_assoc) != 2){
                if(is_array($element)){
                    array_push($new_list, $element);
                }else{
                    array_push($new_list, $this->exchange_autotype($element));   
                }
            }else{
                if(is_array($element)){
                    $new_list[$check_assoc[0]] = $check_assoc[1];
                }else{
                    $new_list[$check_assoc[0]] = $this->exchange_autotype($check_assoc[1]);
                }
            }
            $element = "";
        }

        return $new_list;
    }
}