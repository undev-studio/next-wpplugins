<?php

    error_reporting(E_ERROR|E_WARNING);
    ini_set('display_errors', '1');

    require_once( realpath(dirname(__FILE__)). '/language.php');

?>

<div class="wrap">
    <h2>Translations</h2>

    <div class="toolbar">

        <? echo nextTranslate('backend_translation_language'); ?>

        <select id="langselect" onchange="loadContent();">
            <option value="de">DE</option>
            <option value="at">AT</option>
            <option value="fr">FR</option>
            <option value="en">EN</option>
        </select>

    </div>
    <br/><br/>

    <div id="transcontent" class="wrap"></div>

</div>


<style type="text/css">

h3
{
    margin-top:0px;
    margin-bottom:6px;
}

.treeTable
{
    background-color: white;
}

.treeTable tr.head
{
    /*background-color: #eee;*/
    
}

.treeTable td
{
    padding-left: 5px;
}

.treeTable th
{
    /*background-color: #eee;*/
    text-align: left;
    padding:3px;
    border-bottom: 1px solid #ccc;
}

.treeTable tr
{
    border-bottom: 1px solid #ccc;
}
.treeTable tr:hover
{
    background-color: #ccc;
    cursor:pointer;
}

.treeTable input
{
    width:100%;
}

.missing
{
    background-color: rgba(255,200,200,0.4);
}

.editrow
{
    background-color: #f5f5f5;
}

.editrow td
{
    padding-top: 10px;
    padding-bottom: 10px;
    vertical-align: top;
}

.translation_preview
{
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 400px;
    overflow: hidden;
}

</style>
<script>

var NEXTLANG=
{

};

NEXTLANG

function htmlEscape(str) {
    return str
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

// I needed the opposite function today, so adding here too:
function htmlUnescape(str){
    return str
        .replace(/&quot;/g, '"')
        .replace(/&#39;/g, "'")
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>')
        .replace(/&amp;/g, '&');
}


NEXTLANG.setTranslation=function(key)
{
    jQuery.ajax(
        {
            url: "/wp-admin/admin-ajax.php?action=setLangTrans&l="+jQuery('#langselect').val()+"&key="+key+"&trans="+encodeURIComponent(htmlEscape(jQuery('#trans_'+key).val())),
        })
        .done(function( res )
        {
            try
            {
                var result=JSON.parse(res);
                console.log(result['key']);

                var cleanTrans = htmlUnescape(decodeURIComponent(result[key].replace(/<\/?[^>]+(>|$)/g, "")));

                console.log('trans got back',cleanTrans);
                jQuery('#preview_'+key).html(cleanTrans);
                jQuery('#preview_'+key).removeClass('missing');

                console.log('LANGUAGE ',jQuery('#langselect').val() );

                console.log('------');
                // console.log(res);
            }
            catch(e)
            {
                alert('translation error!');
                console.log(result);
            }
        });
}

    function indent(level,charr)
    {
        var str='';
        for(var i=0;i<level;i++) str+=charr;
        return str;
    }

    function editRow(which)
    {
        jQuery('#editrow_'+which).toggle();
    }

    function parseChild(el, level, _path)
    {
        var html='';
        level=level||0;

        for(var i=0;i<el.length;i++)
        {
            console.log( indent(level,' ') + el[i].name );

            var path=(_path||'')+el[i].name;

            html+='<tr class="" onclick="editRow(\''+path+'\');">';
            html+='<td>';
            if(el[i].childs) html+='<h3>';

            html+=''+indent(level,'&nbsp;&nbsp;&nbsp;')+el[i].name;

            if(el[i].childs) html+='</h3>';

            html+='</td>';
            var trans=NEXTLANG.content[path]||'';

            if(!el[i].childs)
            {
                if(trans)
                {
                    html+='<td><div class="translation_preview" id="preview_'+path+'">';
                    var cleanTrans = trans.replace(/<\/?[^>]+(>|$)/g, "");

                    html+=decodeURIComponent(cleanTrans);
                    html+='</div></td>';
                }
                else
                {
                    html+='<td width="50%" class="missing" id="preview_'+path+'">-</td>';
                }
            }
            html+='<td>';
            html+=el[i].comment || '';
            html+='</td>';

            html+='</tr>';

            if(!el[i].childs)
            {
                html+='<tr class="hidden editrow" id="editrow_'+path+'">';
                html+=' <td></td>';
                html+=' <td>';
                html+='     <textarea id="trans_'+path+'" style="width:100%;">'+htmlUnescape(trans)+'</textarea>';
                if(!el[i].childs) html+='<div style="opacity:0.5;">key: '+path+'</div>';
                html+='     <a class="button-primary" onclick="NEXTLANG.setTranslation(\''+path+'\');">Save</a>';

                html+=' </td>';
                html+=' <td>';
                html+='   <b>Default:</b><br/>';
                html+=el[i].default||'-';
                html+=' </td>';

                html+='</tr>';
            }

            if(el[i].childs) html+=parseChild(el[i].childs,level+1,path+'_');
        }

        return html;
    }


    function parseStructure(data)
    {
        var html='<table cellspacing="0" style="width:100%" class="treeTable">';

        html+='<tr class="head">';
        html+='  <th>Key</th>';
        html+='  <th>Translation</th>';
        html+='  <th>Note</th>';
        html+='</tr>';

        html+=parseChild(data.content);
        html+='</table>';

        jQuery('#transcontent').html(html);
    }


    function loadContent()
    {
        jQuery('#transcontent').html('');
        jQuery.ajax(
            {
                url: "/wp-admin/admin-ajax.php?action=getLanguageStructure",
            })
            .done(function( res )
            {
                // console.log(res);
                var structure=JSON.parse(res);
                console.log('loaded structure...');

                jQuery.ajax(
                    {
                        url: "/wp-admin/admin-ajax.php?action=getLanguage&l="+jQuery('#langselect').val(),
                    })
                    .done(function( res )
                    {

                        NEXTLANG.content=JSON.parse(res);

                        // console.log(res);
                        console.log('loaded content...');

                        parseStructure(structure);

                    });
                
            });
    }

    loadContent();


</script>


<?php





?>