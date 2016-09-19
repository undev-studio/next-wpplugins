
<div class="wrap">
    <h2>EMail Log</h2>

<div class="toolbar">
Language File:    
    <select id="langselect" onchange="loadContent();">
        <option value="de">DE</option>
        <option value="at">AT</option>
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
    background-color: #eee;
    border-bottom: 2px solid #ccc;
}

.treeTable td
{
    padding-left: 5px;
}

.treeTable th
{
    background-color: #eee;
    text-align: left;
    padding:3px;
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

</style>
<script>

var NEXTLANG=
{

};



    function indent(level,charr)
    {
        var str='';
        for(var i=0;i<level;i++)str+=charr;
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
            if(!el[i].childs)
            {

                var trans=NEXTLANG.content[path];
                if(trans)
                {
                    html+='<td width="50%">';
                    html+=trans;
                    html+='</td>';
                }
                else
                {
                    html+='<td width="50%" class="missing">-</td>';
                }
            }
            html+='<td>';
            html+=el[i].comment || '';
            html+='</td>';

            html+='</tr>';

            if(!el[i].childs)
            {
                html+='<tr class="hidden" id="editrow_'+path+'">';
                html+=' <td></td>';
                html+=' <td colspan="12">';
                html+='     <input value="'+el[i].default+'"/><br/>';
                html+='     <a class="button-primary" onclick="">Save</a>';

                if(!el[i].childs) html+='<br/>Path: ['+path+']';

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


                        console.log(res);
                        console.log('loaded content...');

                        parseStructure(structure);

                    });
                
            });
    }

    loadContent();


</script>


<?php





?>