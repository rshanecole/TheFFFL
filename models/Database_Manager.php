<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	 * Database Manager Model.
	 *
	 * ?????
	 *		
	 */
	
Class Database_Manager extends CI_Model 
{
	public function __construct() 
    {
		parent::__construct();
		//$ci = get_instance();
		$this->load->helper('date');
		
		$this->NFL_db = $this->load->database('NFL',true);
	}
	
	/*Backup of specific table or tables. List of tables even if just one must be in array
	*/
	public function database_backup($table_name_array='All')
	{	
		if($table_name_array=='All'){
			$table_name_array = $this->db->list_tables();	
		}
    	foreach($table_name_array as $table)
		{
			if(!file_exists('/home1/theffflc/public_html/fantasy/data_backups/'.$table.'/')){
				mkdir ('/home1/theffflc/public_html/fantasy/data_backups/'.$table.'/');
			}
			$result = "";
		
			$query = $this->db->get($table);
			$num_fields = $query->num_fields();
			
        
			$num_row = $query->num_rows($query);
			
			foreach ($query->result_array() as $row_array)
			{
				$row = array_values($row_array);
				$result .= "INSERT DELAYED INTO ".$table." VALUES(";
				for($j=0; $j<$num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = preg_replace("/\n/","/\\n/",$row[$j]);
					if (isset($row[$j])) $result .= "\"$row[$j]\"" ; else $result .= "\"\"";
					if ($j<($num_fields-1)) $result .= ",";
				}    
				$result .= "); \n";
				
			}//while row
			$result .= "\n\n\n";
			$time = mdate('%Y-%m-%d.%H%i%s');

			$file_name = $table."_Backup_".$time.".sql";
			
			$fileRoot = '/home1/theffflc/public_html/fantasy/data_backups/'.$table.'/'.$file_name;
			if (!$handle = fopen($fileRoot, 'x')) {
				 echo "Cannot create file ($file_name).\n ";
				 exit;
			}
			
			// Write result data to our opened file.
			if (fwrite($handle, $result) === FALSE) {
			   echo "Cannot write to file ($file_name).\n ";
			   exit;
			}
			
			fclose($handle);
			
			//delete old files (greater than a week but keep at least 5
			// Grab all files from the desired folder
			$files = glob('/home1/theffflc/public_html/fantasy/data_backups/'.$table.'/*.*');
			$file_count = 0;
			
			
			$file_count = count($files);
			
			// Sort files by modified time, latest to earliest
			// Use SORT_ASC  for earliest to latest
			array_multisort(
				array_map('filemtime', $files),
				SORT_ASC,
				$files
			);
			
			while($file_count>5)
			{
				
				if((time()-filemtime($files[0]))>7*24*60*60)//one week
				{
					unlink($files[0]); // the latest modified file should be the first.
					array_splice($files,0,1);
				}
				$file_count--;
			}
		}//foreach table
		
		
		
	}//database backup

//***********************************************************************************	
	
	public function NFL_database_backup($table_name_array='All')
	{	
		if($table_name_array=='All'){
			$table_name_array = $this->NFL_db->list_tables();	
		}
    	foreach($table_name_array as $table)
		{
			
			if(!file_exists('/home1/theffflc/public_html/fantasy/data_backups/NFL/'.$table.'/')){
				mkdir ('/home1/theffflc/public_html/fantasy/data_backups/NFL/'.$table.'/');
			}
			$result = "";
		
			$query = $this->NFL_db->get($table);
			$num_fields = $query->num_fields();
			
        
			$num_row = $query->num_rows($query);
			
			foreach ($query->result_array() as $row_array)
			{
				$row = array_values($row_array);
				$result .= "INSERT DELAYED INTO ".$table." VALUES(";
				for($j=0; $j<$num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = preg_replace("/\n/","/\\n/",$row[$j]);
					if (isset($row[$j])) $result .= "\"$row[$j]\"" ; else $result .= "\"\"";
					if ($j<($num_fields-1)) $result .= ",";
				}    
				$result .= "); \n";
				
			}//while row
			$result .= "\n\n\n";
			$time = mdate('%Y-%m-%d.%H%i%s');

			$file_name = $table."_Backup_".$time.".sql";
			
			$fileRoot = '/home1/theffflc/public_html/fantasy/data_backups/NFL/'.$table.'/'.$file_name;
			if (!$handle = fopen($fileRoot, 'x')) {
				 echo "Cannot create file ($file_name).\n ";
				 exit;
			}
			
			// Write result data to our opened file.
			if (fwrite($handle, $result) === FALSE) {
			   echo "Cannot write to file ($file_name).\n ";
			   exit;
			}
			
			fclose($handle);
			
			//delete old files (greater than a week but keep at least 5
			// Grab all files from the desired folder
			$files = glob('/home1/theffflc/public_html/fantasy/data_backups/NFL/'.$table.'/*.*');
			$file_count = 0;
			
			
			$file_count = count($files);
			
			// Sort files by modified time, latest to earliest
			// Use SORT_ASC  for earliest to latest
			array_multisort(
				array_map('filemtime', $files),
				SORT_ASC,
				$files
			);
			
			while($file_count>5)
			{
				
				if((time()-filemtime($files[0]))>7*24*60*60)//one week
				{
					unlink($files[0]); // the latest modified file should be the first.
					array_splice($files,0,1);
				}
				$file_count--;
			}
		}//foreach table
		
		
		
	}//database backup
	
	
	
}//end model


/*End of file Database_Manager.php*/
/*Location: ./application/models/Database_Manager.php*/