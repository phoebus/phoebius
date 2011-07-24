<?php
/* ***********************************************************************************************
 *
 * Phoebius Framework
 *
 * **********************************************************************************************
 *
 * Copyright (c) 2011 Scand Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU Lesser General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses/>.
 *
 ************************************************************************************************/

class HtmlUtil
{	
	static function getTagCap($tag, array $attributes = array())
	{
		return self::getTagHead($tag, $attributes) . '>';
	}
	
	static function getNode($tag, array $attributes = array())
	{
		return self::getTagHead($tag, $attributes) . ' />';
	}
	
	static function getContainer($tag, array $attributes = array(), $innerHtml = '')
	{
		return self::getTagCap($tag, $attributes) . $innerHtml . '</' . $tag . '>';
	}

	private static function getTagHead($tag, array $attributes)
	{
		$s = "<$tag";
		$a = array();
		foreach ($attributes as $k => $v) {
			$a[] = $k.'="'.htmlspecialchars($v).'"';
		}
		$a = join(' ', $a);
		if ($a) $s .= ' ' .$a;
		
		return $s;
	}
}

?>