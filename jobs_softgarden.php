<?php
$globalDb = new wpdb(DB_USER,DB_PASSWORD,'next_global',DB_HOST);
if($globalDb->last_error!='') echo($globalDb->last_error);

if($_POST['submit'] && is_array($_POST['positions'])) {
    foreach($_POST['positions'] as $posId => $posPos) {
        $id = esc_sql($posId);
        $pos = (int)esc_sql($posPos);
        $globalDb->update(
            'jobs_softgarden',
            array('position' => $pos),
            array('pkey' => $id),
            array('%d')
        );
    }
}
?>
<style><?php include('unlist/unlist.css'); ?></style>


<div class="wrap unlistwrap">

<h2>Jobs Softgarden</h2>

<br/><br/>

<a class="button-primary" href="/wp-admin/admin-ajax.php?action=softgardenapi">update jobs from softgarden</a>

<?php

    $result=$globalDb->get_results("SELECT * FROM jobs_softgarden");

    echo '<br/><br/><form method="POST"><table class="wp-list-table widefat ">';

    echo '<tr>';
    echo '<th>';
    echo 'Pos.';
    echo '</th><th>';
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
        echo '<input autocomplete="nope" tabindex="'.$job->pkey.'" type="text" style="width: 4em;" name="positions['.$job->pkey.']" value="'.(int)$job->position.'"/>';
        echo '</td><td>';
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
    echo '<br/>';
    echo '<br/>';
    echo '<input type="submit" class="button-primary" name="submit" value="save positions"/>';
    echo '</form>';


?>



</div>