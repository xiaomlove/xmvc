<nav>
  <ul class="pagination pagination-sm">
    <li <?php if($page == 1) echo "class=\"disabled\""?>>
      <a href="javascript:;" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>
    <?php
    	$HTML = "";
    	for ($i = 1; $i <= $total; $i++)
    	{
    		$active = "";
    		if ($page == $i)
    		{
    			$active = " class=\"active\"";
    		}
    		$HTML .= "<li$active><a href=\"javascript:;\">$i</a></li>";
    	}
    	echo $HTML;
    ?>
    <li <?php if($page == $total) echo "class=\"disabled\""?>>
      <a href="javascript:;" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
  </ul>
</nav>