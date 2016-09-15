
<div class="wrap">
    <h2>EMail Log</h2>
    huhu
</div>

<script>

function parseStructure(data)
{
    for(var i=0;i<data.content.length;i++)
    {
        console.log(data.content[i].title);

    }
}

jQuery.ajax(
    {
        url: "/wp-admin/admin-ajax.php?action=getLanguageStructure",
    })
    .done(function( res )
    {
        var data=JSON.parse(res);
        console.log(----);
        console.log(data);
        console.log(----);

        parseStructure(data);
    });


</script>



<?php






?>