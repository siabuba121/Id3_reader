<?php
class Id3_reader{

private $_file_content;
private $_file_size;
private $_tag;
private $_version_id3;
private $_id3v23_tags = array("TIT2","TALB","TPE1","TRCK","TDRC","TLEN","USLT");
private $_id3v22_tags = array("TT2","TAL","TP1","TRK","TYE","TLE","ULT");


	function __construct($file){
		$this->_file_size = filesize($file);
		//only for read
		$this->_file_content = fopen($file, "r");
		$this->_tag = fread($this->_file_content, $this->_file_size);
		fclose($this->_file_content);
		//get version 
		$this->_version_id3 = $this->_get_version();
	}


	private function _get_version(){
		if(substr($this->_tag,0,3) == "ID3"){
			return  hexdec(bin2hex(substr($this->_tag,3,1))).".".hexdec(bin2hex(substr($this->_tag,4,1)));
		}else{
			echo "oops no  ID3\n";
			exit;
		}
	}

	public function get_info_from_id3(){
		$info_array;

		switch ($this->_version_id3){
		case "4.0":
		case "3.0":
			 for ($i=0;$i<count($this->_id3v23_tags);$i++){
            			if (strpos($this->_tag,$this->_id3v23_tags[$i].chr(0))!= FALSE){
                			$pos = strpos($this->_tag, $this->_id3v23_tags[$i].chr(0));
                			$len = hexdec(bin2hex(substr($this->_tag,($pos+5),3)));
                			$data = substr($this->_tag, $pos, 9+$len);
                				for ($a=0;$a<strlen($data);$a++){
                    					$char = substr($data,$a,1);
                    					if($char >= " " && $char <= "~") $tmp.=$char;
                				}
                				if(substr($tmp,0,4) == "TIT2") $info_array['Title'] = substr($tmp,4);
                				if(substr($tmp,0,4) == "TALB") $info_array['Album'] = substr($tmp,4);
                				if(substr($tmp,0,4) == "TPE1") $info_array['Author'] = substr($tmp,4);
                				if(substr($tmp,0,4) == "TRCK") $info_array['Track'] = substr($tmp,4);
                				if(substr($tmp,0,4) == "TDRC") $info_array['Year'] = substr($tmp,4);
                				if(substr($tmp,0,4) == "TLEN") $info_array['Lenght'] = substr($tmp,4);
                				if(substr($tmp,0,4) == "USLT") $info_array['Lyric'] = substr($tmp,7);
                				$tmp = "";
            			}
        		}
			break;
		case "2.0":
			for ($i=0;$i<count($this->_id3v22_tags);$i++){
           			if (strpos($this->_tag,$this->_id3v22_tags[$i].chr(0))!= FALSE){
                			$pos = strpos($this->_tag, $this->_id3v22_tags[$i].chr(0));
               				$len = hexdec(bin2hex(substr($this->_tag,($pos+3),3)));
                			$data = substr($this->_tag, $pos, 6+$len);
                			for ($a=0;$a<strlen($data);$a++){
                    				$char = substr($data,$a,1);
                    				if($char >= " " && $char <= "~") $tmp.=$char;
                			}
                			if(substr($tmp,0,3) == "TT2") $info_array['Title'] = substr($tmp,3);
                			if(substr($tmp,0,3) == "TAL") $info_array['Album'] = substr($tmp,3);
                			if(substr($tmp,0,3) == "TP1") $info_array['Author'] = substr($tmp,3);
                			if(substr($tmp,0,3) == "TRK") $info_array['Track'] = substr($tmp,3);
                			if(substr($tmp,0,3) == "TYE") $info_array['Year'] = substr($tmp,3);
                			if(substr($tmp,0,3) == "TLE") $info_array['Lenght'] = substr($tmp,3);
                			if(substr($tmp,0,3) == "ULT") $info_array['Lyric'] = substr($tmp,6);
                			$tmp = "";
            			}
        		}
			break;
		}
		return $info_array;
	}
}
?>

