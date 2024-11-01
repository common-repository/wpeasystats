<?php
/*
* Simple XML Emulation
*
* Web Traffic Genius Pro v2.1.1
* Copyright (c) 2008-2009
* Success on the Internet Pty Ltd
* PO Box 25
* Salisbury SA 5108
* Australia
* Phone: +61 422 512 549
* Fax: +61 8 8425 9657
* Web: http://www.webtrafficgenius.com
* Support: http://www.marketing-assassins.com
*
*/
if(!class_exists('SimpleXMLObject')):
class SimpleXMLObject{

	function attributes(){
		$container = get_object_vars($this);
		return (object) $container["@attributes"];
	}
	
	function content(){
		$container = get_object_vars($this);
		return (object) $container["@content"];
	}

}
endif;

if(!class_exists('MySimpleXML')):
/**
 * The Main XML Parser Class
 *
 */
class MySimpleXML {

	var $result = array();
	var $xml_array = array();
	var $ignore_level = 0;
	var $skip_empty_values = false;
	var $php_errormsg;
	var $evalCode="";

	/**
	 * Adds Items to Array tgb
	 *
	 * @param int $level
	 * @param array $tags
	 * @param $value
	 * @param string $type
	 */
	function array_insert($level, $tags, $value, $type) {
		$temp = '';
		for ($c = $this->ignore_level + 1; $c < $level + 1; $c++) {
			if (isset($tags[$c]) && (is_numeric(trim($tags[$c])) || trim($tags[$c]))) {
				if (is_numeric($tags[$c])) {
					$temp .= '[' . $tags[$c] . ']';
				} else {
					$temp .= '["' . $tags[$c] . '"]';
				}
			}
		}
		$this->evalCode .= '$this->result' . $temp . "='" . addcslashes($value, "'") . "';//(" . $type . ")\n";
		//		echo $code. "\n";
	}

	/**
	 * Define the repeated tags in XML file so we can set an index
	 *
	 * @param array $array
	 * @return array
	 */
	function xml_tags($array) {
		$repeats_temp = array();
		$repeats_count = array();
		$repeats = array();
		if (is_array($array)) {
			$n = count($array) - 1;
			for ($i = 0; $i < $n; $i++) {
				$idn = $array[$i]['tag'].$array[$i]['level'];
				if (in_array($idn,$repeats_temp)) {
					$repeats_count[array_search($idn,$repeats_temp)]+=1;
				} else {
					array_push($repeats_temp,$idn);
					$repeats_count[array_search($idn,$repeats_temp)]=1; // http://www.takeoverpageone.com
				}
			}
		}
		$n = count($repeats_count);
		for($i=0;$i<$n;$i++){
			if($repeats_count[$i]>1){
				array_push($repeats,$repeats_temp[$i]);
			}
		}
		unset($repeats_temp);
		unset($repeats_count);
		return array_unique($repeats);
	}

	/**
	 * Converts Array Variable to Object Variable
	 *
	 * @param array $arg_array
	 * @return $tmp
	 */
	function array2object ($arg_array) {
		if (is_array($arg_array)) {
			$keys = array_keys($arg_array);
			if(!is_numeric($keys[0])) $tmp = new SimpleXMLObject;
			foreach ($keys as $key) {
				if (is_numeric($key)) $has_number = true;
				if (is_string($key)) $has_string = true;
			}
			if (isset($has_number) and !isset($has_string)) {
				foreach ($arg_array as $key => $value) {
					$tmp[] = $this->array2object($value);
				}
			} elseif (isset($has_string)) {
				foreach ($arg_array as $key => $value) {
					if (is_string($key))
					$tmp->$key = $this->array2object($value);  //tgb
				}
			}
		} elseif (is_object($arg_array)) {
			foreach ($arg_array as $key => $value) {
				if (is_array($value) or is_object($value))
				$tmp->$key = $this->array2object($value);
				else
				$tmp->$key = $value;
			}
		} else {
			$tmp = $arg_array;
		}
		return $tmp; //return the object
	}

