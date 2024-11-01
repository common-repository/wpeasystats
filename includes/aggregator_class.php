<?php
define('AGGREGATORS_XML_TEMPLATE', dirname(__FILE__) . '/aggregators_template.xml');

require_once(dirname(__FILE__) . '/php5_functions.php');
require_once(dirname(__FILE__) . '/simplexml.php');

if(!class_exists('WPBL_Aggregator')):

class WPBL_Aggregator {

	var $dom;
	var $simple_xml;

	function init() {
	
		$aggregators_xml = file_get_contents(AGGREGATORS_XML_TEMPLATE);
		if (empty($aggregators_xml)) return false;
		$this->dom = $this->_parse_aggregators_xml($aggregators_xml);
		return true;
	}

	function get_all_aggregators() {
		return wpbl_to_array($this->dom['aggregator']);
	}

	function get_xml() {
		return file_get_contents(AGGREGATORS_XML_TEMPLATE);
	}

	function get_aggregator($id) {
		foreach ($this->get_all_aggregators() as $aggregator) {
			if (strcmp($aggregator['id'], $id) == 0) {
				return $aggregator;
			}
		}
		return false;
	}

	function reverse_status($id) {
		$aggregators = &$this->dom['aggregator'];
		foreach ($aggregators as $k => $aggregator) {
			if ($aggregator['id'] == $id) {
				if($aggregators[$k]['@attributes']['enabled'] == 'false') {
				}
				$aggregators[$k]['@attributes']['enabled'] = $aggregator['@attributes']['enabled'] == 'false' ? 'true' : 'false';
				return $this->_update();
			}
		}
		return false;
	}

	function _update() {
		$aggregators_xml = $this->simple_xml->asXML($this->dom);
		return file_put_contents(AGGREGATORS_XML_TEMPLATE, $aggregators_xml);
	}

	function _parse_aggregators_xml($aggregators_xml) {
		$this->simple_xml = new MySimpleXML();
		return $this->simple_xml->xml_load_string($aggregators_xml, 'array');
	}

	function get_aggregator_login_step($aggregator_id) {
		$aggregator = $this->get_aggregator($aggregator_id);
		foreach (wpbl_to_array($aggregator['step']) as $step) {  // lcr & ajb
			if (isset($step['url']['@attributes']['name']) && $step['url']['@attributes']['name'] == 'login') {
				return $step;
			}
		}
		return false;
	}

	function get_aggregator_add_feed_step($aggregator_id) {
		$aggregator = $this->get_aggregator($aggregator_id);
		foreach (wpbl_to_array($aggregator['step']) as $step) {
			if (isset($step['url']['@attributes']['name']) && $step['url']['@attributes']['name'] == 'addFeed') {
				return $step;
			}
		}
		return false;
	}

	function get_aggregator_step_by_order($aggregator_id, $order) {
		$aggregator = $this->get_aggregator($aggregator_id);
		foreach (wpbl_to_array($aggregator['step']) as $step) {
			if ($step['order'] == $order) {
				return $step;
			}
		}
		return false;
	}

	function add_aggregator($name, $enabled = 'false') {
		$id = strtoupper(substr(md5(strval(time())), 0, 5));
		$aggregator = array();
		$aggregator['@attributes']['enabled'] = $enabled;
		$aggregator['id'] = $id;
		$aggregator['name'] = trim($name);
		$this->dom['aggregator'][] = $aggregator;
		return $this->_update() ? $id : false;
	}

	function get_aggregator_steps($id) {
		$aggregator = $this->get_aggregator($id);
		if (!isset($aggregator['step'])) 
			return false;
		return wpbl_to_array($aggregator['step']);
	}

