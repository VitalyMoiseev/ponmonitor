<form id="form1" method="POST" action="<?php echo $protocol.$sitename; ?>/auth">
<table class="features-table">
    <thead>
    <tr>
        <td class="grey" width="40%"><a href="<?php echo $protocol.$sitename; ?>" target="_blanc"><img src="<?php echo $protocol.$sitename; ?>/img/logo.png" border="0"></a></td>
        <td class="grey" width="60%"><?php echo $labels['bilenter']; ?></td>
        
    </tr>
    </thead>
    <tbody>
        <tr>
            <td width="40%"><?php echo $labels['name']; ?>:</td>
            <td class="green" width="60%"><input id="f1_name" name="username" required value="<?php echo $username; ?>" /</td>
        </tr>
        <tr>
            <td width="40%"><?php echo $labels['password']; ?>:</td>
            <td class="green" width="60%"><input type="password" id="f1_pass" name="password" required /></td>
        </tr>
        <tr>
            <td width="40%"></td>
            <td class="green" width="60%"><button><strong><?php echo $labels['do_enter']; ?></strong></button></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td class="grey" colspan="2"><strong>&nbsp;<?php echo $message; ?></strong></td></tr></tfoot>
</table>
</form>