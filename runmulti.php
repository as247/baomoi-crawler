<?php
require_once 'c-config.php';
//13941036
$runFrom=13941036;
$runTo=13941036-1100000;//12841036
$threads=10;
if($runFrom>$runTo){
    $max=$runFrom;
    $min=$runTo;
}else{
    $max=$runTo;
    $min=$runFrom;
}

$pages=ceil(($max-$min+1)/$threads);
?>
<strong>Total: <?php echo abs($runFrom-$runTo);?> in <?php echo $threads;?> threads</strong>
<hr/>
<?php
for($i=1;$i<=$threads;$i++):
	$page=$i;
	$ofrom=($i-1)*$pages+$min;
    $previous_state=false;
	if(false===($from=get_state($page)))
		$from=$ofrom;
	else{
        $previous_state=true;
		$from=abs(intval($from));
	}
	$to=$ofrom+$pages-1;

    if($runFrom>$runTo){
        if(!$previous_state){
            $from=$max+$min-$from;
        }
        $to=$max+$min-$to;
        if($to<$min)$to=$min;
    }else{
        if($to>$max)$to=$max;
    }
?>
<h3>Thread:<?php echo $page?> - From:<?php echo $from?> to: <?php echo $to;?> = <?php echo abs($from-$to);?> records</h3>
<iframe src="run.php?from=<?php echo $from?>&to=<?php echo $to?>&page=<?php echo $page;?>" style="width:100%; height: 400px;"></iframe>
<?php endfor;?>