<?php

$DEBUG_SHOW_SQL = false;

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

function printError($error)
{
  print('<br/><br/><div id="message" class="error below-h2"><p>SQL Error: ' . $error . '</p></div>');
}

function printSQLError()
{
  global $wpdb;
  global $DEBUG_SHOW_SQL;
  if ($wpdb->last_error != '') {
    printError($wpdb->last_error);
    if ($DEBUG_SHOW_SQL) printError($wpdb->last_query);
    return true;
  }
  return false;
}

error_reporting(E_ERROR | E_WARNING);
ini_set('display_errors', '1');

if (!isset($jsonFileName)) $jsonFileName = str_replace('php', 'json', __FILE__);
$jstr = file_get_contents($jsonFileName);
$json = json_decode($jstr, true);


if (!isset($json)) {
  printError("JSON decode error..." . $jsonFileName);
  die();
}


$tablename = $json['tablename'];
$idName = $json['idName'];
$sortable = $json['sortable'];
$orderby = $json['orderby'];

$editable = true;
if (isset($json['editable'])) $editable = $json['editable'];

$exportable = false;
if (isset($json['exportable'])) $exportable = $json['exportable'];

$path = $json['path'];

$thumbnailSize = $json['thumbnailSize'];
$thumbnailSizeEdit = $json['thumbnailSizeEdit'];


wp_enqueue_media();


$firstTimeEdit = false;


function lang($which)
{
  global $json;
  if (!isset($json['l'][$which])) return '_' . $which;
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

  if ($json['filter'] && $_REQUEST['filter'] != '') {
    foreach ($json['filter'] as $f) {
      if ($_REQUEST['filter'] == $f['name']) {
        if ($f['sqlWhere'] != '') {
          $sql .= ' WHERE ' . $f['sqlWhere'] . ' ';
        }
      }
    }
  }

  if ($sortable) $sql .= ' ORDER BY sort;';
  else {
    if ($orderby != '') $sql .= ' ORDER BY ' . $orderby . ';';
  }


  return $sql;
}

?>


