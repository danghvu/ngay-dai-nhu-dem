<?php

define ('FB_GROUP_ID', '188053074599163');

class LoadPaperAPI {

    private $facebook;

    private function query($q) {
        return 
            $this->facebook->api(
                array( 
                    'method'=>'fql.multiquery',
                    'queries'=>$q,
                )
            );
    }

    public function __construct($fb) {
        $this->facebook = $fb;
    }

    public function get_post_no_comment() {
        $queries = '{
            "gs_no_comment":"SELECT created_time, actor_id, permalink, message, comments FROM stream WHERE source_id = '.FB_GROUP_ID.' AND comments.count = 0 order by created_time desc LIMIT 100",
            "actor_info":"SELECT uid, name, pic_square FROM user WHERE uid IN (SELECT actor_id FROM #gs_no_comment)",
        }';
        
        $rs = $this->query($queries);            
        foreach ($rs as &$r) {
            $data = $r['fql_result_set'];
            if ($r['name'] === 'gs_no_comment')
                $stream = $data;
            else
                $owner = $data;
        }

        foreach ($owner as &$o) {
            $owner[ $o['uid'] ] = &$o;
        }

        foreach ($stream as &$s) {
            $s['owner'] = $owner[ $s['actor_id'] ];
        }

        return $stream;
    }
}

?>