	/**
	 * Reindexes the whole array with ascending numbers
	 *
	 * @param array $array
	 * @return array
	 */
	function array_reindex($array) {
		if (is_array($array)) {
			if(count($array) == 1 && $array[0]) {
				return $this->array_reindex($array[0]);
			} else {
				foreach($array as $keys => $items) {
					if (is_array($items)) {
						if (is_numeric($keys)) {
							$array[$keys] = $this->array_reindex($items);
						} else {
							$array[$keys] = $this->array_reindex(array_merge(array(), $items));
						}
					}
				}
			}
		}
		return $array;
	}

	/**
	 * Parse the XML generation to array object
	 *
	 * @param array $array
	 * @return array
	 */
	function xml_reorganize($array) {
		$count = count($array);
		$repeat = $this->xml_tags($array);
		$repeatedone = false;
		$tags = array();
		$k = 0;
		for ($i = 0; $i < $count; $i++) {
			switch ($array[$i]['type']) {
				case 'open':
					array_push($tags, $array[$i]['tag']);
					if ($i > 0 && ($array[$i]['tag'] == $array[$i-1]['tag']) && ($array[$i-1]['type'] == 'close'))
						$k++;
					if (isset($array[$i]['value']) && ($array[$i]['value'] || !$this->skip_empty_values)) {
						array_push($tags, '@content');
						$this->array_insert(count($tags), $tags, $array[$i]['value'], "open");
						array_pop($tags);
					}
					if (in_array($array[$i]['tag'] . $array[$i]['level'], $repeat)) {
						if (($repeatedone == $array[$i]['tag'] . $array[$i]['level']) && ($repeatedone)) {
							array_push($tags, strval($k++));
						} else {
							$repeatedone = $array[$i]['tag'] . $array[$i]['level'];
							array_push($tags, strval($k));
						}
					}
					if (isset($array[$i]['attributes']) && $array[$i]['attributes'] && $array[$i]['level'] != $this->ignore_level) {
						array_push($tags, '@attributes');
						foreach ($array[$i]['attributes'] as $attrkey => $attr) {
							array_push($tags, $attrkey);
							$this->array_insert(count($tags), $tags, $attr, "open");  //aab
							array_pop($tags);
						}
						array_pop($tags);
					}
					break;
					
				case 'close':
					array_pop($tags);
					if (in_array($array[$i]['tag'] . $array[$i]['level'], $repeat)) {
						if ($repeatedone == $array[$i]['tag'] . $array[$i]['level']) {
							array_pop($tags);
						} else {
							$repeatedone = $array[$i + 1]['tag'] . $array[$i + 1]['level'];
							array_pop($tags);
						}
					}
					break;
					
				case 'complete':
					array_push($tags, $array[$i]['tag']);
					if (in_array($array[$i]['tag'] . $array[$i]['level'], $repeat)) {
						if ($repeatedone == $array[$i]['tag'] . $array[$i]['level'] && $repeatedone) {
							array_push($tags, strval($k));
						} else {
							$repeatedone = $array[$i]['tag'] . $array[$i]['level'];
							array_push($tags, strval($k));
						}
					}
					if (isset($array[$i]['value']) && ($array[$i]['value'] || !$this->skip_empty_values)) {
						if (isset($array[$i]['attributes']) && $array[$i]['attributes']) {
							array_push($tags, '@content');
							$this->array_insert(count($tags), $tags, $array[$i]['value'], "complete");
							array_pop($tags);
						} else {
							$this->array_insert(count($tags), $tags, $array[$i]['value'], "complete");
						}
					}
					if (isset($array[$i]['attributes']) && $array[$i]['attributes']) {
						array_push($tags, '@attributes');
						foreach ($array[$i]['attributes'] as $attrkey => $attr) {
							array_push($tags, $attrkey);
							$this->array_insert(count($tags), $tags, $attr, "complete");
							array_pop($tags);
						}
						array_pop($tags);
					}
					if (in_array($array[$i]['tag'] . $array[$i]['level'], $repeat)) {
						array_pop($tags);
						$k++;
					}
					array_pop($tags);
					break;
			}
		}
		eval($this->evalCode);
		$last = $this->array_reindex($this->result);
		return $last;
	}

