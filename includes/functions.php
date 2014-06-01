<?php
function c_exit($message){
	$header='<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>City manager</title></head><body>';
	$footer='</body></html>';
	exit($header."<p>".$message."</p>".$footer);
}
function save_state($page,$value){
	//echo 'save'.$page.$value;
	if(!$page)
		return false;
	$filename=DIR."last/$page";
	$data=$value;
	file_put_contents($filename, $data);
}
function get_state($page){
	$filename=DIR."last/$page";
	if(!file_exists($filename))
		return false;
	return file_get_contents($filename);
}
function require_db(){
	global $cdb;
	include(DIR.'includes/c-db.php');
}
function c_reset_vars( $vars ) {
	for ( $i=0; $i<count( $vars ); $i += 1 ) {
		$var = $vars[$i];
		global $$var;
		if (!isset( $$var ) ) {
			if ( empty( $_POST["$var"] ) ) {
				if ( empty( $_GET["$var"] ) )
					$$var = '';
				else
					$$var = $_GET["$var"];
			} else {
				$$var = $_POST["$var"];
			}
		}
	}
}
function get_city_byid($id){
	$id=abs(intval($id));
	global $cdb;
	return $cdb->get_row($cdb->prepare('SELECT * FROM city WHERE id=%d',$id));
}
function get_cities($args=null){
    global $cdb;
	$keyword=$_GET['s'];
    $result=array();
    $orderby=$_GET['orderby'];
    if(!in_array($orderby,array('name','popula','area','number_plate','phone_area_code')))
        $orderby='id';    
    $sort=$_GET['sort'];
    if(!$sort)
        $sort='asc';
    else
        $sort = $sort=='desc'?'DESC':'ASC';
    if(!empty($keyword)){       
        if(is_numeric($keyword))
            $result=$cdb->get_results($cdb->prepare("SELECT * FROM city WHERE popula like '%%%1\$s%%' or area like '%%%1\$s%%' or number_plate like '%%%1\$s%%' or phone_area_code like '%%%1\$s%%' ORDER BY $orderby $sort",$keyword));
        else
            $result=$cdb->get_results($cdb->prepare("SELECT * FROM city WHERE name like '%%%1\$s%%' ORDER BY $orderby $sort",$keyword));        
    }else{
        $result=$cdb->get_results('SELECT * FROM city ORDER BY '.$orderby ." ".$sort);        
    }
	return search_filter($result);
}
function search_filter($result){
	$orinal=$result;
    $keyword=$_GET['s'];
    global $number_result_excluded;
    $number_result_excluded=0;
    $tmp=array();
    if($keyword && !is_numeric($keyword)){
            foreach((array) $result as $key=>$city)
               if(!preg_match("#($keyword)#i",$city->name)){ 
                    $tmp[]=$result[$key];
                    $number_result_excluded++;
                    unset( $result[$key] );
               }
    }
    if($_GET['filter']=='0'||count($result)==0){
        $number_result_excluded=0;
		if($_GET['orderby']) return $orinal;
        return array_merge($result,$tmp);
    }
    return $result;
}
function hightlight_search($text,$color='#FFFF00'){
	if($keyword=$_GET['s']){
		return preg_replace("#($keyword)#i",'<span style="background-color: '.$color.'">\1</span>',$text);
    }
	return $text;
}
function update_city($city,$id){
	global $cdb;
	$id=abs(intval($id));
	return $cdb->update('city',$city,array('id'=>$id));
}
function delete_city($id){
	global $cdb;
	$id=abs(intval($id));
	return $cdb->query("DELETE FROM city WHERE id=$id");
}
function class_alternate(){
	global $class_alternate;
	if(!isset($class_alternate))$class_alternate=true;
	if($class_alternate){
		echo ' class="alternate"';
		$class_alternate=false;
	}
	else $class_alternate=true;
}
function c_redirect($url){
	header("Location: $url");
	exit();
}
function is_installed(){
	global $cdb;
	return (bool) $cdb->get_var("DESCRIBE city");
}
function sort_link($orderby){
	$order=$_GET['sort'];
	if(!$order){
	   $order='asc';
	}elseif($_GET['orderby']==$orderby){
	   $order=$order=='desc'?'asc':'desc';
	}
    $url=add_query_arg(array('orderby'=>$orderby,'sort'=>$order));
	echo $url;
}
function sort_img($orderby){
    $order=$_GET['sort'];
    $order_img = $order=='desc'?'s_desc.png':'s_asc.png';
    if($_GET['orderby']==$orderby)
        echo "<img src=\"./includes/$order_img\" />";
}
function c_parse_str( $string, &$array ) {
	parse_str( $string, $array );
	if ( get_magic_quotes_gpc() )
		$array = stripslashes_deep( $array );
}
function add_query_arg() {
	$ret = '';
	if ( is_array( func_get_arg(0) ) ) {
		if ( @func_num_args() < 2 || false === @func_get_arg( 1 ) )
			$uri = $_SERVER['REQUEST_URI'];
		else
			$uri = @func_get_arg( 1 );
	} else {
		if ( @func_num_args() < 3 || false === @func_get_arg( 2 ) )
			$uri = $_SERVER['REQUEST_URI'];
		else
			$uri = @func_get_arg( 2 );
	}

	if ( $frag = strstr( $uri, '#' ) )
		$uri = substr( $uri, 0, -strlen( $frag ) );
	else
		$frag = '';

	if ( preg_match( '|^https?://|i', $uri, $matches ) ) {
		$protocol = $matches[0];
		$uri = substr( $uri, strlen( $protocol ) );
	} else {
		$protocol = '';
	}

	if ( strpos( $uri, '?' ) !== false ) {
		$parts = explode( '?', $uri, 2 );
		if ( 1 == count( $parts ) ) {
			$base = '?';
			$query = $parts[0];
		} else {
			$base = $parts[0] . '?';
			$query = $parts[1];
		}
	} elseif ( !empty( $protocol ) || strpos( $uri, '=' ) === false ) {
		$base = $uri . '?';
		$query = '';
	} else {
		$base = '';
		$query = $uri;
	}
	c_parse_str( $query, $qs );
	$qs = urlencode_deep( $qs ); // this re-URL-encodes things that were already in the query string
	if ( is_array( func_get_arg( 0 ) ) ) {
		$kayvees = func_get_arg( 0 );
		$qs = array_merge( $qs, $kayvees );
	} else {
		$qs[func_get_arg( 0 )] = func_get_arg( 1 );
	}
	foreach ( (array) $qs as $k => $v ) {
		if ( $v === false )
			unset( $qs[$k] );
	}
	$ret = build_query( $qs );
	$ret = trim( $ret, '?' );
	$ret = preg_replace( '#=(&|$)#', '$1', $ret );
	$ret = $protocol . $base . $ret . $frag;
	$ret = rtrim( $ret, '?' );
	return $ret;
}
function urlencode_deep($value) {
	$value = is_array($value) ? array_map('urlencode_deep', $value) : urlencode($value);
	return $value;
}
function build_query( $data ) {
	return _http_build_query( $data, null, '&', '', false );
}
function _http_build_query($data, $prefix=null, $sep=null, $key='', $urlencode=true) {
	$ret = array();

	foreach ( (array) $data as $k => $v ) {
		if ( $urlencode)
			$k = urlencode($k);
		if ( is_int($k) && $prefix != null )
			$k = $prefix.$k;
		if ( !empty($key) )
			$k = $key . '%5B' . $k . '%5D';
		if ( $v === NULL )
			continue;
		elseif ( $v === FALSE )
			$v = '0';

		if ( is_array($v) || is_object($v) )
			array_push($ret,_http_build_query($v, '', $sep, $k, $urlencode));
		elseif ( $urlencode )
			array_push($ret, $k.'='.urlencode($v));
		else
			array_push($ret, $k.'='.$v);
	}

	if ( NULL === $sep )
		$sep = ini_get('arg_separator.output');

	return implode($sep, $ret);
}
?>