<style><?php include('unlist/unlist.css'); ?></style>


<div class="wrap unlistwrap">

<h2>Jobs Softgarden</h2>


<table id="unlistTable" class="wp-list-table widefat sorted_table">
	<thead>
		<tr>
			<th>Title</th>
			<th>ID</th>
			<th>Keywords</th>
		</tr>
	</thead>
	<tbody>

<?php

    require_once '../wp-content/themes/next2015/libs/unirest/Unirest.php';

    $headers = array('Accept' => 'application/json');
    Unirest\Request::auth('f799c05f-8293-43cc-8c8d-580450847565', '');
    $response = Unirest\Request::get('https://api.softgarden.io/api/rest/v2/frontend/jobboards/27828_extern/jobs', $headers, $query);


    foreach($response->body->results as $job)
    {
    	echo '<tr>';
    	echo '<td>';
        echo $job->title;
        echo '</td>';

		echo '<td>';
        echo $job->jobPostingId;
        echo '</td>';

		echo '<td>';
		if(!$job->config->softgarden_keywords)$job->config->softgarden_keywords=Array();
        echo implode(',',$job->config->softgarden_keywords);
        echo '</td>';


        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';


    echo '<pre>';
	var_dump($response);
	echo '</pre>';

?>

</div>