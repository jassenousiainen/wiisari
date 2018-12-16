<?php

// display 'Powered by' info in bottom right of each page //

echo "      </table>\n";
echo "    </td>\n";
echo "  </tr>\n";
echo "</table>\n";



echo "
<div class='footerbox'>
<p>$app_name&nbsp; versio: $app_version</p>";

if ($email != "none") {
    echo "<a class=footer_links href='mailto:$email'>$email</a>&nbsp;&#8226;&nbsp;";
}

echo '
Powered by: <a class="footer_links" href="http://timeclock.sourceforge.net/"">PHP Timeclock</a>
</div>
';

echo "</body>\n";
echo "</html>\n";
?>
