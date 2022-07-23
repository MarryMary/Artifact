<?php

namespace MarryMary\Artifact;

include "Artifact.php";

class Typer{
    public function TypeGet(String $Target){
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

    public function Exchange_AutoType(String $Target){
        $res = $this->TypeGet($Target);
        if($res == "Integer"){
            return (int)$res;
        }else if($res == "Float"){
            return (float)$res;
        }else if($res == "Boolean"){
            return (bool)$res;
        }else{
            return (string)$res;
        }
    }
}