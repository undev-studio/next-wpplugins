<?php



    $DEBUG_SHOW_SQL=false;

    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
    ini_set('display_errors', '1');

    function printError($error) { print('<br/><br/><div id="message" class="error below-h2"><p>SQL Error: '.$error.'</p></div>'); }
    function printSQLError() { 
        global $wpdb; 
        global $DEBUG_SHOW_SQL;
        if($wpdb->last_error!='') 
        {
            printError($wpdb->last_error); 
            if($DEBUG_SHOW_SQL) printError($wpdb->last_query); 
            return true;
        }
        return false;
    }

    error_reporting(E_ERROR|E_WARNING);
    ini_set('display_errors', '1');
    
    if(!isset($jsonFileName))$jsonFileName = str_replace('php','json', __FILE__ );
    $jstr=file_get_contents($jsonFileName);
    $json=json_decode($jstr,true);

 
    if(!isset($json))
    {
        printError("JSON decode error...".$jsonFileName);
        die();
    }

    $tablename=$json['tablename'];
    $idName=$json['idName'];
    $sortable=$json['sortable'];
    $orderby=$json['orderby'];

    $editable=true;
    if(isset($json['editable'])) $editable=$json['editable'];


    $path=$json['path'];

    $thumbnailSize=$json['thumbnailSize'];
    $thumbnailSizeEdit=$json['thumbnailSizeEdit'];


    function getWidgetAlias($name)
    {
        if($name=='defaultwidget.json')return 'Folded out Widget';
        return $name;
    }


    wp_enqueue_media();


    $firstTimeEdit=false;


    function lang($which)
    {
        global $json;
        if(!isset( $json['l'][$which] ))return '_'.$which;
        return $json['l'][$which];
    }

    function getListingSQL()
    {
        global $orderby;
        global $json;
        global $tablename;
  if ($json['sqlStart']) {
    $sql = $json['sqlStart'] . ' FROM ' . $tablename;
  } else {
    $sql = 'SELECT * FROM ' . $tablename;
  }

        if($json['filter'] && $_REQUEST['filter']!='')
        {
            foreach ($json['filter'] as $f)
            {
                if($_REQUEST['filter']==$f['name'])
                {
                    if($f['sqlWhere']!='')
                    {
                        $sql.=' WHERE '.$f['sqlWhere'].' ';
                    }
                }
            }
        }

        if($sortable)$sql.=' ORDER BY sort;';
            else if($orderby!='')$sql.=' ORDER BY '.$orderby.';';
        return $sql;
    }



function unrowsForm($count,$json)
{
    global $post;
    $content = get_post_meta( $post->ID, 'unrowContent' );
    $extraCSS = "";

    print('<div class="unrow_row"'. $extraCSS .'>');
    print('<div class="unrow_head">');

    if( is_array($content) && count($content)>0 && count(  $json['vars'])>0 && isset( $content[0][$count][$json['vars'][0]['name']] ))
    {
        echo '<span class="teasetext">';
        echo $content[0][$count][$json['vars'][0]['name']];
        echo '</span>';
    }

    print('</div>');

    $style='';

    print('<div class="unrow_row_content" style="'.$style.'">');
    print('<table class="unrow">');

    if(isset($json['vars']))
    foreach ($json['vars'] as $key => $var)
    {
        $value=$var['default'];
        if(isset( $content[0][$count][$var['name']] ))$value=$content[0][$count][$var['name']];

        if(!is_array($value))$value=esc_attr($value);
        $fieldName='widget_'.$var['name'];

        $title=$var['name'];
        if(isset($var['title']))$title=$var['title'];

        if($title)$title.=':';
        print('<tr><th class="unrow">'.$title.'</th>');
        print('<td>');

        if($var['input']=='image') $var['input']='media';

        $contentClassName='content_'.$var['input'];
        if(canClassBeAutloaded($contentClassName) )
        {
            $mod = new $contentClassName();
            $mod->init($var);
            echo $mod->getBackendHTML($fieldName,$value,$var);
        }
        else
        {
            echo 'could not initialize content module:';
        }

        if(unrows::startsWith($value,'sql::'))
        {
            print('<br/>Database Results: '.count($result) );
        }

        print('</td>');
        print('</tr>');
    }

    print('</table>');
    print('</div>');
    print('</div>');
}

