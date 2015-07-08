<?php

// -----------------------------------------------------------------------
// This file is part of AROUNDMe
// 
// Copyright (C) 2003-2008 Barnraiser
// http://www.barnraiser.org/
// info@barnraiser.org
// 
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; see the file COPYING.txt.  If not, see
// <http://www.gnu.org/licenses/>
// -----------------------------------------------------------------------


class Plugin_common {

	function select_block($plugin, $name) {
		// We use a unique ID for each block on the page
		// imagine two listings; the first is listing 25-50 records and the second 75-100
		// We need to be able to tell each list which range it is to handle:
		// &from1=25&from2=75 where "1" is the first block on the page and "2" is the second;
		// hence IDs 1 and 2
		
		// We attempt to get the block from the database
		$query = "
			SELECT block_id, block_body 
			FROM " . $this->am_storage->prefix . "_block
			WHERE
			webspace_id=" . AM_WEBSPACE_ID . " AND
			block_plugin=" . $this->am_storage->qstr($plugin) . " AND
			block_name=" . $this->am_storage->qstr($name)
		;
		
		$result = $this->am_storage->Execute($query);
		
		if (isset($result[0]['block_id'])) {
			$block = $result[0];
		}
		else {
			// we need to create the block and store it in the database
			$block_name = $plugin . '_' . $name . '.block.php';

			$block_html = @file_get_contents('plugins/' . $plugin . '/source_blocks/'. $block_name);

			// compile language into block
			$block_lang = array();

			if (is_file('plugins/' . $plugin . '/language/'. AM_DEFAULT_LANGUAGE_CODE . '/block.lang.php')) {
				include('plugins/' . $plugin . '/language/'. AM_DEFAULT_LANGUAGE_CODE . '/block.lang.php');
			}

			if (defined('AM_LANGUAGE_CODE')) {
				if (is_file('plugins/' . $plugin . '/language/'. AM_LANGUAGE_CODE . '/block.lang.php')) {
					include('plugins/' . $plugin . '/language/'. AM_LANGUAGE_CODE . '/block.lang.php');
				}
			}
			
			foreach($block_lang as $lang_key => $lang_val):
				$block_key = "AM_BLOCK_LANGUAGE_" . strtoupper($lang_key);
				$block_html = str_replace($block_key, $lang_val, $block_html);
			endforeach;

			// We insert the block
			if (get_magic_quotes_gpc()) {
				$block_html = addslashes($block_html);
			}
			
			$rec = array();
			$rec['block_plugin'] = $plugin;
			$rec['block_name'] = $name;
			$rec['block_body'] = '';
			$rec['webspace_id'] = AM_WEBSPACE_ID;
			
			$table = $this->am_storage->prefix . "_block";
			
			$this->am_storage->insertDb($rec, $table);

			$block['block_id'] = $this->am_storage->insertID();
			
			// APPLY ID TO BLOCK AND SAVE
			$block['block_body'] = str_replace('AM_PLUGIN_KEYWORD_ID', $block['block_id'], $block_html);

			$query = "
				UPDATE " . $this->am_storage->prefix . "_block SET
				block_body=" . $this->am_storage->qstr($block['block_body']) . "
				WHERE
				block_id=" . $block['block_id']
			;
			
			$this->am_storage->Execute($query);

			$block['block_body'] = stripslashes($block['block_body']);
		}
		
		return $block;
	}
}

?>