	function _add_a_url(&$step, $args, $i) {
		$url = array();
		$url['@content'] = trim($args['url'][$i]);
		$url['@attributes']['formIDType'] = trim($args['formIDType'][$i]);
		$url['@attributes']['formIDValue'] = trim($args['formIDValue'][$i]);
		if(isset($args['method'][$i])) {
			$url['@attributes']['method']= trim($args['method'][$i]);
		} else {
			$url['@attributes']['method']= "POST";
		}
		if ($args['order'][$i] == 1) {
			$url['@attributes']['name'] = 'login';
		} elseif ($args['order'][$i] == 2) {
			$url['@attributes']['name'] = 'addFeed';
		}
		$step['url'] = $url;
		$step['successRegx'] = trim(stripslashes($args['successRegx'][$i]));
	}

	function _add_a_step(&$aggregator, $args, $i) {
		if (strlen(trim($args['url'][$i])) == 0) 
			return;
		$step = array();
		$step['order'] = $args['order'][$i];
		$this->_add_a_url($step, $args, $i);
		$aggregator['step'][] = $step;
	}

	function set_aggregator_steps($args) {
		foreach (wpbl_to_array($this->dom['aggregator']) as $k => $ag) {
			if ($ag['id'] == $args['id']) {
				$aggregator = &$this->dom['aggregator'][$k];
				break;
			}
		}
		for ($i = 0; $i < count($args['order']); $i++) {
			foreach (wpbl_to_array($aggregator['step']) as $k => $st) {
				if ($st['order'] == $args['order'][$i]) {
					$step = &$aggregator['step'][$k];
					break;
				}
			}
			if (empty($step)) {
				$this->_add_a_step($aggregator, $args, $i);
			} else {
				if (strlen(trim($args['url'][$i])) == 0) {
					for ($k = 0; $k < count($aggregator['step']); $k++) {
						if ($aggregator['step'][$k]['order'] == $args['order'][$i]) {
							unset($aggregator['step'][$k]);
						}
					}
					continue;
				}
				// update this step, first remove url element, then add the new url elemnt
				unset($step['url']);
				unset($step['successRegx']);
				$this->_add_a_url($step, $args, $i);
			}
			unset($step);
		}
		return $this->_update();
	}

	function set_aggregator_name($id, $name) {
		foreach (wpbl_to_array($this->dom['aggregator']) as $k => $aggregator) {
			if ($aggregator['id'] == $id) {
				$this->dom['aggregator'][$k]['name'] = trim($name);
				return $this->_update();
			}
		}
		return false;
	}

	function set_aggregator_params($ag_id, $order_id, $args) {
		foreach (wpbl_to_array($this->dom['aggregator']) as $k => $aggregator) {
			if ($aggregator['id'] == $ag_id) {
				foreach (wpbl_to_array($this->dom['aggregator'][$k]['step']) as $j => $step) {
					if ($step['order'] == $order_id) {
					$step = &$this->dom['aggregator'][$k]['step'][$j];
					break;
					}
				}
			}
		}
		unset($step['params']);
		for ($param_index = 0; $param_index < count($args['paramName']); $param_index++) {
			for ($i = 0; $i < count($args['paramName'][$param_index]); $i++) {
				if (strlen(trim($args['paramName'][$param_index][$i])) != 0) {
					$param = array();
					$param['name'] = trim($args['paramName'][$param_index][$i]);
					$param['value'] = htmlspecialchars(($args['paramValue'][$param_index][$i]));
					$param['@attributes']['type'] = trim($args['paramType'][$param_index][$i]);
					$step['params'][$param_index]['param'][] = $param;
				}
			}
		}
		return $this->_update();
	}

	function set_parameter($aggregator_id, $param_field, $v) {
		foreach (wpbl_to_array($this->dom['aggregator']) as $k => $aggregator) {
			if ($aggregator['id'] == $aggregator_id) {
				foreach (wpbl_to_array($this->dom['aggregator'][$k]['step']) as $j => $step) {
					$step = &$this->dom['aggregator'][$k]['step'][$j];
					wpbl_to_array($step['params']);
					$params = &$step['params'];
					foreach ($params['param'] as $k => $param) {
						if ($param['name'] == $param_field) {
							$params['param'][$k]['value'] = $v;
							return $this->_update();
						}
					}
				}
			}
		}
	}

