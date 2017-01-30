<?php

    error_reporting(E_ERROR|E_WARNING);
    ini_set('display_errors', '1');

    require_once( realpath(dirname(__FILE__)). '/language.php');

?>

<div class="wrap">
    <h2>Status2</h2>

    <div id="transcontent" class="wrap">
    </div>

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

    var urls=[
        'www.next-kraftwerke.de',
        'www.next-kraftwerke.at',
        'www.next-kraftwerke.be',
        'www.suisse-next.ch',
        'www.centrales-next.fr',
        'www.next-kraftwerke.com',
        'www.elektrownie-next.pl',
        'www.centrali-next.it',
        ];
    var states=[];

    function updateList()
    {
        var html='';
        html+='<tr>';
        html+='<td>Site</td>';
        html+='<td>Version</td>';
        html+='<td>Plugins</td>';
        html+='</tr>';

        for(var i=0;i<states.length;i++)
        {
            html+='<tr>';
            html+='<td><a href="https://'+states[i].url+'">'+states[i].url+'</a></td>'
            html+='<td>'+states[i].version+'</td>'
            html+='<td>'+(states[i].count_plugins||0)+'</td>'
            html+='</tr>';
        }

        jQuery('#transcontent').html(html);
    }

    function getStatus(_url)
    {
        jQuery.ajax(
            {
                url: 'https://'+_url+"/wp-admin/admin-ajax.php?action=status&rnd="+Math.random()
            })
            .done(function( res )
            {
                try
                {
                    var result=JSON.parse(res);
                    console.log(result);
                    result.url=_url;
                    states.push(result);
                    updateList();
                    // jQuery('')
                }
                catch(e)
                {
                    states.push({'version':'error','url':_url});
                    updateList();
                    alert('ajax error!');
                    console.log(res);
                }
            }).error(function(res)
            {
                states.push({'version':'error','url':_url});
                updateList();

            });

    }


    for(var i=0;i<urls.length;i++)
    {
        getStatus(urls[i]);
    }







</script>


<?php





?>