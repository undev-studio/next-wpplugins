
<style type="text/css" media="screen">
    .nextformedit .draggable,.nextformedit .placeholder
    {
        margin:5px;
        border:1px solid #ccc;
        background-color: #e6e6e6;
        border-radius: 4px;
        height:50px;
        padding:5px;
        cursor:move;
        opacity: 0.9;
        text-overflow: ellipsis;
        overflow: hidden;
        max-width: 300px;
    }

    .activefield
    {
        border:1px solid #888 !important;
        background-color: #ffba00 !important;
        /*background-color: #fff !important;*/
        opacity: 1 !important;
    }

    .nextformedit .placeholder
    {
        /*background-color: red;*/
        opacity: 0.3;
    }

    .formeditform
    {
        position: fixed;
        top:30%;
        right:20px;
        /*top:100px;*/
        /*border:2px solid red;*/
        background-color: #e6e6e6;
        padding:10px;
        border-radius: 4px;
        border:1px solid #ccc !important;
        max-width: 300px;
    }

    .formeditform tr
    {
        background-color: transparent !important;
    }

    #footer-thankyou,#footer-upgrade
    {
        display: none;
    }

    #input_rowdata
    {
        display: none;
    }

</style>
<script type="text/javascript">

var NEXTFORM={};


NEXTFORM.save=function()
{
    var linkOrderData = jQuery('.dragarea1').sortable('toArray');
    var linkOrderData2 = jQuery('.dragarea2').sortable('toArray');

    var arr=[];
    var arr2=[];

    function cleanData(data)
    {
        if(data.sortableItem) delete data.sortableItem;
        if(data['sortable.preventClickEvent']) delete data['sortable.preventClickEvent'];
        return data;
    }

    for(var i=0;i<linkOrderData.length;i++)
    {
        var id=linkOrderData[i];
        var data=jQuery('#'+id).data();
        data=cleanData(data);
        arr.push(data);
    }

    for(var i=0;i<linkOrderData2.length;i++)
    {
        var id=linkOrderData2[i];
        var data=jQuery('#'+id).data();
        data=cleanData(data);
        arr2.push(data);
    }

    var str=JSON.stringify(
        {
            column1:arr,
            column2:arr2
        },false,4);

    jQuery('#input_rowdata').val(str)
}


NEXTFORM.addField=function()
{
    var arr1 = jQuery('.dragarea1').sortable('toArray');
    var arr2 = jQuery('.dragarea2').sortable('toArray');

    if(arr1.length>arr2.length) NEXTFORM.addColumnField('.dragarea2');
        else NEXTFORM.addColumnField('.dragarea1');

    NEXTFORM.save();
    NEXTFORM.bindFieldEvents();
};


NEXTFORM.guid=function()
{
    function s4() {
        return Math.floor((1 + Math.random()) * 0x10000)
            .toString(16)
            .substring(1);
    }
    return s4() + s4() + '-' + s4() + '-' + s4() + '-' +s4() + '-' + s4() + s4() + s4();
}


NEXTFORM.addColumnField=function(selector)
{
    var id=NEXTFORM.guid();
    var html='<div class="draggable" id="'+id+'" ></div>';
    jQuery(selector).append(html);
    NEXTFORM.updateField(id,{});
    return id;
}

NEXTFORM.deleteField=function(id)
{
    jQuery('#'+id).remove();
    jQuery('#field-edit').html('');
    
    NEXTFORM.save();
}

NEXTFORM.buildColumnFields=function(selector,arr)
{
    jQuery(selector).html('');

    for(var i in arr)
    {
        var data=arr[i];
        var id=NEXTFORM.addColumnField(selector);

        NEXTFORM.updateField(id,data)
    }
}

NEXTFORM.deserialize=function()
{
    var str=jQuery('#input_rowdata').val();
    var data=JSON.parse(str);

    NEXTFORM.buildColumnFields('.dragarea1',data.column1);
    NEXTFORM.buildColumnFields('.dragarea2',data.column2);
}