	function get_parameter($aggregator_id, $param_field) {
		foreach (wpbl_to_array($this->dom['aggregator']) as $k => $aggregator) {
			if ($aggregator['id'] == $aggregator_id) {
				foreach (wpbl_to_array($this->dom['aggregator'][$k]['step']) as $j => $step) {
					$step = &$this->dom['aggregator'][$k]['step'][$j];
					wpbl_to_array($step['params']);
					$params = &$step['params'];
					foreach ($params['param'] as $k => $param) {
						if ($param['name'] == $param_field) {
							return $params['param'][$k]['value'] ;
						}
					}
				}
			}
		}
	}

	function delete_aggregator($id) {
		foreach (wpbl_to_array($this->dom['aggregator']) as $k => $aggregator) {
			if ($aggregator['id'] == $id) {
				unset($this->dom['aggregator'][$k]);
				return $this->_update();
			}
		}
		return false;
	}

	function is_ready($id) {
		$login_step = $this->get_aggregator_login_step($id);
		if (empty($login_step)) return true; // login not required for this aggregator
		wpbl_to_array($login_step['params']);
		$params = $login_step['params'][0]['param'];
		if (empty($params)) 
			return false; // No parameter was entered
		foreach (wpbl_to_array($params) as $param) {
			if (!isset($param['value']) || strlen(trim($param['value'])) == 0) return false; // One of the parameters not entered
		}
		return true;
	}

	function get_aggregator_param($aggregator_id, $order, $param_index = 0) {
		$params = $this->get_aggregator_params($aggregator_id, $order);
		if (empty($params)) return false;
		if (isset($params[$param_index])) {
			return $params[$param_index]['param'];
		}
		return false;
	}

	function get_aggregator_params($aggregator_id, $order) {
		$aggregator = $this->get_aggregator($aggregator_id);
		foreach (wpbl_to_array($aggregator['step']) as $step) {
			if ($step['order'] == $order) {
				if (!isset($step['params'])) 
					return false;
				return wpbl_to_array($step['params']);
			}
		}
		return false;
	}

	function add_a_profile($ag_id, $order_id) {
		foreach (wpbl_to_array($this->dom['aggregator']) as $k => $aggregator) {
			if ($aggregator['id'] == $ag_id) {
				foreach (wpbl_to_array($this->dom['aggregator'][$k]['step']) as $j => $step) {
					if ($step['order'] == $order_id) {
						$step = &$this->dom['aggregator'][$k]['step'][$j];
						wpbl_to_array($step['params']);
						$params = &$step['params'];
						$params[] = array('param' => array(
						array('@attributes' => array('type' => 'value'), 'name' => $params[0]['param'][0]['name'], 'value' => ' '),
						array('@attributes' => array('type' => 'value'), 'name' => $params[0]['param'][1]['name'], 'value' => ' ')
						));
						return $this->_update();
					}
				}
			}
		}
	}

	function delete_a_profile($ag_id, $order_id, $param_index) {
		foreach (wpbl_to_array($this->dom['aggregator']) as $k => $aggregator) {
			if ($aggregator['id'] == $ag_id) {
				foreach (wpbl_to_array($this->dom['aggregator'][$k]['step']) as $j => $step) {
					if ($step['order'] == $order_id) {
						$step = &$this->dom['aggregator'][$k]['step'][$j];
						wpbl_to_array($step['params']);
						$params = &$step['params'];
						unset($params[$param_index]);
						return $this->_update();
					}
				}
			}
		}
	}

	function get_enablecount() {
		$count=0;
		$aggregators=$this->get_all_aggregators();
		foreach($aggregators as $k => $aggregator) {
			if ($aggregators[$k]['@attributes']['enabled']=='true') {
				$count = $count+1;
			}
		}
		return $count;
	}

}

endif;
