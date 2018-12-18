<style><?php include('unlist/unlist.css'); ?></style>


<div class="wrap unlistwrap">

<h2>Jobs Softgarden</h2>

<br/><br/>

<a class="button-primary" href="/wp-admin/admin-ajax.php?action=softgardenapi">update jobs from softgarden</a>



<?php


    $globalDb = new wpdb(DB_USER,DB_PASSWORD,'next_global','localhost');
    if($globalDb->last_error!='') echo($globalDb->last_error);

    $result=$globalDb->get_results("SELECT * FROM jobs_softgarden");

    echo '<br/><br/><table class="wp-list-table widefat ">';

    echo '<tr>';
    echo '<th>';
    echo 'Title';
    echo '</th><th>';
    echo 'Softgarden';
    echo '</th><th>';
    echo 'Tags';
    echo '</th>';
    echo '</tr>';

    foreach($result as $job)
    {
        echo '<tr>';
        echo '<td>';
        echo $job->title;
        echo '</td><td>';
        echo '<a href="$job->applylink">'.$job->sgid.'</a>';
        echo '</td><td>';
        echo $job->keywords;
        echo '</td>';
        echo '</tr>';
    }

    echo '</table>';
    echo '<br/>';
    echo count($result).' jobs';

?>



</div>