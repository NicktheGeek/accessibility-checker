<?php

function edac_rule_aria_hidden($content, $post){

	$dom = $content['html'];
	$errors = [];
	$elements = $dom->find('[aria-hidden="true"]');

	if($elements){
		foreach ($elements as $element) {
			
			if(stristr($element->getAttribute('class'), 'wp-block-spacer')) continue;
			
			$errors[] = $element;
		}
	}

	return $errors;
}