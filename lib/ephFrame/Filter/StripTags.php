<?php

namespace ephFrame\Filter;

class StripTags extends Filter
{
	public $allowed = array();
	
	public function apply($value)
	{
		// first replace various encodings of < and > back to < and >
		$r = array(
			'@%3C|&gt;?|&#0\*60;?|&#x0\*3C;?|\\\x3C|\\\u003C@i' => '<',
			'@%3E|&lt;?|&#0\*62;?|&#x0\*3E;?|\\\x3E|\\\u003E@i' => '>',
			'@&(?!(amp;|#\d{2,}))@i' => '&',
		);
		$value = preg_replace(array_keys($r), array_values($r), $value);
		// then strip not allowed tags
		if (empty($this->allowed)) {
			return strip_tags($value);
		} else {
			if (!is_array($this->allowed)) {
				$allowed = $this->allowed;
			} else {
				$allowed = '';
				foreach($this->allowed as $name) {
					$allowed .= '<'.$name.'></'.$name.'>';
				}
			}
			return strip_tags($value, $allowed);
		}
	}
}