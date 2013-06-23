<?php
include_once 'header.php';

if (!$loggedin) die();

if (isset($_GET['view'])) $view = sanitizeString($_GET['view']);
else                      $view = $user;

if (isset($_POST['text']))
{
    $text = sanitizeString($_POST['text']);

    if ($text != "")
    {
        $pm   = substr(sanitizeString($_POST['pm']),0,1);
        $time = time();
        queryMysql("INSERT INTO messages VALUES(NULL, '$user',
            '$view', '$pm', $time, '$text')");
    }
}

if ($view != "")
{
    if ($view == $user) $name1 = $name2 = "Your";
    else
    {
        $name1 = "<a href='members.php?view=$view'>$view</a>'s";
        $name2 = "$view's";
    }

    echo "<div class='page-header'>$name1 Messages</div>";
    showProfile($view);
    
    echo <<<_END
<form class='centered' method='post' action='messages.php?view=$view'>
Type here to leave a message:<br />
<textarea name='text' cols='40' rows='3'></textarea><br />
Public<input type='radio' name='pm' value='0' checked='checked' />
Private<input type='radio' name='pm' value='1' />
<input style='padding:3px' type='submit' value='Post Message' /><br />
_END;

    if (isset($_GET['erase']))
    {
        $erase = sanitizeString($_GET['erase']);
        queryMysql("DELETE FROM messages WHERE id=$erase AND recip='$user'");
    }
    
    $query  = "SELECT * FROM messages WHERE recip='$view' ORDER BY time DESC";
    $result = queryMysql($query);
    $num    = mysql_num_rows($result);

    
    for ($j = 0 ; $j < $num ; ++$j)
    {
        $row = mysql_fetch_row($result);

        if ($row[3] == 0 || $row[1] == $user || $row[2] == $user)
        {
            echo date('M jS \'y g:ia:', $row[4]);
            echo " <a href='messages.php?view=$row[1]'>$row[1]</a> ";

            $message = $row[5];

            $link = "";
            $linkPos = strpos($message,'builder.php?edit');


            if ( $linkPos !== false)
            {
                $link = substr($message, $linkPos);
                $message = substr($message, 0, $linkPos);
            }
                

            if ($row[3] == 0)
            {
                if( $link == "" )
                    echo "wrote: &quot;$row[5]&quot; ";
                else
                {
                    echo $message;
                    echo "<br>";
                    echo "<a href='$link'> Edit Case </a>";
                }
            }  
            else 
            {
                if( $link == "" )
                    echo "whispered: <span class='whisper'>" .
                        "&quot;$row[5]&quot;</span> ";
                else
                {
                    echo $message;
                    echo "<br>";
                    echo "<a href='$link'> Edit Case </a>";
                }
            }

            if ($row[2] == $user)
                echo "[<a href='messages.php?view=$view" .
					      "&erase=$row[0]'>erase</a>]";

            echo "<br>";
        }
    }
}

if (!$num) echo "<br /><span class='info'>No messages yet</span><br /><br />";

echo "<br /><a class='button' href='messages.php?view=$view'>Refresh messages</a>";
?>

</form>
</div><br /></body></html>
