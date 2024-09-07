<table class="features-table" width="100%"><thead>
<tr><td class="grey">ONU statuses</td></tr></thead><tbody>
<tr><td class="grey">
<div style="text-align:left; padding:0 10px; margin:1% 15% 1% 1%; border: 1px solid black; border-radius: 3px;">
<?php

if ($_GET['gpon'] == 1){
?>
&FilledSmallSquare; <b>Admin ctrol</b> - Выключена SFP<br>
&FilledSmallSquare; <b>Dying Gasp</b> - Выключено питание ONU<br>
&FilledSmallSquare; <b>Losi</b> - Обрыв по оптике<br>
&FilledSmallSquare; <b>reboot</b> - Перезагрузка по комманде OLT<br>
<?php
}else{
    ?>
&FilledSmallSquare; <b>unknow</b> - Неизвестно (ОНУ не была активна после перезагрузки OLT)<br>
&FilledSmallSquare; <b>power-off</b> - Выключено питание ONU<br>
&FilledSmallSquare; <b>wire-down</b> - Обрыв по оптике<br>
&FilledSmallSquare; <b>llid-admin-down</b> - Заблокирована администратором или проблема с уровнем<br>
<?php
}
?>
</div>
</td></tr>
</tbody><tfoot><tr><td class="grey"><a href="javascript:void();" onclick="hide_me();">[Hide]</a></td></tr></tfoot></table>