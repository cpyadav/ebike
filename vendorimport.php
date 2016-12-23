
<?php 

  if (isset($_POST) && $_POST['submit'] =="submit"){
      //  print_r($_POST); 
     
 
//////////   ftp connection code //////////////
$host= '192.168.1.169';
$user = 'elanceftp';
$password = '14>>Pqwrt';
$ftpConn = ftp_connect($host);
$login = ftp_login($ftpConn,$user,$password);
// check connection
if ((!$ftpConn) || (!$login)) {
 echo 'FTP connection has failed! Attempted to connect to '. $host. ' for user '.$user.'.';
}else{
 echo 'FTP connection was a success.';
 $directory = ftp_nlist($ftpConn,'');
 
    $list = ftp_get_filelist($ftpConn,"projects/careline/");
     //$key =   array_search($list){}
     
     foreach($list as $k=>$v) {
        $new[$k] = $v['name'];
}
  $file_name = "sample.csv";
     if(in_array($file_name,$new[$k])){
		 $msg ="success";
	 }else{
		 
	 }
	 //print_r($new); die;
    //$filepath_old = $directory['13']."/careline/";
    $filepath = "http://192.168.1.169/careline/new.csv";
    
  if (($getdata = fopen($filepath, "r")) !== FALSE) {
			   fgetcsv($getdata); 
			   $data = fgetcsv($getdata);
			  //print_r($data); die;
			   while (($data = fgetcsv($getdata)) !== FALSE) {
			 
					$fieldCount = count($data);
					for ($c=0; $c < $fieldCount; $c++) {
					  $columnData[$c] = $data[$c];
					   // print_r($columnData); 
					}				
			 $product_name = mysqli_real_escape_string($connect ,$columnData[0]);
			 $price = mysqli_real_escape_string($connect ,$columnData[1]);
			 $import_data[]="('".$product_name."','".$price."')";
			  
			// SQL Query to insert data into DataBase
			 }
			 $connect = mysql_connect('localhost','root','admin','casreline'); // First paramater stands for host, Second for Database-user, Third stand for Database-password, Forth Database-name.
if (!$connect) {
	 //Connection is possible using above setting or not
 die('Could not connect to MySQL: ' . mysql_error());
}
			 $import_data = implode(",", $import_data);
			  print_r($import_data); die;
			 $query = "INSERT INTO products(product_name,price) VALUES  $import_data ;";
			 $result = mysql_query($connect ,$query);
			 fclose($getdata);
}else{
	 echo "fail";
}
  
}
ftp_close($ftpConn); 
///////////////////end ////////////////////////


  }
  
  
  
  
   function list_all_files($conn_id, $path){
	   
    $buff = ftp_rawlist($conn_id, $path);
    print_r($buff); die;
    $res = parse_rawlist( $buff) ;
    
    static $flist = array();
    if(count($res)>0){
        foreach($res as $result){
            // verify if is dir , if not add to the  list of files
            if($result['size']== 0){
                // recursively call the function if this file is a folder
                list_all_files($conn_id, $path.'/'.$result['name']);
            }
            else{
            // this is a file, add to final list
                $flist[] = $result;
            }     
        }
    }
    return $flist;
} 
 function ftp_get_filelist($con, $path){
        $files = array();
        $contents = ftp_rawlist ($con, $path);
        $a = 0;

        if(count($contents)){
            foreach($contents as $line){

                preg_match("#([drwx\-]+)([\s]+)([0-9]+)([\s]+)([0-9]+)([\s]+)([a-zA-Z0-9\.]+)([\s]+)([0-9]+)([\s]+)([a-zA-Z]+)([\s ]+)([0-9]+)([\s]+)([0-9]+):([0-9]+)([\s]+)([a-zA-Z0-9\.\-\_ ]+)#si", $line, $out);

                if($out[3] != 1 && ($out[18] == "." || $out[18] == "..")){
                    // do nothing
                } else {
                    $a++;
                   // $files[$a]['rights'] = $out[1];
                    //$files[$a]['type'] = $out[3] == 1 ? "file":"folder";
                    //$files[$a]['owner_id'] = $out[5];
                    //$files[$a]['owner'] = $out[7];
                    //$files[$a]['date_modified'] = $out[11]." ".$out[13] . " ".$out[13].":".$out[16]."";
                    $files[$a]['name'] = $out[18];
                }
            }
        }
        return $files;
    } 
   
    
    
?>
  <html>
    <head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
      <script type="text/javascript">
		  
		  $(document).ready(function(){
				 $("input[name='import_type']").on("click", function() {						
							var import_type = $(this).val();											
							$("#import_type_name").val(import_type);																			
					});
					
			$("input[name='import_source']").on("click", function() {					
					var radio = $(this).val();
					
					$("#import_source_name").val(radio);
					
					$("#import_type_name").val(radio);	
						if(radio == 1 || radio == 2 ){
							$("#ftp_user_div").show();							
						}else{
							$("#ftp_user_div").hide();	
						}
						
					});
			  });
			
				  
             function validatefrm(){
				  										
                 var import_type =jQuery("input:radio[name=import_type]:checked").val();
                 if(import_type == undefined ){
					   alert("Please select import type");
					   return false;
				 }		
                 var isValid =jQuery("input:radio[name=import_source]:checked").val();
                 if(isValid == undefined ){
					   alert("Please select import source");
					    return false;
				 }	
			 }
      </script>
    </head>
    <body>
		 <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return validatefrm()">
		 
		   <input type="hidden" name="import_type_name" value="" id="import_type_name">
		   <input type="hidden" name="import_source_name" value="" id="import_source_name">
			 <br>
         <div style="text-align:center;">
               Name: <input type="text" name ="name">            
         </div>
         <div style="text-align:center;">
           Import Type: <input type="radio" name="import_type" class="import_type" value="1">Product Data
						<input type="radio" name="import_type" class="import_type" value="2">Product Inventory
						<input type="radio" name="import_type" class="import_type" value="3">Prices
						<input type="radio" name="import_type" class="import_type" value="4">Images
         </div><br>
           <div style="text-align:center;" id="radioBtnDiv">
           Import Source: <input type="radio" name="import_source" class="import_source" value="1"> FTP
                          <input type="radio" name="import_source" class="import_source" value="2"> SFTP
                          <input type="radio" name="import_source" class="import_source" value="3"> Upload
                          <input type="radio" name="import_source" class="import_source" value="4"> EDI
          </div><br>
          <div style="text-align:center; display:none;" id="ftp_user_div"><br>
               FTP Host:	<input type="text" name="ftp_host" ><br>
               
               FTP User:    <input type="text" name="ftp_user" ><br>
                FTP Password:<input type="text" name="ftp_password" ><br>
                
               FTP Filename: <input type="text" name="ftp_filename" ><br>
               
          </div><br>
          
          <div style="text-align:center;">Image Dir/URL: <input type="text" name="image_url" id="image_url"></div><br>
          <div style="text-align:center;"><input type="submit" name="submit" value="submit"></div>
          
         </form>
    </body>
  </html>
