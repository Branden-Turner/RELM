<?php
include_once 'header.php';

if (!$loggedin)
die("You must be logged in to view this page");
?>

<div>

<?php
if (isset($_GET['view']))
{
    $view = sanitizeString($_GET['view']);

    if ($view == $user) $name = "Your";
    else                $name = "$view's";

    echo "<div class='page-header'>$name Profile</hdiv></br>";
    showProfile($view);
    die("</div></body></html>");
}

$result = queryMysql("SELECT user,points FROM members ORDER BY points DESC");
$num    = mysql_num_rows($result);

echo "<div class='page-header'>Members Ranked by Evidence Points</div>";

echo<<<_END
<table width="55%" border="0">
  <tr>
    <th align='left'>Rank</th>
    <th align='left'>Member</th>
    <th align='left'>Evidence</th>
  </tr>
_END;

for ($j = 0 ; $j < $num ; ++$j)
{
    $row = mysql_fetch_row($result);
    $rank = $j + 1;
    echo "<tr> <td align='left'> $rank </td><td align='left'><a href='members.php?view=$row[0]'>$row[0]</a></td><td align='left'>$row[1] </td> </tr>";
}
?>

</table>
</div></body></html>
