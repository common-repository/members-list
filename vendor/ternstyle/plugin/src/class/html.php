<?php

namespace MLP\ternplugin;
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//found this class here: http://stackoverflow.com/questions/16583676/shorten-text-without-splitting-words-or-breaking-html-tags
class Html {

	protected
		$reachedLimit = false,
		$totalLen	= 0,
		$maxLen = 25,
		$toRemove	= [];

	public static function trim($html, $maxLen = 25,$a=''){
		$dom = new \DomDocument('1.0', 'utf-8');
		@$dom->loadHTML($html);

		$html = new static();
		$toRemove = $html->walk($dom, $maxLen,$a);

		// remove any nodes that passed our limit
		foreach($toRemove as $child) {
			$child->parentNode->removeChild($child);
		}

		// remove wrapper tags added by DD (doctype, html...)
		if(version_compare(PHP_VERSION, '5.3.6') < 0) {
			// http://stackoverflow.com/a/6953808/1058140
			$dom->removeChild($dom->firstChild);
			$dom->replaceChild($dom->firstChild->firstChild->firstChild, $dom->firstChild);
			return $dom->saveHTML();
		}

		return $dom->saveHTML($dom->getElementsByTagName('body')->item(0));
	}

	protected function walk(\DomNode $node, $maxLen,$a){

		if($this->reachedLimit) {
			$this->toRemove[] = $node;

		}
		else {
			// only text nodes should have text,
			// so do the splitting here
			if($node instanceof \DomText) {
				$this->totalLen += $nodeLen = strlen($node->nodeValue);

				// use mb_strlen / mb_substr for UTF-8 support
				if($this->totalLen > $maxLen) {
					$node->nodeValue = substr($node->nodeValue, 0, $nodeLen - ($this->totalLen - $maxLen));
					$node->nodeValue = preg_replace("/[^a-zA-Z0-9]+$/",'',$node->nodeValue) . '...';
					$this->reachedLimit = true;
				}

			}

			// if node has children, walk its child elements
			if(isset($node->childNodes)) {
				foreach($node->childNodes as $child) {
					$this->walk($child, $maxLen,$a);
				}
			}
		}

		return $this->toRemove;
	}
}

?>
