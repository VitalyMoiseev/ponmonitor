<footer><span class="l1"><?php
switch ($lang) {
        case 'ru':
            echo "<a href=\"\" onclick=\"set_lang('uk'); return false;\">Українська</a> - Русский";
            break;
        case 'uk':
            echo "Українська - <a href=\"\" onclick=\"set_lang('ru'); return false;\">Русский</a>";
            break;
    }
    ?>
</span> IP: <?php echo $_SERVER['REMOTE_ADDR']; ?> &copy; <?php echo "$years $servername"; ?> v<?php echo $version; ?> <?php echo $support_mail; ?></footer></body></html>