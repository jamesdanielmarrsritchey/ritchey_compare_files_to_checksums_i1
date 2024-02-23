<?php
#Name:Ritchey Compare Files To Checksums i1 v1
#Description:Hash all files in a directory (recursively), and compare them to checksums found in TXT files. Returns "TRUE" if all files match checksums. Returns an array of files if any don't match. Returns "FALSE" on failure.
#Notes:Optional arguments can be "NULL" to skip them in which case they will use default values. Files named 'sha256.txt' are not hashed.
#Arguments:'source' (required) is the folder containing the files to hash. 'hashing_algorithm' (optional) is the hashing algorithm to use. Valid values are 'sha256'. Default value is 'sha256'. 'display_errors' (optional) indicates if errors should be displayed.
#Arguments (Script Friendly):source:path:required,hashing_algorithm:string:optional,display_errors:bool:optional
#Content:
#<value>
if (function_exists('ritchey_compare_files_to_checksums_i1_v1') === FALSE){
function ritchey_compare_files_to_checksums_i1_v1($source, $hashing_algorithm = NULL, $display_errors = NULL){
	$errors = array();
	$location = realpath(dirname(__FILE__));
	if (@is_dir($source) === FALSE){
		$errors[] = 'destination';
	}
	if ($hashing_algorithm === NULL){
		$hashing_algorithm = 'sha256';
	} else if ($hashing_algorithm === 'sha256'){
		//Do nothing
	} else {
		$errors[] = "hashing_algorithm";
	}
	if ($display_errors === NULL){
		$display_errors = FALSE;
	} else if ($display_errors === TRUE){
		#Do Nothing
	} else if ($display_errors === FALSE){
		#Do Nothing
	} else {
		$errors[] = "display_errors";
	}
	##Task
	if (@empty($errors) === TRUE){
		###Get a list of all files
		$location = realpath(dirname(__FILE__));
		require_once $location . '/dependencies/ritchey_list_files_i1_v1/ritchey_list_files_i1_v1.php';
		$files = ritchey_list_files_i1_v1($source, FALSE);
		###For each file compare to the existing checksum (if there is one)
		$result = array();
		foreach ($files as &$item1){
			if (basename($item1) !== 'sha256.txt'){
				####If a checksum exists, extract it
				$checksums_file = '';
				$checksum = '';
				if ($hashing_algorithm === 'sha256'){
					$checksums_file = dirname($item1) . '/sha256.txt';
				}
				if (is_file($checksums_file) === TRUE){
					$location = realpath(dirname(__FILE__));
					require_once $location . '/custom_dependencies/ritchey_get_line_by_postfix_i1_v1/ritchey_get_line_by_postfix_i1_v1.php';
					$checksum = ritchey_get_line_by_postfix_i1_v1($checksums_file, basename($item1), FALSE);
					$checksum = explode(' ', $checksum);
					$checksum = $checksum[0];
				}
				####Compare checksums if possible
				if ($checksum !== ''){
					if ($hashing_algorithm === 'sha256'){
						$current_checksum = hash_file('sha256', $item1);
					}
					if ($checksum !== $current_checksum){
						$result[] = "{$item1},{$checksum} != {$current_checksum}";
					}
				} else {
					$result[] = "{$item1}";
				}
			} else {
				//
			}
		}
		unset($item1);
	}
	result:
	##Display Errors
	if ($display_errors === TRUE){
		if (@empty($errors) === FALSE){
			$message = @implode(", ", $errors);
			if (function_exists('ritchey_compare_files_to_checksums_i1_v1_format_error') === FALSE){
				function ritchey_compare_files_to_checksums_i1_v1_format_error($errno, $errstr){
					echo $errstr;
				}
			}
			set_error_handler("ritchey_compare_files_to_checksums_i1_v1_format_error");
			trigger_error($message, E_USER_ERROR);
		}
	}
	##Return
	if (@empty($errors) === TRUE){
		if (@empty($result) === TRUE){
			return TRUE;
		} else {
			return $result;
		}
	} else {
		return FALSE;
	}
}
}
#</value>
?>