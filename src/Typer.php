<?php

namespace MarryMary\Artifact;

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

    public function trim_end(String $target, String $excision = " ", Int $limit = 0){
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

    public function trim_start(String $target, String $excision = " ", Int $limit = 0){
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
        return $this->trim_end($this->trim_start($target, $excision, $limit), $excision, $limit);
    }

    public function print(...$target){
        $echo = "";
        foreach($target as $t){
            $echo .= $t." ";
        }
        echo $this->trim_end($echo, " ", 1);
    }

    public function exchange_string(Array $target){
        $arrays = "";
        foreach($target as $key => $val){
            if(is_array($val)){
                $arrays .= "[".$this->exchange_string($val)."]";
            }else{
                $arrays .= $key." => ".$val.", ";
            }
        }

        $arrays = $this->trim_end($arrays, ",")."]";
        return $arrays;
    }

    public function writer_array(String $fullpath, Array $target){
        $array_to_string = $this->exchange_string($target);
        return file_put_contents($fullpath, $array_to_string);
    }

    public function in_string(String $needle, String $target){
        if(strpos($target, $needle) !== false){
            return True;
        }else{
            return False;
        }
    }

    public function startswith(String $needle, String $target, Bool $trim_spaceandtab = False){
        $analyzer = mb_str_split($this->trim_all($this->trim_all($target), "\t"));
        $limit = count($analyzer);
        $read_start = "";

        for($i = 0; $i < $limit; $i++){
            if(array_key_exists($i, $analyzer)){
                $read_start .= $analyzer[$i];
            }else{
                break;
            }
        }

        if($needle == $read_start){
            return True;
        }else{
            return False;
        }
    }

    public function endswith(String $needle, String $target, Bool $trim_spaceandtab = False){
        $analyzer = array_reverse(mb_str_split($this->trim_all($this->trim_all($target), "\t")));
        $limit = count($analyzer);
        $read_end = "";

        for($i = 0; $i < $limit; $i++){
            if(array_key_exists($i, $analyzer)){
                $read_end .= $analyzer[$i];
            }else{
                break;
            }
        }

        if($needle == strrev($read_end)){
            return True;
        }else{
            return False;
        }
    }


    public function str_compressor(String $string){
        $analyzer = mb_str_split($string);
        return implode(array_unique($analyzer));
    }

    public function range($start, $end){
        $japanese_kana_list = ["あ", "い", "う", "え", "お", "か", "き", "く", "け", "こ", "さ", "し", "す", "せ", "そ", "た", "ち", "つ", "て", "と", "な", "に", "ぬ", "ね", "の", "は", "ひ", "ふ", "へ", "ほ", "ま", "み", "む", "め", "も", "や",  "ゆ", "よ", "ら", "り", "る", "れ", "ろ", "わ", "ゐ", "う", "ゑ", "を", "ん"];
        $english_alphabet_list = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];
        $japanese_katakana_list = ["ア", "イ", "ウ", "エ", "オ", "カ", "キ", "ク", "ケ", "コ", "サ", "シ", "ス", "セ", "ソ", "タ", "チ", "ツ", "テ", "ト", "ナ", "ニ", "ヌ", "ネ", "ノ", "ハ", "ヒ", "フ", "ヘ", "ホ", "マ", "ミ", "ム", "メ", "モ", "ヤ", "ユ", "ヨ", "ラ", "リ", "ル", "レ", "ロ", "ワ", "イ", "ウ", "エ", "ヲ", "ン"];
        $load = False;
        $prepared_string = [];

        if($this->get_type($start) == "Integer" || $this->get_type($start) == "Float" || $this->get_type($end) == "Integer" || $this->get_type($end) == "Float"){
            return range($start, $end);
        }else{
            if(in_array($start, $english_alphabet_list) && in_array($end, $english_alphabet_list)){
                foreach($english_alphabet_list as $alpherbet){
                    if($alpherbet == $start){
                        $load = True;
                        $prepared_string .= $alpherbet;
                    }else if($alpherbet == $end){
                        $load = False;
                        $prepared_string .= $alpherbet;
                        break;
                    }else if($load){
                        $prepared_string .= $alpherbet;
                    }
                }
            }else if(in_array($start, $japanese_kana_list) && in_array($end, $japanese_kana_list)){
                foreach($japanese_kana_list as $kana){
                    if($kana == $start){
                        $load = True;
                        $prepared_string .= $kana;
                    }else if($kana == $end){
                        $load = False;
                        $prepared_string .= $kana;
                        break;
                    }else if($load){
                        $prepared_string .= $kana;
                    }
                }
            }else if(in_array($start, $japanese_katakana_list) && in_array($end, $japanese_katakana_list)){
                foreach($japanese_katakana_list as $katakana){
                    if($katakana == $start){
                        $load = True;
                        $prepared_string .= $katakana;
                    }else if($katakana == $end){
                        $load = False;
                        $prepared_string .= $katakana;
                        break;
                    }else if($load){
                        $prepared_string .= $katakana;
                    }
                }
            }

            return $prepared_string;
        }
    }

    public function rand($start, $end){
        $select = $this->range($start, $end);

        return $select[rand(0, count($select) - 1)];
    }

    public function str_allreplace(String $replaced, String $target){
        return  str_repeat($replaced, mb_strlen($target, "UTF8"));
    }


    public function exchange_array(String $target){
        $analyzer = mb_str_split($this->trim_end($this->trim_start($this->trim_all($target), "[", 1), "]", 1));
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