<div class="wrap unlistwrap">
    <h2><?php echo $json['title']; ?></h2>

    <style>
      <?php include('unlist.css'); ?></style>

    <script type="text/javascript">

      var untags = [];

      function unlRemoveTag(id, tagid, title) {
        for (var i = 0; i < untags[id].length; i++) {
          if (untags[id][i] == tagid) {
            untags[id].splice(i, 1);
          }
        }

        jQuery('#tags_' + id).val(untags[id].toString());
        jQuery('#tagdestvalue' + tagid).remove();
      }

      function unlAddTag(id, tagid, title) {
        var found = false;
        if (!untags[id]) untags[id] = [];

        for (var i = 0; i < untags[id].length; i++) {
          if (untags[id][i] == tagid) {
            found = true;
          }
        }
        if (!found) {
          var html = '';
          html += '<div value="' + tagid + '" id="tagdestvalue' + tagid + '">';
          html += title;
          html += '&nbsp;&nbsp;<a onclick="unlRemoveTag(\'' + id + '\',' + tagid + ',\'' + title + '\')">remove</a>';
          html += '</div>';

          jQuery('#tagsdest' + id).append(html);

          untags[id].push(tagid);
          jQuery('#tags_' + id).val(untags[id].toString());
        }
      }

      function saveOrder() {
        var idStr = "";

        jQuery("#unlistTable").find("td.sortid").each(function (i, nrElt) {
          var id = jQuery(nrElt).text();
          idStr += id + ",";
        });

        document.location.href = "?page=<?php print($path) ?>&func=saveorder&order=" + idStr;
      }


      function selectImage(id) {
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
          title: 'Choose Image',
          button: {
            text: 'Choose Image'
          },
          multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function () {
          attachment = custom_uploader.state().get('selection').first().toJSON();
          jQuery('#' + id).val(attachment.url);
          jQuery('#' + 'img_' + id).attr('src', attachment.url);
        });

        custom_uploader.open();
      }

      function unrowImport() {
        var imp = prompt('Paste the copied export data');

        var impData = JSON.parse(imp);
        if (!Array.isArray(impData)) {
          impData = [impData];
        }

        var newnum = jQuery('.unrow_head').length;

        for (var j in impData) {
          var data = impData[j];
          for (var i in data) {
            if (i === 'func' || i === 'which') continue;
            var inputField = jQuery(':input[name="' + i + '"]');
            if (inputField.length) {
              inputField.val(data[i]);
            }

            var parts = i.split('_');
            parts[1] = newnum;
            var index = parts.join('_');
            jQuery('#unrowmetabox').append('<textarea name="' + index + '">' + data[i] + '</textarea>');
          }
          newnum++;

        }
        jQuery('#publish').click();

      }

      function unrowCloseExport() {
        jQuery('#exportdialog').hide();


      }

      function unrowExport(which) {

        function serializeForm(selector) {
          var json = {};
          jQuery(selector).find(':input').each(function () {
            json[jQuery(this).attr('name')] = jQuery(this).val();
          });
          jQuery(selector).find('select').each(function () {
            json[jQuery(this).attr('name')] = jQuery(this).val();
          });


          return json;
        }

        var form = {};
        if (which !== undefined) {
          form = serializeForm('.unrow' + which);
        } else {
          form = [];
          var count = 0;

          while (jQuery('.unrow' + count).length > 0) {
            var single = serializeForm('.unrow' + count);
            form.push(single);
            count++;
          }
        }

        var txt = JSON.stringify(form);
        jQuery('#exportdialog').show();
        jQuery('#exportText').val(txt);
        document.getElementById("exportText").select();


      }

    </script>

  <?php


  function getPageSelect($fieldName, $value)
  {
    $html = '';
    $html .= '<select name="' . $fieldName . '" >';

    $args = array();
    $pages = get_pages($args);

    $html .= '<option value="">None</option>';

    foreach ($pages as $kpp => $p) {
      $sel = '';
      $v = '' . $p->ID;
      if ($v == $value) $sel = "SELECTED";

      $html .= '<option value="' . $p->ID . '" ' . $sel . '>';
      $depth = count(get_ancestors($p->ID, "page"));
      for ($i = 0; $i < $depth; $i++) $html .= '&nbsp;&nbsp;&nbsp;';
      $html .= $p->post_title;
      $html .= '</option>';
    }

    $html .= '</select>';

    return $html;
  }


  global $wpdb;

  $func = $_REQUEST['func'];

  if ($func == 'save') {
    $sql = 'UPDATE ' . $tablename . ' SET ';

    $count = 0;
    foreach ($json['vars'] as $f) {

      $val = $_REQUEST[$f['name']];

      if ($val == 'TIMESTAMP') {
        $r_day = $_REQUEST[$f['name'] . '_date_day'];
        $r_month = $_REQUEST[$f['name'] . '_date_month'];
        $r_year = $_REQUEST[$f['name'] . '_date_year'];
        $r_minute = $_REQUEST[$f['name'] . '_date_minute'];
        $r_hour = $_REQUEST[$f['name'] . '_date_hour'];

        $datestr = $r_year . '-' . $r_month . '-' . $r_day . ' ' . $r_hour . ':' . $r_minute . ':00';

        // print('$datestr '.$datestr);
        $val = $datestr;
      }

      if ($f['input'] != 'divider') {
        if ($count != 0) $sql .= ",";
        $sql .= $f['name'] . '="' . $val . '" ';

      }

      $count++;
    }

    $sql .= ' WHERE ' . $idName . ' = ' . (int)$_REQUEST['which'];
    $wpdb->query($sql);

    // print($sql);

    $func = "";
    printSQLError();
  }


  if ($func == 'saveorder') {
    $arr = split(",", $_REQUEST["order"]);

    $count = 0;
    foreach ($arr as &$id) {
      if ($id != '') {
        $wpdb->query('UPDATE ' . $tablename . ' SET sort = "' . $count . '" WHERE ' . $idName . ' = ' . $id);
        printSQLError();
      }

      $count++;
    }
    $func = "";


  }

  if ($func == 'delete') {
    $which = (int)$_REQUEST['which'];
    $wpdb->delete('' . $tablename . '', array('id' => $which));
    $func = "";

    printSQLError();
  }

  if ($func == 'create') {
    $data = array();

    foreach ($json['vars'] as $f) {
      $data[$f['name']] = $f['default'];
    }

    $wpdb->insert('' . $tablename . '', $data);
    $func = "";


    $error = printSQLError();
    if (!$error) {
      $func = 'edit';
      $_REQUEST['which'] = $wpdb->insert_id;

      $firstTimeEdit = true;
    }
  }

  if ($func == '') {
    global $wpdb;


    $rows = $wpdb->get_results(getListingSQL());

    printSQLError();

    print('<br/><br/><a class="button-primary" href="?page=' . $path . '&func=create">' . lang('create') . '</a>');

    print('<div class="wrap">');


    if ($json['filter']) {
      print('<div class="unlistFilterContainer">');

      print('Filter: &nbsp;');

      $currentFilter = $_REQUEST['filter'];

      $count = 0;
      foreach ($json['filter'] as $f) {
        $cssclass = "";
        if ($f['name'] == $currentFilter) {
          $cssclass = 'unlistFilterActive';
        }
        print('<a href="?page=' . $path . '&filter=' . $f['name'] . '" class="' . $cssclass . '">' . $f['title'] . '</a> ');


        if ($count < sizeof($json['filter']) - 1) print('&nbsp; ');
        $count++;
      }

      print('</div>');
    }


    print('<br/><table id="unlistTable" class="wp-list-table widefat sorted_table" >');
    print('<thead>');
    print('<tr>');

    foreach ($json['vars'] as $f) {
      if (!$f['visible']) continue;
      print('<th>');
      print($f['title']);
      print('</th>');
    }

    print('<th></th>');

    print('</tr>');
    print('</thead>');
    print('<tbody>');

    foreach ($rows as &$row) {
      print('<tr>');

      foreach ($json['vars'] as $f) {
        if (!$f['visible']) continue;
        print('<td>');

        if ($f['input'] == 'media') {
          print('<img style="width:' . $thumbnailSize . 'px;" src="' . $row->{$f['name']} . '"/> ');
        } else
          print($row->{$f['name']});
        print('</td>');
      }

      print('<td>');
      if ($editable) {
        print('<a href="?page=' . $path . '&func=edit&which=' . $row->id . '">' . lang('edit') . '</a> | ');
        print('<a href="?page=' . $path . '&func=delete&which=' . $row->id . '" onclick="return confirm(\'' . lang('delete') . ' ?\')">' . lang('delete') . '</a> ');
      }

      print('</td>');
      print('<td class="sortid">' . $row->id . '</td>');
      print('</tr>');
    }
    print('</tbody>');
    print('</table>');
    print('</div>');

    print('<div class="metainf">' . sizeof($rows) . ' Entries.</div>');

    if ($sortable) print('<br/><a class="button-primary" onclick=" saveOrder();">' . lang('save_order') . '</a>');
  }


  if ($func == 'edit') {
    global $wpdb;
    $rows = $wpdb->get_results('SELECT * FROM ' . $tablename . ' WHERE ' . $idName . '=' . (int)$_REQUEST['which'] . ';');


    print('<div class="postbox-container">');
    print('<div class="postbox">');
    print('<h3 style="padding:10px;">' . lang('edit') . ':</h3>');
    print('<div class="inside">');
    if ($exportable) {
      print('<div style="width: 100%; text-align: right;"><a href="#" onclick="unrowExport(' . (int)$_REQUEST['which'] . ');">Export</a> ||  <a href="#" onclick="unrowImport(' . (int)$_REQUEST['which'] . ');">Import</a></div>');
      print('<div id="exportdialog" class="unrow_row_menu" style="display:none;">');
      print('<h3>Export</h3>');
      print('<b>Copy this text and paste it into the import dialog to copy the module content.<br/><br/></b>');
      print('<textarea id="exportText" style="width:500px;height:300px;"></textarea><br/><br/>');
      print('<a class="button" onclick="unrowCloseExport();">close</a>');
      print('</div>');
    }
    print('<form method="post" action="" enctype="multipart/form-data" class="unrow' . (int)$_REQUEST['which'] . '">');
    print('<input type="hidden" name="func" value="save" />');
    print('<input type="hidden" name="which" value="' . (int)$_REQUEST['which'] . '" />');

    foreach ($rows as &$row) {
      print('<table>');

      foreach ($json['vars'] as $f) {
        $visi = '';
        if ($f['editable'] == false) $visi = 'display:none;';

        $maxlength = $f['maxLength'];
        if (isset($maxlength) && $maxlength != '') $maxlength = 'maxlength="' . $maxlength . '"';


        $row->{$f['name']} = htmlentities($row->{$f['name']});

        print('<tr><td> </td></tr>');

        if ($f['input'] == 'input') {
          print('<tr style="' . $visi . '">');
          print(' <td valign="middle" class="edittitle">' . $f['title'] . ':</td>');
          print(' <td><input ' . $maxlength . ' type="text" style="width:600px;" name="' . $f['name'] . '" value="' . $row->{$f['name']} . '"/></td>');
          print('</tr>');
        }

        if ($f['input'] == 'date' || $f['input'] == 'datetime') {
          $ts = strtotime($row->{$f['name']});
          if ($row->{$f['name']} == '0000-00-00 00:00:00') $ts = time();

          $dateDay = gmdate('d', $ts);
          $dateMonth = gmdate('m', $ts);
          $dateYear = gmdate('Y', $ts);

          $dateHour = gmdate('H', $ts);
          $dateMinute = gmdate('i', $ts);

          print('<tr style="' . $visi . '">');
          print('<td valign="middle" class="edittitle">' . $f['title'] . ':</td>');
          print('<td>');

          print('<input type="hidden" name="' . $f['name'] . '" value="TIMESTAMP"/>');

          print('<input maxlength="2" type="text" style="width:30px;" name="' . $f['name'] . '_date_day"    value="' . $dateDay . '"/>.');
          print('<input maxlength="2" type="text" style="width:30px;" name="' . $f['name'] . '_date_month"  value="' . $dateMonth . '"/>.');
          print('<input maxlength="4" type="text" style="width:50px;" name="' . $f['name'] . '_date_year"   value="' . $dateYear . '"/>');

          $class = 'hidden';
          if ($f['input'] == 'datetime') $class = '';

          print('&nbsp;&nbsp;<span class="' . $class . '">');
          print('<input maxlength="2" type="text" style="width:50px;" name="' . $f['name'] . '_date_hour"   value="' . $dateHour . '"/>:');
          print('<input maxlength="2" type="text" style="width:50px;" name="' . $f['name'] . '_date_minute" value="' . $dateMinute . '"/>');
          print('</span>');

          print('</td>');
          print('</tr>');
        }

        if ($f['input'] == 'textarea') {
          print('<tr style="' . $visi . '">');
          print(' <td valign="top" class="edittitle">' . $f['title'] . ':</td>');
          print(' <td><textarea ' . $maxlength . ' type="text" style="height:160px;width:600px;" name="' . $f['name'] . '" >' . $row->{$f['name']} . '</textarea></td>');
          print('</tr>');
        }


        if ($f['input'] == 'page') {
          print('<tr style="' . $visi . '">');
          print(' <td valign="top" class="edittitle">' . $f['title'] . ':</td>');
          print(' <td>' . getPageSelect($f['name'], $row->{$f['name']}) . '</td>');
          print('</tr>');

        }

        if ($f['input'] == 'language') {
          if (function_exists('pll_the_languages')) {
            print('<tr style="' . $visi . '">');
            print('<td valign="middle" class="edittitle">' . $f['title'] . ':</td>');
            print('<td>');
            print('<select name="' . $f['name'] . '">');

            global $polylang;
            if (isset($polylang)) $languages = $polylang->get_languages_list();

            foreach ($languages as $lang) {
              // $l['selected']='';
              $sel = '';

              if ($lang->slug == $row->{$f['name']}) $sel = " selected ";
              if (pll_current_language() == $lang->slug) $l['selected'] = " selected";
              // $langs[]=$l;
              // $s="";

              print('<option value="' . $lang->slug . '" ' . $sel . '>' . $lang->name . '</option>'); //.' // '.$row->{$f['name']}
            }

            print('</select>');

            print('</td>');
            print('</tr>');
          }
        }


        if ($f['input'] == 'select') {
          print('<tr style="' . $visi . '">');
          print('<td valign="middle" class="edittitle">' . $f['title'] . ':</td>');
          print('<td>');

          print('<select name="' . $f['name'] . '">');

          foreach ($f['options'] as $key => $opt) {
            $sel = '';
            if ($row->{$f['name']} == $opt['value']) $sel = " selected ";
            print('<option ' . $sel . ' value="' . $opt['value'] . '">' . $opt['title'] . '</option>');
          }

          print('</select>');

          print('</td>');
          print('</tr>');
        }

        if ($f['input'] == 'media') {
          print('<tr style="' . $visi . '">');
          print('  <td valign="top" class="edittitle">' . $f['title'] . ':</td>');
          print('  <td>');


          print('<img id="img_media_' . $f['name'] . '" src="' . $row->{$f['name']} . '" style="width:' . $thumbnailSizeEdit . 'px;">');
          print('<br/>');
          print('<input type="text" style="width:600px;" id="media_' . $f['name'] . '" name="' . $f['name'] . '" value="' . $row->{$f['name']} . '">');
          print('  <a onclick="selectImage(\'media_' . $f['name'] . '\')">&nbsp;' . lang('select_file') . '');
          print('</a>');
          print('  </td>');

          print('</tr>');
        }

        if ($f['input'] == 'divider') {
          print('<tr><td colspan="2"><br/><hr/>&nbsp;</td></tr>');
        }

        if ($f['input'] == 'tags') {
          $id = $f['name'];
          $items = array();

          if ($f['values'] == 'wpCategories') {
            $categories = get_categories(array('hide_empty' => 0, 'name' => 'category_parent', 'orderby' => 'name', 'hierarchical' => false, 'show_option_none' => __('None')));
            foreach ($categories as $k => $cat) {
              $a = array();
              $a['title'] = $cat->name;
              $a['id'] = $cat->term_id;
              $items[] = $a;
            }
          }

          if ($f['values'] == 'wpTags') {
            $tags = get_tags(array('hide_empty' => 0, 'name' => 'category_parent', 'orderby' => 'name', 'hierarchical' => false, 'show_option_none' => __('None')));
            foreach ($tags as $k => $t) {
              $a = array();
              $a['title'] = $t->name;
              $a['id'] = $t->term_id;
              $items[] = $a;
            }
          }

          print('<tr style="' . $visi . '">');
          print('  <td valign="top">' . $f['title'] . ':</td>');
          print('  <td>');

          print('<div id="tagssource' . $id . '" class="unltagbox">');
          foreach ($items as $key => $item) {
            print('<div value="' . $item['id'] . '"  title="' . $item['title'] . '" >');
            print($item['title']);

            print('&nbsp;&nbsp;<a onclick="unlAddTag(\'' . $id . '\',' . $item['id'] . ',\'' . $item['title'] . '\');">add</a>');

            print('</div>');
          }
          print('</div>');

          print('<div id="tagsdest' . $id . '" class="unltagbox">');
          print('</div>');

          print('  <input type="hidden" style="width:600px;" id="tags_' . $f['name'] . '" name="' . $f['name'] . '" value="' . $row->{$f['name']} . '">');
          print('  </td>');

          print('</tr>');

          print('<script>');
          $arr = explode(",", $row->{$f['name']});
          foreach ($arr as $key => $v) {
            $title = '';
            foreach ($items as $key => $item) {
              if ($item['id'] == $v) $title = $item['title'];
            }

            print('unlAddTag(\'' . $id . '\',' . $v . ',\'' . $title . '\'); ');
          }
          print('</script>');

        }
      }

      print('<tr>');
      print(' <td></td>');
      print(' <td><br/><br/>');
      print(' <input type="submit" class="button-primary" value="' . lang('save') . '" />');
      print(' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?page=' . $path . '&func=delete&which=' . $row->id . '" onclick="return confirm(\'' . lang('delete') . ' ?\')">' . lang('delete') . '</a> ');
      print('</td>');
      print('</tr>');

      print('<tr>');
      print('<td colspan="2"');
      print('<div id="exportdialog" class="unrow_row_menu" style="display:none;">');
      print('<h3>Export</h3>');
      print('<b>Copy this text and paste it into the import dialog to copy the module content.<br/><br/></b>');
      print('<textarea id="exportText" style="width:500px;height:300px;"></textarea><br/><br/>');
      print('<a class="button" onclick="unrowCloseExport();">close</a>');
      print('</div>');
      print('</td></tr>');


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

<script>
  <?php
  if ($sortable) print(" jQuery('.sorted_table tbody').sortable({ containerSelector: 'table', itemSelector: 'tr', placeholder: 'placeholder' }); ");
  ?>
</script>