	/**
	 * Get the XML contents from string and parse like SimpleXML
	 *
	 * @param string $data
	 * @param string $resulttype
	 * @param string $encoding
	 * @return array/object
	 */
	function xml_load_string($data, $resulttype = 'object', $encoding = 'UTF-8'){
		$php_errormsg="";
		$this->result="";
		$this->evalCode="";
		$values="";
		if (empty($data)){
			printf('Error:Xml string is empty');
			return false;
		}
		
		$parser = xml_parser_create($encoding);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		
		$ok = xml_parse_into_struct($parser, $data, $values);
		if (!$ok) {
			$errmsg = sprintf("XML parse error %d '%s' at line %d, column %d (byte index %d)",
			xml_get_error_code($parser),
			xml_error_string(xml_get_error_code($parser)),
			xml_get_current_line_number($parser),
			xml_get_current_column_number($parser),
			xml_get_current_byte_index($parser));
		}

		xml_parser_free($parser);
		if (!$ok)
		return $errmsg;

		$this->xml_array = $this->xml_reorganize($values);
		if ($resulttype == 'array')
			return $this->xml_array;
		// default $resulttype is 'object'
		return $this->array2object($this->xml_array);
	}

	/**
	 * Get the XML contents and parse like SimpleXML
	 *
	 * @param string $file
	 * @param string $resulttype
	 * @param string $encoding
	 * @return array/object
	 */
	function xml_load_file($file, $resulttype = 'object', $encoding = 'UTF-8') {
		$data = file_get_contents($file);
		if (!$data) {
			printf('Error: Cannot open xml document: ' . (isset($php_errormsg) ? $php_errormsg : $file));
			return false;
		}
		return $this->xml_load_string($data, $resulttype, $encoding);
	}

	function asXML($dom, $encoding = 'UTF-8') {
		$xml = '<?xml version="1.0" encoding="' . $encoding . '"?>';
		$result = $this->_asXML($dom, 'root', $encoding);
		if ($result) {
			$xml .= $result;
			return $xml;
		}
		return false;
	}

	function _asXML($tree, $name, $encoding = 'UTF-8') {
		if (!is_array($tree)) return "<$name>" . $this->my_htmlentities($tree) . "</$name>";
		$xml = '';
		$has_number = false;
		foreach ($tree as $k => $v) {
			if (is_numeric($k)) 
				$has_number = true;
		}
		if ($has_number) {
			foreach ($tree as $v) {
				$xml .= $this->_asXML($v, $name, $encoding);  // akb
			}
		} else {
			$xml .= "<$name";
			if (array_key_exists('@attributes', $tree)) {
				foreach ($tree['@attributes'] as $k => $v) {
					$xml .= (' ' . $k . '="' . $this->my_htmlentities($v) . '"');
				}
				unset($tree['@attributes']);
			}
			$xml .= ">";
			if (array_key_exists('@content', $tree)) {
				$xml .= $this->my_htmlentities($tree['@content']);
				unset($tree['@content']);
			} else {
				foreach ($tree as $k => $v) {
					$xml .= $this->_asXML($v, $k, $encoding);
				}
			}
			$xml .= "</$name>";
		}
		return $xml;
	}

	function my_htmlentities($s) {
		return str_replace(array('&', '<', '>', '"', "'"), array('&#38;','&#60;','&#62;','&#34;','&#39;'), $s);
	}
}
endif;
