<?php
// Patch: recupera automaticamente organizer_email dal meeting Zoom

sed -i '181,185s/.*/    \/\/ Validate required fields\n    if (empty($data->name) || empty($data->meeting_id)) {\n        throw new moodle_exception('"'"'missingrequiredfield'"'"', '"'"'mod_zoomattendance'"'"');\n    }\n    \n    \/\/ Recupera il meeting Zoom e l'"'"'email dell'"'"'organizzatore\n    $zoom_meeting = $DB->get_record('"'"'zoom'"'"', ['"'"'meeting_id'"'"' => $data->meeting_id], '"'"'*'"'"', MUST_EXIST);\n    if (!$zoom_meeting || empty($zoom_meeting->host)) {\n        throw new moodle_exception('"'"'error_meeting_host_not_found'"'"', '"'"'mod_zoomattendance'"'"');\n    }\n    $data->organizer_email = $zoom_meeting->host;/' lib.php
