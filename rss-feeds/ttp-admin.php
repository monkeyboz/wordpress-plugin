<?php
class Layout{
    var $query;
    var $layout;
    var $info;
    var $layout_dir = 'layout/';
    var $json = '';
    var $xml = '';
    
    //constructor for the layout function, used for various information gathering and setting up
    //numerous other information used for various other html layout compositions
    public function __construct($args){
        extract($args);
        $queryString = "post_type=".$post;
        if(isset($num)){
            $queryString .= '&post_per_page='.$num;
        }
        $this->info = new WP_Query($queryString);
    }
    //get public function that allows for information to be passed through to various other modules when needed
    //there are numerous options in this function to allow returning information to numerous modules and other
    //string types which include xml as well as json
    public function getInfo(){
        $this->info = $this->query->posts;
    }
    //used to return xml information
  	public function getJSON(){
  		return json_encode($this->info->posts);
  	}
    //used to return json information
  	public function getXML(){
  	    $json = $this->getJSON();
  	    $json = json_decode($json,true);
        $this->createXML($json);
  	}
  	public function createXML($json,$string=""){
  	    if(is_array($json)){
  	        $string .= '<post>';
      	    foreach($json as $v=>$c){
                if(!is_array($c)){
                  $string .= '<'.$v.'>'.$c.'</'.$v.'>';
                } else {
                  $string .= '<'.$v.'>'.$this->createXML($c,$string).'</'.$v.'>';
                }
      	    }
      	    $string .= '</post>';
  	    }
  	    return $string;
  	}
    //search function used for various searching queries used for various populating information with
    //the WP_Query Object
    public function search($query){
        $queryString = '';
        foreach($query as $k=>$q){
            $queryString .= $k.'='.$q.'&';
        }
        $queryString = substr($queryString,0,-1);
        $this->query = new WP_Query($queryString);
        $this->info = $this->query->posts;
    }
    //get layout is used to create a layout that will allow the population of the template file
    //if there are issues with the template, it will be fixed through the populate_layout function
    public function get_layout($layout){
        $this->layout = file_get_contents($layout);
    }
    //populates the template to make sure that there are variables need to complete the layout
    //this will allow for various layouts to be created dynamically if needed.
    /*the options being passed are 
   		$template which holds the template string
    	$info which holds the info to be pulled into the template
    */
    //if there is information in the template but not in the information string, then there will be an ommiting of that information
    //until it is otherwise validated through the plugin
    public function populate_layout($info){
        $templateHolder = "";
        $template = $this->layout;
        if($this->layout == ''){
        	foreach($info as $k=>$i){
        		$templateHolder .= '<div class="'.$k.'">'.$i.'</div>';
        	}
        } else {
        	foreach($info as $k=>$p){
        	    $template_double = $template;
        	    preg_match_all("/\[(\w+(\:\d+)?)\]/is", $template_double, $str_result);
        	    foreach($str_result[0] as $s){
        	        preg_match("/(\w+)(\:(\d+))?/is", $s, $strHolder);
            	    if(sizeof($strHolder) < 4){
            	        if($strHolder[0] == 'link'){
            	            $template_double = str_replace('['.$strHolder[0].']', get_permalink($p->ID), $template_double);
            	        } else {
            		        $template_double = str_replace('['.$strHolder[0].']', $p->{$strHolder[0]}, $template_double);
            	        }
            	    } else {
            	        $template_double = str_replace('['.$strHolder[0].']', substr($p->{$strHolder[1]},0,$strHolder[3]), $template_double);
            	    }
        	    }
        	    $templateHolder .= $template_double;
        	}
        }
        return $templateHolder;
    }
}
?>