NEXTFORM.editField=function(which)
{
    console.log('edit field');
    var html='';
    var ele=jQuery('#'+which);

    jQuery('.draggable').removeClass('activefield');
    ele.addClass('activefield');

    html+='Edit Field: <b>'+ele.data('title')+'</b>';
    html+='<br/>';
    html+='<br/>';

    html+='<table>';

    html+='<tr>';
    html+='<td>Title</td>';
    html+='<td>';
    html+='  <input id="field_title" oninput="NEXTFORM.saveField(\''+which+'\');" value="'+(ele.data('title')||'')+'"/>';
    html+='</td>';
    html+='</tr>';

    html+='<tr>';
    html+='<td>Type</td>';
    html+='<td>';
    html+='  <select id="field_type" onchange="NEXTFORM.saveField(\''+which+'\');" >';


    var sel='';
    html+='    <option value="empty">empty space</option>';




    sel='';
    if(ele.data('type')=='headline')sel='selected';
    html+='    <option '+sel+' value="headline">headline</option>';

    if(ele.data('type')=='input')sel='selected';
    html+='    <option '+sel+' value="input">input field</option>';
    
    sel='';
    if(ele.data('type')=='textarea')sel='selected';
    html+='    <option '+sel+' value="textarea">textarea</option>';

    sel='';
    if(ele.data('type')=='select')sel='selected';
    html+='    <option '+sel+' value="select">selectbox</option>';

    sel='';
    if(ele.data('type')=='checkbox')sel='selected';
    html+='    <option '+sel+' value="checkbox">checkbox</option>';

    sel='';
    if(ele.data('type')=='fineprint')sel='selected';
    html+='    <option '+sel+' value="fineprint">fine-print</option>';

    sel='';
    if(ele.data('type')=='divider')sel='selected';
    html+='    <option '+sel+' value="divider">divider</option>';

    sel='';
    if(ele.data('type')=='dividerfullwidth') sel='selected';
    html+='    <option '+sel+' value="dividerfullwidth">divider full width</option>';


    sel='';
    if(ele.data('type')=='emptyfullwidth')sel='selected';
    html+='    <option '+sel+' value="emptyfullwidth">empty full width</option>';






    html+='  </select>';

    html+='</td>';
    html+='</tr>';
    html+='</table>';
    html+='<br/>';
    html+='<a  onclick="NEXTFORM.deleteField(\''+which+'\');" style="text-decoration:underline">Delete Field</a>';
    html+='<br/>';

    jQuery('#field-edit').html(html);
}

NEXTFORM.saveField=function(which)
{
    NEXTFORM.updateField(which,
    {
        "title":jQuery('#field_title').val(),
        "type":jQuery('#field_type').val()
    });

    NEXTFORM.save();
}

NEXTFORM.updateField=function(which,data)
{
    data.title=data.title||'';
    data.type=data.type||'empty';

    var html='';
    html+='<b>'+data.title+'</b>';
    html+=' ('+data.type+') ';

    jQuery('#'+which).data('title',data.title);
    jQuery('#'+which).data('type',data.type);
    jQuery('#'+which).html(html);
}

NEXTFORM.bindFieldEvents=function()
{

    jQuery(".draggable").unbind();
    jQuery(".draggable").bind("click",function()
    {
        NEXTFORM.editField(jQuery(this).attr("id"));
    });

}

jQuery( document ).ready(function()
{
    jQuery(".dragarea").sortable(
    {
        containerSelector: "div",
        itemSelector: ".draggable",
        connectWith: ".dragarea",
        placeholder: "placeholder",
        update: NEXTFORM.save
    });

    NEXTFORM.deserialize();
    NEXTFORM.bindFieldEvents();
});


</script>
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
        $sql='SELECT * FROM '.$tablename;

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
        else
        {
            if($orderby!='')$sql.=' ORDER BY '.$orderby.';';
        }

        return $sql;
    }

