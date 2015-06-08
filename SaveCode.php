<?php

include 'OutputConfig.php';
include 'CodeChecker.php';
include 'OutputHandler.php';
include 'InputHandler.php';
include 'InputConfig.php';


	if(!empty($_POST['data'])){
		$data = $_POST['data'];
		$zend = $_POST['zend'];
		$hhvm = $_POST['hhvm'];
		$hippyvm = $_POST['hippyvm'];
		$hack = $_POST['hack'];
		$mali = false;
		if(preg_match('/eval\((base64|eval|\$_|\$\$|\$[A-Za-z_0-9\{]*(\(|\{|\[))/i', $data)) {
			$zend = "false";
			$hhvm = "false";
			$hippyvm = "false";
			$hack = "false";
			$mali = true;
		}

		$start_tag = $_POST['start'];

		$ip = getIP();
		$fname_top = $ip . "code.php";

		$data = addUtilCode($data, $ip);
		$hippyvm_data = addUtilCodeHippyVM($data);

		$zend_out = NULL;
		$zend_time = NULL;

		if ($zend === "true") {
			$fname = "/home/tmp/zend/" . $fname_top;	
			$file = fopen($fname, 'w');
			fwrite($file, "<?php \n" . $data . " \n?>" );
			fclose($file);

			exec("schroot -c secondjail -- php $fname 2>&1", $exec_out_zend, $zend_exit_code);
			$exec_out_zend = array_slice($exec_out_zend, 2); 
			//$exec_out_zend = array_filter($exec_out_zend, "checkEmpty");

			
			if ($zend_exit_code == 0) {
				$zend_time = array_pop($exec_out_zend);	
			} else {
				$exec_out_zend = array_filter($exec_out_zend, "checkEmpty");
			}
			
			$length = count($exec_out_zend);
			for ($i = 1; $i < $length * 2 - 1; $i += 2) {
    			array_splice($exec_out_zend, $i, 0, "<br>");
    		}
    		foreach ($exec_out_zend as $val) {
    			$zend_out = $zend_out . $val;
			}
			$zend_out = handle_output($zend_out, "zend", $fname_top, $start_tag, $ip, $O_ZEND, $O_UTIL, $zend_exit_code);
			



			/*if ($zend_exit_code == 0) {
				for ($i = 0; $i < count($exec_out_zend) - 1; ++$i) {
					if ($i == 0) {
						$zend_out = $exec_out_zend[0];
					} else {
						$zend_out = $zend_out . "<br>" . $exec_out_zend[$i];
					}
				}
				$zend_time = $exec_out_zend[count($exec_out_zend) - 1] . "s";
			} else {
				//$zend_out = $exec_out_zend[count($exec_out_zend) - 1];
				//$zend_time = NULL;

				for ($i = 0; $i < count($exec_out_zend); ++$i) {
					if (strcmp($exec_out_zend[$i], "") != 0) {
						if ($i == 0) {
							$zend_out = $exec_out_zend[0];
						} else {
							$zend_out = $zend_out . "<br>" . $exec_out_zend[$i];
						}
					}
				}*/
				/*$is_first_zend = true;
				for ($i = 0; $i < count($exec_out_zend); ++$i) {
					if (strcmp($exec_out_zend[$i], "") != 0) {
                		if ($is_first_zend) {
                     		$zend_out = $exec_out_zend[$i];
                     		$is_first_zend = false;
                		} else {
                     		$zend_out = $zend_out . "<br>" . $exec_out_zend[$i];
                		}
					}
				}*/
				/*while (strpos($zend_out, "/var/www/html/website/tmp/") !== false) {
					$zend_out = errorPrinter($zend_out);
				}
				$zend_out = fixLineNumbers($zend_out, $start_tag);
				$zend_time = NULL;*/
			//}

			

			//`rm $fname`;
		}

		$hhvm_out = NULL;
		$hhvm_time = NULL;

		if ($hhvm === "true") {
			$fname = "/home/tmp/hhvm/" . $fname_top;	
			$file = fopen($fname, 'w');
			fwrite($file, "<?php \n" . $data . " \n?>" );
			fclose($file);

			exec("schroot -c secondjail -- hhvm $fname 2>&1", $exec_out_hhvm, $hhvm_exit_code);
			$exec_out_hhvm = array_slice($exec_out_hhvm, 2); 

			if ($hhvm_exit_code == 0) {
				$hhvm_time = array_pop($exec_out_hhvm);	
			} else {
				$exec_out_hhvm = array_filter($exec_out_hhvm, "checkEmpty");
			}
			
			$length = count($exec_out_hhvm);
			for ($i = 1; $i < $length * 2 - 1; $i += 2) {
    			array_splice($exec_out_hhvm, $i, 0, "<br>");
    		}
    		foreach ($exec_out_hhvm as $val) {
    			$hhvm_out = $hhvm_out . $val;
			}
			$hhvm_out = handle_output($hhvm_out, "hhvm", $fname_top, $start_tag, $ip, $O_HHVM, $O_UTIL, $hhvm_exit_code);

			/*if ($hhvm_exit_code == 0) {
				for ($i = 0; $i < count($exec_out_hhvm) - 1; ++$i) {
					if ($i == 0) {
						$hhvm_out = $exec_out_hhvm[0];
					} else {
						$hhvm_out = $hhvm_out . "<br>" . $exec_out_hhvm[$i];
					}
				}
				$hhvm_time = $exec_out_hhvm[count($exec_out_hhvm) - 1] . "s";
			} else {
				//$hhvm_out = $exec_out_hhvm[count($exec_out_hhvm) - 1];
				//$hhvm_time = NULL;

				/*for ($i = 0; $i < count($exec_out_hhvm); ++$i) {
					if (strcmp($exec_out_hhvm[$i], "") != 0) {
						if ($i == 0) {
							$hhvm_out = $exec_out_hhvm[0];
						} else {
							$hhvm_out = $hhvm_out . "<br>" . $exec_out_hhvm[$i];
						}
					}
				}*/
			/*	$is_first_hhvm = true;
				for ($i = 0; $i < count($exec_out_hhvm); ++$i) {
					if (strcmp($exec_out_hhvm[$i], "") != 0) {
                		if ($is_first_hhvm) {
                     		$hhvm_out = $exec_out_hhvm[$i];
                     		$is_first_hhvm = false;
                		} else {
                     		$hhvm_out = $hhvm_out . "<br>" . $exec_out_hhvm[$i];
                		}
					}
				}*/


				/*while (strpos($hhvm_out, "/var/www/html/website/tmp/") !== false) {
					$hhvm_out = errorPrinter($hhvm_out);
				}
				$hhvm_out = fixLineNumbers($hhvm_out, $start_tag);*/
			//	$hhvm_time = NULL;
			//}

			//$hhvm_out = $exec_out_hhvm;

			//`rm $fname`;
		}

		$hippyvm_out = NULL;
		$hippyvm_time = NULL;

		if ($hippyvm === "true") {
			$fname = "/home/tmp/hippyvm/" . $fname_top;	
			$file = fopen($fname, 'w');
			fwrite($file, "<?php \n" . $hippyvm_data . " \n?>" );
			fclose($file);

			exec("schroot -c secondjail -- /usr/src/hippyvm/hippy-c $fname 2>&1", $exec_out_hippyvm, $hippyvm_exit_code);
			$exec_out_hippyvm = array_slice($exec_out_hippyvm, 2); 
			
			if ($hippyvm_exit_code == 0 && $exec_out_hippyvm[count($exec_out_hippyvm) - 1] === 'success_EOF_exit_0') {
				array_pop($exec_out_hippyvm);	
				$hippyvm_time = array_pop($exec_out_hippyvm);
			} else {
				$exec_out_hippyvm = array_filter($exec_out_hippyvm, "checkEmpty");
			}
			$exec_out_hippyvm = handle_hippyvm_special($exec_out_hippyvm);
			
			$length = count($exec_out_hippyvm);
			for ($i = 1; $i < $length * 2 - 1; $i += 2) {
    			array_splice($exec_out_hippyvm, $i, 0, "<br>");
    		}
    		foreach ($exec_out_hippyvm as $val) {
    			$hippyvm_out = $hippyvm_out . $val;
			}
			$hippyvm_out = handle_output($hippyvm_out, "hippyvm", $fname_top, $start_tag, $ip, $O_HIPPYVM, $O_UTIL, $hippyvm_exit_code);

			/*if ($hippyvm_exit_code == 0) {
				for ($i = 0; $i < count($exec_out_hippyvm) - 1; ++$i) {
					if ($i == 0) {
						$hippyvm_out = $exec_out_hippyvm[0];
					} else {
						$hippyvm_out = $hippyvm_out . "<br>" . $exec_out_hippyvm[$i];
					}
				}
				$hippyvm_time = $exec_out_hippyvm[count($exec_out_hippyvm) - 1] . "s";
			} else {*/
				//$hippyvm_out = $exec_out_hippyvm[count($exec_out_hippyvm) - 1];
				//$hippyvm_time = NULL;

				/*for ($i = 0; $i < count($exec_out_hippyvm); ++$i) {
					if (strcmp($exec_out_hippyvm[$i], "") != 0) {
						if ($i == 0) {
							$hippyvm_out = $exec_out_hippyvm[0];
						} else {
							$hippyvm_out = $hippyvm_out . "<br>" . $exec_out_hippyvm[$i];
						}
					}
				}*/
				/*if (strpos($exec_out_hippyvm[0], "In function") !== false) {
					$exec_out_hippyvm[0] = "";
				}*/
				/*$is_first_hippyvm = true;
				for ($i = 0; $i < count($exec_out_hippyvm); ++$i) {
					if (strcmp($exec_out_hippyvm[$i], "") != 0) {
                		if ($is_first_hippyvm) {
                     		$hippyvm_out = $exec_out_hippyvm[$i];
                     		$is_first_hippyvm = false;
                		} else {
                     		$hippyvm_out = $hippyvm_out . "<br>" . $exec_out_hippyvm[$i];
                		}
					}
				}*/
				/*while(strpos($hippyvm_out, "tmp/hippyvm") !== false) {
					$hippyvm_out = errorPrinterHippyvm($hippyvm_out);
				}
				$hippyvm_out = errorPrinterHippyvmCaseTrace($hippyvm_out);
				/if (strpos($hippyvm_out, "Parse error") !== false) {
					$hippyvm_out = "Parse error";
				} else {
					$hippyvm_out = rtrim($hippyvm_out, ":");
				}*/
				//$hippyvm_time = NULL;
			//}//

			//$hippyvm_out = $exec_out_hippyvm;
			//`rm $fname`;
		}

		$hack_out = NULL;
		$hack_time = NULL;

		if ($hack === "true") {
			$fname = "/home/tmp/hack/" . $fname_top;	
			$file = fopen($fname, 'w');
			fwrite($file, "<?hh \n" . $data);
			fclose($file);

			exec("schroot -c secondjail -- hhvm $fname 2>&1", $exec_out_hack, $hack_exit_code);
			$exec_out_hack = array_slice($exec_out_hack, 2);
			
			if ($hack_exit_code == 0) {
				$hack_time = array_pop($exec_out_hack);	
			} else {
				$exec_out_hack = array_filter($exec_out_hack, "checkEmpty");
			}
			
			$length = count($exec_out_hack);
			for ($i = 1; $i < $length * 2 - 1; $i += 2) {
    			array_splice($exec_out_hack, $i, 0, "<br>");
    		}
    		foreach ($exec_out_hack as $val) {
    			$hack_out = $hack_out . $val;
			}
			$hack_out = handle_output($hack_out, "hack", $fname_top, $start_tag, $ip, $O_HACK, $O_UTIL, $hack_exit_code);
			

			/*if ($hack_exit_code == 0) {
				for ($i = 0; $i < count($exec_out_hack) - 1; ++$i) {
					if ($i == 0) {
						$hack_out = $exec_out_hack[0];
					} else {
						$hack_out = $hack_out . "<br>" . $exec_out_hack[$i];
					}
				}
				$hack_time = $exec_out_hack[count($exec_out_hack) - 1] . "s";
			} else {*/
				//$hack_out = $exec_out_hack[count($exec_out_hack) - 1];
				//$hack_time = NULL;

				/*for ($i = 0; $i < count($exec_out_hack); ++$i) {
					if (strcmp($exec_out_hack[$i], "") != 0) {
						if ($i == 0) {
							$hack_out = $exec_out_hack[0];
						} else {
							$hack_out = $hack_out . "<br>" . $exec_out_hack[$i];
						}
					}
				}*/

				/*$is_first_hack = true;
				for ($i = 0; $i < count($exec_out_hack); ++$i) {
					if (strcmp($exec_out_hack[$i], "") != 0) {
                		if ($is_first_hack) {
                     		$hack_out = $exec_out_hack[$i];
                     		$is_first_hack = false;
                		} else {
                     		$hack_out = $hack_out . "<br>" . $exec_out_hack[$i];
                		}
					}
				}*/
				/*while (strpos($hack_out, "/var/www/html/website/tmp/") !== false) {
					$hack_out = errorPrinter($hack_out);
				}
				$hack_out = fixLineNumbers($hack_out, $start_tag);*/
			//	$hack_time = NULL;
			//}
			//$hack_out = $exec_out_hack;
			//`rm $fname`;
		}

		echo json_encode(array("is_mali"=>$mali, "zend_out"=>$zend_out, "zend_time"=>$zend_time,
							   "hhvm_out"=>$hhvm_out, "hhvm_time"=>$hhvm_time,
							   "hippyvm_out"=>$hippyvm_out, "hippyvm_time"=>$hippyvm_time,
							   "hack_out"=>$hack_out, "hack_time"=>$hack_time));

	}



	/*function errorPrinter($error_out) {
		$start = strpos($error_out, "/var/www/html/website/tmp/");
		$end = strlen($error_out);

		if (!$start) {
			return $error_out;
		}

		for ($i = $start; $i < strlen($error_out) - 1; ++$i) {
			if ($error_out{$i + 1} == ' ') {
				$end = $i;
				break;
			}
		}
		return substr($error_out, 0, $start - 3) . " ". substr($error_out, $end + 1, strlen($error_out));
	}

	function errorPrinterHippyvmCaseTrace($error_out) {
		$start = strpos($error_out, "RPython traceback:");
		$end = strpos($error_out, "...");
		if (!$start || !$end) {
			return $error_out;
		}
		return substr($error_out, 0, $start) . " ". substr($error_out, $end + 3, strlen($error_out));
	}

	function errorPrinterHippyvm($error_out) {
		$start = strpos($error_out, "tmp/hippyvm");
		$end = strlen($error_out);

		if (!$start) {
			return $error_out; 
		}

		for ($i = $start; $i < strlen($error_out) - 1; ++$i) {
			if ($error_out{$i + 1} == ' ') {
				$end = $i;
				break;
			}
		}
		return substr($error_out, 0, $start - 3) . " ". substr($error_out, $end + 1, strlen($error_out));
	}*/

	/*function fixLineNumbers($string, $buffer_exists) {
		$start = strpos($string, "on line") + 8;
		$end = strlen($string);

		if (!$start) {
			return $string;
		}

		for ($i = $start; $i < strlen($string) - 1; ++$i) {
			if ($string{$i + 1} == '<' || $string{i + 1} == ' ') {
				$end = $i;
				break;
			}
		}
		$actual_value = substr($string, $start, $end - $start + 1) - 4;
		if ($buffer_exists === "true") {
			$actual_value++;
		}

		$actual_string = substr($string, 0, $start) . $actual_value . substr($string, $end + 1, strlen($string));

		return $actual_string;
	}*/

	/*for ($i = 1; $i < count($array_1) * 2 - 1; $i += 2) {
    	array_splice($array, $i, 0, '##');
	}*/

	function getIP() {
    	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
 		   $ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
    		$ip = $_SERVER['REMOTE_ADDR'];
		}
		$ip = str_replace(":", "_", $ip);
		$ip = str_replace(".", "_", $ip);
		return $ip;
	}

	function addUtilCode($data, $ip) {
		$data = "$" . "time_before_$ip = microtime(true);\n" . $data;
		$data = "set_time_limit(3);\nini_set('memory_limit','64K');\n" . $data; 
		$data = $data . "\n$" . "time_after_$ip = microtime(true);\n"; 
		$data = $data . str_replace("n", "\n", "echo 'n';");
		$data = $data . "\necho printf('%.7f', " . "$" . "time_after_$ip - " . "$" . "time_before_$ip);\n";
		return $data;
	}

	function addUtilCodeHippyVM($data) {
		$data = $data . "\necho 'success_EOF_exit_0'\n";
		return $data;
	}

	function handle_hippyvm_special($exec_hippyvm) {
		for ($i = 0; $i < count($exec_hippyvm); ++$i) {
			if (strpos($exec_hippyvm[$i], "In function") !== false 
				|| strpos($exec_hippyvm[$i], "function <") !== false) {
				$exec_hippyvm[$i] = "";
			}
		}
		if (strpos($exec_hippyvm[count($exec_hippyvm) - 1], "E: Child terminated") !== false) {
			array_pop($exec_hippyvm);
		}
		return $exec_hippyvm;
	}	

	function substring($str, $start, $end) {
		return substr($str, $start, $end - $start);
	}

	function checkEmpty($var) {
    	return !empty($var);
	}
?>
