<div id="header">
    <div class="logo"><img src="img/gtonline_logo.png" style="opacity:1;background-color:FEFFFF;" border="0" alt="" title="Trade Plaza Logo"/></div>
</div>

<div class="nav_bar">
    <ul>
        <li><a href="welcome.php" <?php if($current_filename=='welcome.php') echo "class='active'"; ?>>Welcome</a></li>
        <li><a href="list_item.php" <?php if(strpos($current_filename, 'List_item.php') !== false) echo "class='active'"; ?>>List Item</a></li>
        <li><a href="view_item.php" <?php if($current_filename=='view_item.php') echo "class='active'"; ?>>My Items</a></li>
        <li><a href="search_items.php" <?php if($current_filename=='search_items.php') echo "class='active'"; ?>>Search for Item</a></li>
        <li><a href="view_trade.php" <?php if($current_filename=='view_trade.php') echo "class='active'"; ?>>Trade History</a></li>
        <li><a href="Trade_response.php" <?php if($current_filename=='trade_response.php') echo "class='active'"; ?>>Trade Response</a></li>
        <li><a href="logout.php" <span class='glyphicon glyphicon-log-out'></span> Log Out</a></li>
    </ul>
</div>