?>


<div class="wrap unlistwrap">
<h2><?php echo $json['title']; ?></h2>

<style>
<?php include('unlist/unlist.css'); ?></style>

<script type="text/javascript">

function saveWidgetForm()
{
    var data={};
    jQuery(".unrow").serializeArray().map(function(x){data[x.name] = x.value;});
    console.log(data);
    jQuery('#input_rowdata').val(JSON.stringify(data));
    jQuery('#finalform').submit();
}

jQuery(document).ready(function()
{
    var data=jQuery('#input_rowdata').val();
    data=JSON.parse(data);
    console.log(data);
    for(var i in data)
    {
        var id='[name=\"'+i+'\"]';

        jQuery(id).val(data[i])

        if ( jQuery( '#'+i+'_thumbnail' ).length )
        {
            jQuery('#'+i+'_thumbnail').attr('src',data[i]);
        }
    }
});



</script>

<?php
    function cleanString($string)
    {
       $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
       return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    function getPageSelect($fieldName,$value)
    {
        $html='';
        $html.='<select name="'.$fieldName.'" >';

        $args=array();
        $pages=get_pages($args);

        $html.='<option value="">None</option>';

        foreach ($pages as $kpp => $p) 
        {
            $sel='';
            $v=''.$p->ID;
            if($v == $value)$sel="SELECTED";

            $html.='<option value="'.$p->ID.'" '.$sel.'>';
            $depth = count(get_ancestors($p->ID, "page"));
            for($i=0;$i<$depth;$i++)$html.='&nbsp;&nbsp;&nbsp;';
            $html.=$p->post_title;
            $html.='</option>';
        }

        $html.='</select>';

        return $html;
    }

    global $wpdb;

    $func=$_REQUEST['func'];

    if($func=='save')
    {
        $sql='UPDATE '.$tablename.' SET ';
        
        $count=0;
        foreach ($json['vars'] as $f)
        {
	    if($f['name'] == 'count') continue;

            $val=$_REQUEST[$f['name']];

            if($val=='TIMESTAMP')
            {
                $r_day=$_REQUEST[$f['name'].'_date_day'];
                $r_month=$_REQUEST[$f['name'].'_date_month'];
                $r_year=$_REQUEST[$f['name'].'_date_year'];
                $r_minute=$_REQUEST[$f['name'].'_date_minute'];
                $r_hour=$_REQUEST[$f['name'].'_date_hour'];

                $datestr=$r_year.'-'.$r_month.'-'.$r_day.' '.$r_hour.':'.$r_minute.':00';

                // print('$datestr '.$datestr);
                $val = $datestr;
            }

            if($f['input']!='divider')
            {
                if($count!=0)$sql.=",";
                $sql.=$f['name'].'="'.$val.'" ';
            }

            // echo $f['name'].':::::::::::::::'.$val.'<br/>';
            $count++;
        }

        $sql.=' WHERE '.$idName.' = '.(int)$_REQUEST['which'];
        $wpdb->query( $sql );

        // print($sql);

        $func="edit";
        printSQLError();
    }


    if($func=='saveorder')
    {
        $arr=split ( ",", $_REQUEST["order"]);

        $count=0;
        foreach ($arr as &$id)
        {
            if($id!='')
            {
                $wpdb->query( 'UPDATE '.$tablename.' SET sort = "'.$count.'" WHERE '.$idName.' = '.$id  );
                printSQLError();
            }

            $count++;
        }
        $func="";
    }

    if($func=='delete')
    {
        $which=(int)$_REQUEST['which'];
        $wpdb->delete( ''.$tablename.'', array( 'id' =>  $which) );
        $func="";

        printSQLError();
    }
    
    if($func=='create')
    {
        $data=array();

        foreach ($json['vars'] as $f)
        {
	    if($f['name'] == 'count') continue;
            $data[$f['name']]=$f['default'];
        }

        $wpdb->insert(''.$tablename.'', $data);
        $func="";
        $error=printSQLError();

        if(!$error)
        {
            $func='edit';
            $_REQUEST['which']=$wpdb->insert_id;

            $firstTimeEdit=true;
        }
    }

    if($func=='')
    {
        global $wpdb;

        $rows = $wpdb->get_results(getListingSQL());

        printSQLError();

        print('<br/><br/><a class="button-primary" href="?page='.$path.'&func=create">'.lang('create').'</a>');
        print('&nbsp;&nbsp;<input id="widgetsearch" placeholder="Search Widgets"/>');
        print('<div class="wrap">');

        if($json['filter'])
        {
            print('<div class="unlistFilterContainer">');

            print('Filter: &nbsp;');

            $currentFilter=$_REQUEST['filter'];

            $count=0;
            foreach ($json['filter'] as $f)
            {
                $cssclass="";
                if($f['name'] == $currentFilter)
                {
                    $cssclass='unlistFilterActive';
                }
                print('<a href="?page='.$path.'&filter='.$f['name'].'" class="'.$cssclass.'">'.$f['title'].'</a> ');


                if($count<sizeof($json['filter'])-1 ) print('&nbsp; ');
                $count++;
            }

            print('</div>');
        }

        print('<br/><table id="unlistTable" class="wp-list-table widefat sorted_table" >');
        print('<thead>');
        print('<tr>');
        print('<th></th>');

        foreach ($json['vars'] as $f)
        {
            if(!$f['visible'])continue;
            print('<th>');
            print($f['title']);
            print('</th>');
        }

        print('<th></th>');

        print('</tr>');
        print('</thead>');
        print('<tbody>');

        foreach ($rows as &$row)
        {
            $search='';
            foreach ($json['vars'] as $f)
            {
                $search.=$row->{$f['name']};
            }
            $search=cleanString(strtolower($search));

            print('<tr class="searchable" data-index="'.$search.'">');
            print('<td>');
            if($editable) 
            {
               print('<a class="button" href="?page='.$path.'&func=edit&which='.$row->id.'">'.lang('edit').'</a>  &nbsp; ');
            }
            print('</td>');


            foreach ($json['vars'] as $f)
            {
                if(!$f['visible'])continue;
                print('<td>');

                if($f['input']=='media')
                {
                    print('<img style="width:'.$thumbnailSize.'px;" src="'.$row->{$f['name']}.'"/> ');
                }
                else print(getWidgetAlias($row->{$f['name']}));

                print('</td>');
            }

            print('<td>');
            if($editable) 
            {
               print('<a href="?page='.$path.'&func=delete&which='.$row->id.'" onclick="return confirm(\''.lang('delete').' ?\')">'.lang('delete').'</a> ');
            }

            print('</td>');
            print('<td class="sortid">'.$row->id.'</td>');
            print('</tr>');
        }
        print('</tbody>');
        print('</table>');
        print('</div>');

        print('<div class="metainf">&nbsp;&nbsp;'.sizeof($rows).' Entries.</div>');

        if($sortable) print('<br/><a class="button-primary" onclick=" saveOrder();">'.lang('save_order').'</a>');
    }


    if($func=='edit')
    {
        global $wpdb;
        $rows = $wpdb->get_results('SELECT * FROM '.$tablename.' WHERE '.$idName.'='.(int)$_REQUEST['which'].';');

        print('<iframe src="/staff/widget-preview?id='.(int)$_REQUEST['which'].'" style="width:300px;height:900px;position:absolute;right:10px;"></iframe>');

        print('<div class="postbox-container" style="max-width:755px;">');
        print('<div class="postbox">');
        print('<h3 style="padding:10px;">'.lang('edit').':</h3>');
        print('<div class="inside">');
        print('<form method="post" action="" id="finalform" enctype="multipart/form-data">');
        print('<input type="hidden" name="func" value="save" />');
        print('<input type="hidden" name="which" value="'.(int)$_REQUEST['which'].'" />');

        $dir='../wp-content/plugins/next_content/templates_widgets/';
        $widgetJsonFilename='';

        foreach ($rows as &$row)
        {
            print('<table>');
            
            foreach ($json['vars'] as $f)
            {
                $visi='';
                if($f['editable']==false)$visi='display:none;';

                $maxlength=$f['maxLength'];
                if(isset($maxlength) && $maxlength!='' )$maxlength='maxlength="'.$maxlength.'"';

                print('<tr><td> </td></tr>');

                if($f['input']=='input')
                {
                    print('<tr style="'.$visi.'">');
                    print(' <td valign="middle" class="edittitle">'.$f['title'].':</td>');
                    print(' <td><input '.$maxlength.' type="text" style="width:600px;" name="'.$f['name'].'" value="'.$row->{$f['name']}.'"/></td>');
                    print('</tr>');
                }

                if($f['input']=='textarea')
                {
                    print('<tr style="'.$visi.'">');
                    print(' <td valign="top" class="edittitle">'.$f['title'].':</td>');
                    print(' <td><textarea id="input_'.$f['name'].'" '.$maxlength.' type="text" style="height:160px;width:600px;" name="'.$f['name'].'" >'.$row->{$f['name']}.'</textarea></td>');
                    print('</tr>');
                }




                if($f['input']=='selectTemplate')
                {
                    print('<tr style="'.$visi.'">');
                    print('<td valign="middle" class="edittitle">'.$f['title'].':</td>');
                    print('<td>');
                    
                    if($row->{$f['name']}!='')
                    {
                        print(getWidgetAlias($row->{$f['name']}).' ('.$row->{$f['name']}.' ) ');
                        print('<input  name="'.$f['name'].'" type="hidden" value="'.$row->{$f['name']}.'">');
                        $widgetJsonFilename=$row->{$f['name']};
                    }
                    else
                    {
                        print('<select name="'.$f['name'].'">');
                        $files=Array();
                        if ($dh = opendir($dir))
                        {
                            while (($file = readdir($dh)) !== false)
                            {
                                if(!is_dir($dir.$file))
                                {
                                    if(unRows::endsWith($file,'.json'))
                                    {
                                        $sel='';
                                        if($file==$row->{$f['name']})
                                        {
                                            $sel=" selected ";
                                            $widgetJsonFilename=$row->{$f['name']};
                                        }
                                        print('<option '.$sel.' value="'.$file.'">'.getWidgetAlias($file).'</option>');
                                    }
                                }
                            }
                            closedir($dh);
                        }
                        print('</select>');
                    }

                    print('</td>');
                    print('</tr>');
                }
            }

            print('<tr>');
            print('<td colspan="2">');
            if(isset($widgetJsonFilename) && $widgetJsonFilename!='')
            {
                $jstr=file_get_contents($dir.$widgetJsonFilename);
                $jsonMod=json_decode($jstr,true);

                if(!isset($jsonMod))
                {
                    print("<br/>error: json decode error... [".$dir.' -- '.$widgetJsonFilename.']');
                }
                else
                {
                    $jsonMod['filename']=$filename;
                    unrowsForm(0,$jsonMod);
                }
            }

            print('</td>');
            print('</tr>');

            print('<tr>');
            print(' <td colspan="4"><br/><br/>');
            print(' <input type="button" class="button-primary" onClick="saveWidgetForm();" value="'.lang('save').'" />');
            print(' <input type="button" class="button-secondary" onClick="document.location.href=\'admin.php?page=next_content%2Fnext_widgets.php\';" value="'.lang('list').'" />');

            print(' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?page='.$path.'&func=delete&which='.$row->id.'" onclick="return confirm(\''.lang('delete').' ?\')">'.lang('delete').'</a> ');
            print('</td>');
            print('</tr>');
            print('</table>');
        }

        print('</form>');
        print('</div>');
        print('</div>');
    }
?>
</div>
                                    
<script type="text/javascript" src="/wp-includes/js/jquery/ui/core.min.js"></script>
<script type="text/javascript" src="/wp-includes/js/jquery/ui/widget.min.js"></script>
<script type="text/javascript" src="/wp-includes/js/jquery/ui/mouse.min.js"></script>
<script type="text/javascript" src="/wp-includes/js/jquery/ui/sortable.min.js"></script>

<style id="search_style"></style>
<script>

    jQuery('#widgetsearch').on('input',widgetSearch);

    function widgetSearch()
    {
        searchFor=jQuery('#widgetsearch').val().toLowerCase();

        jQuery('.metainf').hide();

        if(!searchFor) jQuery('#search_style').html('.searchable:{display:block;}');
            else jQuery('#search_style').html(".searchable:not([data-index*=\"" + searchFor.toLowerCase() + "\"]) { display: none; }");
    }

</script>