?>


<div class="wrap unlistwrap">
<h2><?php echo $json['title']; ?></h2>

<style>
<?php include('unlist/unlist.css'); ?></style>

<script type="text/javascript">


function saveWidgetForm()
{
    console.log('saveWidgetForm');
    jQuery('#finalform').submit();
}


</script>

<?php
function cleanString($string) {
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
                $search.=$row->$f['name'];
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
                    print('<img style="width:'.$thumbnailSize.'px;" src="'.$row->$f['name'].'"/> ');
                }
                else print(getWidgetAlias($row->$f['name']));

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


        // print('<iframe src="/staff/widget-preview?id='.(int)$_REQUEST['which'].'" style="width:300px;height:900px;position:absolute;right:10px;"></iframe>');

        // print('<div class="postbox-container" style="max-width:755px;">');
        // print('<div class="postbox">');
        // print('<h3 style="padding:10px;">'.lang('edit').':</h3>');
        // print('<div class="inside">');
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
                    print(' <td><input '.$maxlength.' type="text" style="width:600px;" name="'.$f['name'].'" value="'.$row->$f['name'].'"/></td>');
                    print('</tr>');
                }

                if($f['input']=='textarea')
                {
                    print('<tr style="'.$visi.'">');
                    print(' <td valign="top" class="edittitle">'.$f['title'].':</td>');
                    print(' <td><textarea id="input_'.$f['name'].'" '.$maxlength.' type="text" style="height:160px;width:600px;" name="'.$f['name'].'" >'.$row->$f['name'].'</textarea></td>');
                    print('</tr>');
                }

                if($f['input']=='selectTemplate')
                {
                    print('<tr style="'.$visi.'">');
                    print('<td valign="middle" class="edittitle">'.$f['title'].':</td>');
                    print('<td>');
                    
                    if($row->$f['name']!='')
                    {
                        print(getWidgetAlias($row->$f['name']).' ('.$row->$f['name'].' ) ');
                        print('<input  name="'.$f['name'].'" type="hidden" value="'.$row->$f['name'].'">');
                        $widgetJsonFilename=$row->$f['name'];
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
                                        if($file==$row->$f['name'])
                                        {
                                            $sel=" selected ";
                                            $widgetJsonFilename=$row->$f['name'];
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
            // if(isset($widgetJsonFilename) && $widgetJsonFilename!='')
            // {
            //     $jstr=file_get_contents($dir.$widgetJsonFilename);
            //     $jsonMod=json_decode($jstr,true);

            //     if(!isset($jsonMod))
            //     {
            //         print("<br/>error: json decode error... [".$dir.' -- '.$widgetJsonFilename.']');
            //     }
            //     else
            //     {
            //         $jsonMod['filename']=$filename;
            //         // unrowsForm(0,$jsonMod);
            //     }
            // }

            print('</td>');
            print('</tr>');

            print('<tr>');
            print('<td class="edittitle"  valign="top">');
            print('    <div id="field-edit" class="formeditform"></div>');
            print('Modules');

            



            print('</td>');
            print('<td class="nextformedit">');


            print('<div class="dragarea dragarea1" style="width:50%;float:left;">');
            print('</div>');

            print('<div class="dragarea dragarea2" style="width:50%;float:left;">');
            print('</div>');


            print('<div style="clear:both;"></div>');
            print('<input type="button" class="button-secondary" onclick="NEXTFORM.addField();" value="add field" style="float:right;margin-right:5px;margin-top:25px;">');


            print('</td>');
            print('</tr>');


            print('<tr>');
            print(' <td colspan="4"><br/><br/>');

            print(' <input type="button" class="button-primary" onClick="saveWidgetForm();" value="'.lang('save').'" />');
            print(' <input type="button" class="button-secondary" onClick="document.location.href=\'admin.php?page=next_content%2Fforms.php\';" value="'.lang('list').'" />');

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

