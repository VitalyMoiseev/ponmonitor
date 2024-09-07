<table class="features-table">
    <thead>
    <tr>
<?php
if($spLevel == 0){
    $trdcs = 3;
}else{
    $trdcs = 2;
}
$col_w = floor(100/$trdcs);
if ($request[1] != 'PON') {
    echo "<td class=\"grey\" width=\"$col_w%\"><a href=\"$protocol$sitename/$base_url1/PON\">PON</a></td>\n";
} else {
    echo "<td class=\"blue\" width=\"$col_w%\"><a href=\"$protocol$sitename/$base_url1/PON\">PON</a></td>\n";
}
if($spLevel == 0){
    $l1 = $detect->isMobile() ? "Настр." : "Настройки";
    if ($request[1] != 'настройки') {
        echo "<td class=\"grey\" width=\"$col_w%\"><a href=\"$protocol$sitename/$base_url1/settings\">$l1</a></td>\n";
    } else {
        echo "<td class=\"blue\" width=\"$col_w%\"><a href=\"$protocol$sitename/$base_url1/settings\">$l1</a></td>\n";
    }
}
echo "<td class=\"grey\"><a href=\"$protocol$sitename/exit\">".$labels['exit']."</a></td></tr>";
?>
</thead>
<tfoot><tr><td style="vertical-align: center; text-align: right;" class="grey" colspan="<?php echo $trdcs; ?>"><small><?php echo $labels['User']; ?>: <strong><?php echo $username; ?></strong> &nbsp;&DoubleVerticalBar;&nbsp; 
<?php
$formatter = new IntlDateFormatter($date_loc, IntlDateFormatter::LONG, IntlDateFormatter::MEDIUM);
echo $formatter->format(time());
?></small></td></tr></tfoot>
</table>
