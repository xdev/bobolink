<?php

/*
	beaudurrant at gmail dot com
	19-Dec-2007 11:23
	http://www.php.net/manual/en/function.xml-parse-into-struct.php
*/

class XmlToArray  {
   
    // raw xml
    private $rawXML;
    // xml parser
    private $parser = null;
    // array returned by the xml parser
    private $valueArray = array();
    private $keyArray = array();
   
    // arrays for dealing with duplicate keys
    private $duplicateKeys = array();
   
    // return data
    private $output = array();
    private $status;

    public function XmlToArray($xml){
        $this->rawXML = $xml;
        $this->parser = xml_parser_create();
        return $this->parse();
    }

    private function parse(){
       
        $parser = $this->parser;
       
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); // Dont mess with my cAsE sEtTings
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);     // Dont bother with empty info
        if(!xml_parse_into_struct($parser, $this->rawXML, $this->valueArray, $this->keyArray)){
            $this->status = 'error: '.xml_error_string(xml_get_error_code($parser)).' at line '.xml_get_current_line_number($parser);
            return false;
        }
        xml_parser_free($parser);

        $this->findDuplicateKeys();

        // tmp array used for stacking
        $stack = array();        
        $increment = 0;
       
        foreach($this->valueArray as $val) {
            if($val['type'] == "open") {
                //if array key is duplicate then send in increment
                if(array_key_exists($val['tag'], $this->duplicateKeys)){
                    array_push($stack, $this->duplicateKeys[$val['tag']]);
                    $this->duplicateKeys[$val['tag']]++;
                }
                else{
                    // else send in tag
                    array_push($stack, $val['tag']);
                }
            } elseif($val['type'] == "close") {
                array_pop($stack);
                // reset the increment if they tag does not exists in the stack
                if(array_key_exists($val['tag'], $stack)){
                    $this->duplicateKeys[$val['tag']] = 0;
                }
            } elseif($val['type'] == "complete") {
                //if array key is duplicate then send in increment
                if(array_key_exists($val['tag'], $this->duplicateKeys)){
                    array_push($stack, $this->duplicateKeys[$val['tag']]);
                    $this->duplicateKeys[$val['tag']]++;
                }
                else{               
                    // else send in tag
                    array_push($stack,  $val['tag']);
                }
                $this->setArrayValue($this->output, $stack, $val['value']);
                array_pop($stack);
            }
            $increment++;
        }

        $this->status = 'success: xml was parsed';
        return true;

    }
   
    private function findDuplicateKeys(){
       
        for($i=0;$i < count($this->valueArray); $i++) {
            // duplicate keys are when two complete tags are side by side
            if($this->valueArray[$i]['type'] == "complete"){
                if( $i+1 < count($this->valueArray) ){
                    if($this->valueArray[$i+1]['tag'] == $this->valueArray[$i]['tag'] && $this->valueArray[$i+1]['type'] == "complete"){
                        $this->duplicateKeys[$this->valueArray[$i]['tag']] = 0;
                    }
                }
            }
            // also when a close tag is before an open tag and the tags are the same
            if($this->valueArray[$i]['type'] == "close"){
                if( $i+1 < count($this->valueArray) ){
                    if(    $this->valueArray[$i+1]['type'] == "open" && $this->valueArray[$i+1]['tag'] == $this->valueArray[$i]['tag'])
                        $this->duplicateKeys[$this->valueArray[$i]['tag']] = 0;
                }
            }
           
        }
       
    }
   
    private function setArrayValue(&$array, $stack, $value){
        if ($stack) {
            $key = array_shift($stack);
            $this->setArrayValue($array[$key], $stack, $value);
            return $array;
        } else {
            $array = $value;
        }
    }
   
    public function getOutput(){
        return $this->output;
    }
   
    public function getStatus(){
        return $this->status;   
    }
      
}

?>