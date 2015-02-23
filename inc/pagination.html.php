<!-- pagination -->
<div class="pagination">

	<?php
	$i=0;
	
	while ($i < $totalPages){
		$i++;

		if($totalPages!=1){

			if($i == $page){ 

				echo '<span>' . $i . '</span>';

			} else {

				echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&p=' . $i . '">' . $i . '</a>';

			}
		}

	}
	?>

</div>
<div class="clear"></div>