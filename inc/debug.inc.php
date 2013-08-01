<?php
	final class Debug
	{
		private static function _print_variable($variable_name, $variable, $writing_type)
		{
			if ($writing_type == "html")
			{$end_line = "<br />\n";}
			else
			{$end_line = "\n";}
			
			$description = print_r($variable, true);
			if ($description == "")
			{
				if (is_bool($variable))
				{$description = $variable ? "true" : "false";}
				$description .= "\n";
			}
			else if (!is_object($variable) && !is_array($variable))
			{$description .= "\n";}
	
			$result = ($writing_type == "html") ? "<pre>" : "";
			$variable_name .= " < " . gettype($variable) . " >";
			$result .= 	"---------- $variable_name ----------$end_line" .
						$description .
						"-----------";
			$variable_name_length = strlen($variable_name);
			for ($i = 0 ; $i < $variable_name_length ; ++$i)
			{$result .= "-";}
			$result .= "-----------";
			$result .= ($writing_type == "html") ? "<pre>" : "";
			return ($result);
		}
	
		public static function print_variable_data($variable_name, $variable)
		{
			$output = $_SESSION["debug"];
			switch ($output)
			{
				case "alert":
					echo(	"
							<script type=\"text/javascript\">
								window.alert(\"<<< DEBUG >>>\\n\" + " . json_encode(self::_print_variable($variable_name, $variable, $output)) . ");
							</script>
							"
						);
					break;
	
				case "console":
					echo(	"
							<script type=\"text/javascript\">
								if (typeof console != undefined)
								{console.log(\"<<< DEBUG >>>\\n\" + " . json_encode(self::_print_variable($variable_name, $variable, $output)) . " + \"\\n\\n\");}
								else
								{window.alert(\"Debug error : console not available.\");}
							</script>
							"
						);
					break;
	
				case "html":
					echo("<<< DEBUG >>>" . self::_print_variable($variable_name, $variable, $output) . "<<< DEBUG >>><br /><br />\n");
					break;
	
				case "file":
					$file = fopen("../logs/debug_log.txt", "abt") or die("Can't open file");
					fwrite($file, self::_print_variable($variable_name, $variable, $output));
					fclose($file);
					break;
	
				default:
					echo("<script type=\"text/javascript\">window.alert(\"Debug error : undefined output stream.\");</script>");
					break;			
			}
		}
			
		public static function print_message($message)
		{
			$output = $_SESSION["debug"];
			switch ($output)
			{
				case "alert":
					echo(	"
							<script type=\"text/javascript\">
								window.alert(\"<<< DEBUG >>>\\n\" + \"$message\");
							</script>
							"
						);
					break;
	
				case "console":
					echo(	"
							<script type=\"text/javascript\">
								if (typeof console != undefined)
								{console.log(\"<<< DEBUG >>>\\n$message\\n\\n\");}
								else
								{window.alert(\"Debug error : console not available.\");}
							</script>
							"
						);
					break;
	
				case "html":
					echo("&lt;&lt;&lt; DEBUG &gt;&gt;&gt; >>><br /><br />&lt;&lt;&lt; DEBUG &gt;&gt;&gt;<br /><br />\n");
					break;
		
					case "file":
					$file = fopen("../logs/debug_log.txt", "abt") or die("Can't open file");
					fwrite($file, $message);
					fclose($file);
					break;
	
				default:
					echo("<script type=\"text/javascript\">window.alert(\"Debug error : undefined output stream.\");</script>");
					break;
			}
		}
	
		public static function erase_debug_file()
		{unlink("debug_log.txt");}
	};
?>
