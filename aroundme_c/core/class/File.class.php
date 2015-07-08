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

class File {
	
	var $path = '';
	
	
	// sebastian öblom, 2007-09-27
	function File($db, $config) {
	
		$this->db = $db;
		$this->config = $config;
		$this->allowable_mime_types = $this->config['mime'];
		$this->thumbnail_width = $this->config['thumbnail']['width'];
		$this->thumbnail_height = $this->config['thumbnail']['height'];
	}
	
	function selFiles($file_name=null, $type=null, $limit=null, $order=null) {
		$query = "
			SELECT *
			FROM " . $this->db->prefix . "_file
			WHERE webspace_id=" . AM_WEBSPACE_ID;
		
		if (isset($type) && !empty($type)) {
		
			$types = "";
			foreach($type as $t) {
				$types .= $this->db->qstr($t) . ",";
			}
			$types = trim($types, ',');
			
			$query .= " AND file_type IN (" . $types . ")";
		}
		
		if (isset($file_name) && !empty($file_name)) {
			$query .= " AND file_name=" . $this->db->qstr($file_name);
		}
		
		if (isset($order) && !empty($order)) {
			$query .= " ORDER BY " . trim($order);
		}
		
		if (isset($limit) && !empty($limit)) {
			$query .= " LIMIT " . trim($limit);
		}

		$result = $this->db->Execute($query);
		foreach($this->thumbnail_width as $w) {
			foreach($result as $key => $val) {
				
				if (in_array($val['file_type'], array('image/gif', 'image/jpeg', 'image/png'))) {
					
					$tmp = strrpos($val['file_name'], '.');
					if ($tmp) {
						$prefix = substr($val['file_name'], 0, $tmp);
						$suffix = substr($val['file_name'], $tmp);
						$name_new = $prefix . '_' . $w . $suffix;
					}
					else {
						$name_new = $val['file_name'] . '_' . $w;
					}
					
					$result[$key]['thumb_' . $w] = $name_new;
				}
			}
		}
		return $result;
	}
	
