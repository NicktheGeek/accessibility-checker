<?php

/**
 * Check for anchors with a target of _blank
 *
 * @param string $content
 * @param array $post
 * @return array
 */
function edac_rule_link_blank($content, $post){

	$content = $content['html'];
	$errors = [];
	$elements = $content->find('a[target="_blank"]');
	if($elements){
		foreach ($elements as $a) {

			$error = false;
			$error_code = $a->outertext;
			
			// check aria-label
			if($a->hasAttribute('aria-label')){

				$text = $a->getAttribute('aria-label');
				$error = edac_check_link_blank_text($text);

			// check aria-labelledby
			}elseif($a->hasAttribute('aria-labelledby')){
				
				// get aria-labelledby and explode into array since aria-labelledby allows for multiple element ids
				$label_string = $a->getAttribute('aria-labelledby');
				$labels = explode( ' ', $label_string );
				$label_text = [];
				
				if($labels){
					foreach( $labels as $label ) {

						// if element has text push into array
						$element = $content->find( '#' . $label, 0 );
						if($element->plaintext){
							$label_text[] = $element->plaintext;
						}
					}

					// implode array and check
					if($label_text){
						$text = implode(' ',$label_text);
						$error = edac_check_link_blank_text($text);
					}
				}
			
			// check plain text
			}elseif($a->plaintext){

				$text = $a->plaintext;
				$error = edac_check_link_blank_text($text);

			// check image alt text
			}else{
				$images = $a->find('img');
				foreach ($images as $image){
					$alt = $image->getAttribute('alt');
					$error = edac_check_link_blank_text($alt);
					if($error == true){
						break;
					}
				}
			}

			// push error code into array
			if($error == false){
				$errors[] = $error_code;
			}
			
		}
	}
	return $errors;

}

/**
 * Check for link blank text
 *
 * @param string $text
 * @return void
 */
function edac_check_link_blank_text($text){

	$text = strtolower($text);

	// phrases
	$allowed_phrases = [
		__('opens a new window','edac'),
		__('opens a new tab','edac'),
		__('opens new window','edac'),
		__('opens new tab','edac'),
	];

	// check if text contains any of the allowed phrases
	foreach ($allowed_phrases as $allowed_phrase) {
		if(strpos($text, $allowed_phrase) !== false){
			return true;
			break;
		}
	}

	return false;
	
}