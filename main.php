<?php

namespace MarryMary\Artifact\Main;

/*
* Artifact the enclosing character parser v1.0.0
* MarryMary 2022/07/22
*/

class Artifact{
    private $text;
    private $start;
    private $close;
    private $include_enclosure;
    private $parsed_count;
    private $nested_chase;

    public function __construct(String $text, String $start, String $close, Int $parsed_count = 0, Bool $nested_chase = false, Bool $include_enclosure = false){
        $this->text = $text;
        $this->start = $start;
        $this->close = $close;
        $this->nested_chase = $nested_chase;
        $this->include_enclosure = $include_enclosure;
        $this->parsed_count = $parsed_count;
    }

    public function StartAnalyze(){
        $analyzer = mb_str_split($this->text);
        $prepared = [];
        $inner = "";
        $start = $this->start;
        $end = $this->close;
        $include = $this->include_enclosure;
        $chase = $this->nested_chase;
        $close = 0;

        foreach($analyzer as $analyze){
            if($analyze == $start){
                if($include || !$include && $close != 0){
                    $inner .= $analyze;
                }

                $close++;
            }else if($analyze == $end){
                $close --;

                if($include || !$include && $close != 0){
                    $inner .= $analyze;
                }
                
                if($close == 0){
                    array_push($prepared, $inner);

                    if($chase){
                        print($inner."\n");
                        $me = new Artifact($inner, $start, $end, $this->parsed_count, $chase, $include);
                        $rs = $me->StartAnalyze();
                        foreach($rs as $r){
                            array_push($prepared, $r);
                        }
                    }
                    
                    $inner = "";
                }
            }else if($close != 0){
                $inner .= $analyze;
            }
        }
        return $prepared;
    }
}