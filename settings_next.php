<?php

    class nextSettings
    {
        const USE_MEGA_MENU='use_mega_menu';
        const FORCE_HTTPS='force_https';

        private $tablename='next_settings';

        static function getFileName()
        {
            $file=__DIR__.'/settings.json';
            return $file;
        }

        static function printError($error) { print('<br/><br/><div id="message" class="error below-h2"><p>SQL Error: '.$error.'</p></div>'); }
        static function printSQLError()
        { 
            global $wpdb; 
            global $DEBUG_SHOW_SQL;
            if($wpdb->last_error!='') 
            {
                nextSettings::printError($wpdb->last_error); 
                if($DEBUG_SHOW_SQL) nextSettings::printError($wpdb->last_query); 
                return true;
            }
            return false;
        }


        static function load()
        {
            global $wpdb;
            $rows = $wpdb->get_results('SELECT * FROM next_settings');

            $settings=array();

            foreach ($rows as $key => $row)
            {
                $settings[$row->param]=$row->value;
            }
            return $settings;
        }

        static function save($array)
        {
            global $wpdb;

            foreach ($array as $key => $row)
            {
                $rows = $wpdb->get_results('SELECT * FROM next_settings WHERE param="'.$key.'"');
                // echo $key .' - '.count($rows);

                if(count($rows)==1)
                {
                    $sql='UPDATE next_settings SET value="'.$row.'" WHERE param="'.$key.'"';
                    $wpdb->query( $sql );
                }
                if(count($rows)==0)
                {
                    $data['param']=$key;
                    $data['value']=$row;
                    $wpdb->insert('next_settings', $data);
                }

                nextSettings::printSQLError();

            }
        }
    }

?>