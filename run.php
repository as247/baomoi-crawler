<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Run</title>
<script type="text/javascript" src="jquery.js">
</script>
<script type="text/javascript">
var nostop=<?php echo isset($_GET['nostop'])&&$_GET['nostop']?1:0?>;
var from=<?php echo isset($_GET['from'])?abs(intval($_GET['from'])):0?>;
var to=<?php echo isset($_GET['to'])?abs(intval($_GET['to'])):0?>;
var page=<?php echo isset($_GET['page'])?abs(intval($_GET['page'])):0?>;
var is_reverse=from>to;
var max_line=100;
$(document).ready(function(){
	run(from);
});
function run(i){

	if($('#data li').length>max_line){
		strip=$('#data li').length-max_line
		$('#data li:lt('+strip+')').remove();
	};
	if(is_reverse){
        if(i < to){
            console.info('stopped');
            return;
        }
    }else{
        if(i > to){
            console.info('stopped');
            return;
        }
    }
    console.info('run:'+i);
	$.ajax({
        url: 'baomoi.php',
        data:{
          id:i,
          page:page
        },
        cache: false,
        success: function(data) {
            if(data&&data!='error'&&data.match(/<li.*/)){
                if(data=='stop'){
                    data=$('#data').html()+'<li style="color:red">'+i+' stop</li>';
                    $('#data').html(data);
                    return;
                }
                data=$('#data').html()+data;
                $('#data').html(data);
                if(is_reverse){
                    run(i-1);
                }else{
                    run(i+1);
                }
            }else{
                data=$('#data').html()+'<li style="color:red">'+i+' error, run again</li>';
                $('#data').html(data);
                run(i);
            }

        },
        error:function(){
            data=$('#data').html()+'<li style="color:red">'+i+' error, run again</li>';
            $('#data').html(data);
            run(i);
        }
        });

}

</script>
</head>
<body>
	<ul id="data"></ul>
</body>
</html>