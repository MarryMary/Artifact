<?php

namespace MarryMary\Artifact;

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
        $start_length = mb_strlen($this->start);
        $end_length = mb_strlen($this->close);
        $prepared = [];
        $inner = "";
        $dump_start_tags = "";
        $dump_end_tags = "";
        $type = False;
        $start_mode = False;
        $dump_tag = False;
        $start = $this->start;
        $end = $this->close;
        $include = $this->include_enclosure;
        $chase = $this->nested_chase;
        $parse = False;
        $tag_count = 0;
        $close = 0;

        if($start_length == 2){
            $start = mb_str_split($this->start);
            $end = mb_str_split($this->close);
            $type = True;
        }

        array_push($analyzer, " ");

        foreach($analyzer as $analyze){

            if($type){
                if($analyze == $start[0] && !$dump_tag){
                    $dump_tag = True;
                    $start_mode = True;
                    $dump_start_tags .= $analyze;
                    $tag_count++;
                }else if($analyze == $end[0] && !$dump_tag){
                    $dump_tag = True;
                    $dump_end_tags .= $analyze;
                    $tag_count++;
                }else if($dump_tag && $start_mode && $tag_count < $start_length || $dump_tag && !$start_mode && $tag_count < $end_length){
                    if($start_mode){
                        if($tag_count == $start_length && $include || $tag_count == $start_length && !$include && $close != 0){
                            $inner .= $dump_start_tags;
                        }
                       $dump_start_tags .= $analyze;
                    }else{
                        if($tag_count == $end_length && $include || $tag_count == $end_length && !$include && $close != 0){
                            $inner .= $dump_end_tags;
                        }
                        $dump_end_tags .= $analyze;
                    }

                    $tag_count++;
                }else if($dump_tag && $start_mode && $start_length == $tag_count || $dump_tag && !$start_mode && $end_length == $tag_count){
                    $dump_tag = False;
                    if($start_mode){
                        if($dump_start_tags == $this->start){
                            $inner .= $analyze;
                            $start_mode = False;
                            $close++;
                        }else{
                            $inner .= $dump_start_tags;
                        }
                        $dump_start_tags = "";
                        $tag_count = 0;
                    }else{
                        if($dump_end_tags == $this->close){
                            
                            $close--;
                        }else{
                            $inner .= $dump_end_tags;
                        }
                        $dump_end_tags = "";
                        $tag_count = 0;

                        if($close == 0){
                            array_push($prepared, $inner);

                            if($chase){
                                $me = new Artifact($inner, $start, $end, $this->parsed_count, $chase, $include);
                                $rs = $me->StartAnalyze();
                                foreach($rs as $r){
                                    array_push($prepared, $r);
                                }
                            }

                            $inner = "";
                        }
                    }
                }else if($close != 0){
                    $inner .= $analyze;
                }
            }else{
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
        }
        return $prepared;
    }
}