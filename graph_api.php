<?php
// This file centralizes Zoom API calls for the Zoom Meeting Attendance plugin.
// It wraps the existing mod_zoom webservice functionality.

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/zoom/classes/webservice.php');

/**
 * Get Zoom API webservice instance.
 * Uses the existing mod_zoom configuration.
 *
 * @return \mod_zoom\webservice
 */
function get_zoom_webservice() {
    return new \mod_zoom\webservice();
}

/**
 * Fetch meeting participants from Zoom.
 *
 * @param string $meeting_uuid The meeting UUID.
 * @param bool $webinar Whether it's a webinar (default false).
 * @return array Array of participants.
 */
function get_zoom_meeting_participants($meeting_uuid, $webinar = false) {
    $webservice = get_zoom_webservice();
    return $webservice->get_meeting_participants($meeting_uuid, $webinar);
}