	function uploadFile() {
		if (!isset($_FILES['frm_file']) || empty($_FILES['frm_file']['tmp_name'])) {
			$GLOBALS['am_error_log'][] = array('file_not_set');
		}

		// find out thie mime-type
		if (function_exists('finfo_open')) {
			$resource = finfo_open(FILEINFO_MIME);
			$mime_type = finfo_file($resource, $_FILES['frm_file']['tmp_name']);
			finfo_close($resource);
		}
		elseif (function_exists('mime_content_type')) {
			$mime_type = mime_content_type($_FILES['frm_file']['tmp_name']);
		}
		else {
			$mime_type = $_FILES['frm_file']['type'];
		}

		// We use this to map IE-mimetype to standard mimetype
		$mime_map = array(array("from" => "image/pjpeg", "to" => "image/jpeg"));

		foreach($mime_map as $i):
			if ($i['from'] == $mime_type) {
				$mime_type = $i['to'];
			}
		endforeach;

		// Is the mime-type allowed?
		if (!$this->validateMimeType($this->allowable_mime_types, $mime_type)) {
			$GLOBALS['am_error_log'][] = array('not_valid_mime');
		}
		
		if (empty($GLOBALS['am_error_log'])) {
			$destination = $this->config['dir'] . AM_WEBSPACE_ID . '/';
			
			$stamp = microtime();
			$filename = $this->generateFileName(); 
			
			if (!is_dir($destination)) {
			//echo $destination;
				$oldumask = umask(0);
				@mkdir ($destination, 0770, 1);
				umask($oldumask);
			}
			
			if (@move_uploaded_file($_FILES['frm_file']['tmp_name'], $destination . $filename)) {

				if ($mime_type == "image/gif" || $mime_type == "image/jpeg" || $mime_type == "image/png") {
					$image_size = getimagesize($destination . $filename);
					
					// we create a file
					$type  = explode('/', $mime_type);
					$imagecreatefrom = 'imagecreatefrom' . $type[1];
					$image           = 'image' . $type[1];
					$new_image   = $imagecreatefrom($destination . $filename);
					
					foreach($this->thumbnail_width as $key => $t) {
						
						$tmp = strrpos($filename, '.');
						if ($tmp) {
							$prefix = substr($filename, 0, $tmp);
							$suffix = substr($filename, $tmp);
							$filename_new = $prefix . '_' . $t . $suffix;
						}
						else {
							$filename_new = $filename . '_' . $t;
						}
					
						if ($image_size[0] >= $image_size[1]) { // width > height
							// scale the image to new height
							$height = $this->thumbnail_height[$key];
							$width = $image_size[0] * ($height / $image_size[1]);

							$blank_image = ImageCreateTrueColor($width, $height);
							$col         = imagecolorallocate($blank_image, 255, 255, 255);
							imagefilledrectangle($blank_image, 0, 0, $width, $height, $col);
							$newimage    = ImageCopyResampled($blank_image, $new_image, 0, 0, 0, 0, $width, $height, $image_size[0], $image_size[1]);
							$image($blank_image, $destination . $filename);
							$new_image_2 = $imagecreatefrom($destination . $filename);
							$blank_image = ImageCreateTrueColor($this->thumbnail_width[$key], $this->thumbnail_height[$key]);
							$col         = imagecolorallocate($blank_image, 255, 255, 255);
							imagefilledrectangle($blank_image, 0, 0, $this->thumbnail_width[$key], $this->thumbnail_height[$key], $col);
							$newimage    = imagecopy($blank_image, $new_image_2, 0, 0, ($width - $this->thumbnail_width[$key]) / 2, 0, $this->thumbnail_width[$key], $this->thumbnail_height[$key]);
							@unlink($destination . $filename);
							$image($blank_image, $destination . $filename_new);
						}
						else {
							// scale the image to new width
							$width = $this->thumbnail_width[$key];
							$height = $image_size[1] * ($width / $image_size[0]);

							$blank_image = ImageCreateTrueColor($width, $height);
							$col         = imagecolorallocate($blank_image, 255, 255, 255);
							imagefilledrectangle($blank_image, 0, 0, $width, $height, $col);
							$newimage    = ImageCopyResampled($blank_image, $new_image, 0, 0, 0, 0, $width, $height, $image_size[0], $image_size[1]);
							$image($blank_image, $destination . $filename);
							$new_image_2 = $imagecreatefrom($destination . $filename);
							$blank_image = ImageCreateTrueColor($this->thumbnail_width[$key], $this->thumbnail_height[$key]);
							$col         = imagecolorallocate($blank_image, 255, 255, 255);
							imagefilledrectangle($blank_image, 0, 0, $this->thumbnail_width[$key], $this->thumbnail_height[$key], $col);
							$newimage    = imagecopy($blank_image, $new_image_2, 0, 0, 0,($height - $this->thumbnail_height[$key]) / 2 , $this->thumbnail_width[$key], $this->thumbnail_height[$key]);
							@unlink($destination . $filename);
							$image($blank_image, $destination . $filename_new);
						}
					}
					
					if (isset($this->width)) {
						if (is_numeric($this->width)) {
							$width = $this->width;
							$height = $image_size[1] * ($width / $image_size[0]);
    			
							//$new_image   = $imagecreatefrom($destination . $filename);
							$blank_image = ImageCreateTrueColor($width, $height);
							$col         = imagecolorallocate($blank_image, 255, 255, 255);
							imagefilledrectangle($blank_image, 0, 0, $width, $height, $col);
							$newimage    = ImageCopyResampled($blank_image, $new_image, 0, 0, 0, 0, $width, $height, $image_size[0], $image_size[1]);
							@unlink($destination . $filename);
							$image($blank_image, $destination . $filename);
						}
					}
					else {
						$image($new_image, $destination . $filename);
					}
				}
				
				$rec = array();
				$rec['file_type'] = $mime_type;
				$rec['file_size'] = $_FILES['frm_file']['size'];
				$rec['file_name'] = $filename;
				$rec['webspace_id'] = AM_WEBSPACE_ID;
				$rec['connection_id'] = $_SESSION['connection_id'];
				$rec['file_create_datetime'] = time();
				
				if (isset($this->title)) {
					$rec['file_title'] = $this->title;
				}
				else {
					$rec['file_title'] = $filename;
				}
				
				$this->db->insertDb($rec, $this->db->prefix . '_file');
			}
			else {
				$GLOBALS['am_error_log'][] = array('file_not_uploaded');
			}
		}
	}
	
	function deleteFile($filename) {
		if (!empty($filename)) {
			$query = "
				DELETE 
				FROM " . $this->db->prefix . "_file
				WHERE webspace_id=" . AM_WEBSPACE_ID . "
				AND file_name=" . $this->db->qstr($filename) . ""
			;
	
			if ($this->db->Execute($query)) {
				@unlink($this->config['dir'] . AM_WEBSPACE_ID . '/' . $filename);
				foreach($this->thumbnail_width as $t) {
	
					$tmp = strrpos($filename, '.');
					if ($tmp) {
						$prefix = substr($filename, 0, $tmp);
						$suffix = substr($filename, $tmp);
						$tmp_name = $prefix . '_' . $t . $suffix;
					}
					else {
						$tmp_name = $filename . '_' . $t;
					}
					
					@unlink($this->config['dir'] . AM_WEBSPACE_ID . '/' . $tmp_name);
				}
			}
		}
	}
	
	function selAllocation() {
		
	}
	
	function validateMimeType($mimes, $mime_type) {
		foreach($mimes as $m) {
			if ($m['mime'] == $mime_type) {
				return 1;
			}
		}
		return 0;
	}
	
	function generateFileName($i=0) {
		
		$filename = $_FILES['frm_file']['name'];
		
		if ($i != 0) {
			$tmp = strrpos($filename, '.');
			if ($tmp) {
				$prefix = substr($filename, 0, $tmp);
				$suffix = substr($filename, $tmp);
				$filename = $prefix . $i . $suffix;
			}
			else {
				$filename = $filename . $i;
			}
		}
		
		if (is_file($this->config['dir'] . AM_WEBSPACE_ID . '/' . $filename)) {
			return $this->generateFileName($i+1);
		}
		return $filename;
	}
}

?>