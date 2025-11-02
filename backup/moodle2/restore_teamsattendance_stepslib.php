<?php
// This file defines the structure of the data to be restored for the Teams Meeting Attendance plugin.

class restore_zoomattendance_activity_structure_step extends restore_activity_structure_step {

    /**
     * Define the structure of the restore data.
     *
     * @return restore_path_element[] The paths to be restored.
     */
    protected function define_structure() {
        $paths = [];

        // Define the root element describing the zoomattendance instance.
        $zoomattendance = new restore_path_element('zoomattendance', '/activity/zoomattendance');
        $paths[] = $zoomattendance;

        // Define attendance records
        $attendance = new restore_path_element('zoomattendance', '/activity/zoomattendance/attendance');
        $paths[] = $attendance;

        return $paths;
    }

    /**
     * Process the zoomattendance element.
     *
     * @param array $data The data to process.
     */
    public function process_zoomattendance($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // Insert the zoomattendance record
        $newitemid = $DB->insert_record('zoomattendance', $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Process the attendance element.
     *
     * @param array $data The data to process.
     */
    public function process_zoomattendance($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->sessionid = $this->get_new_parentid('zoomattendance');
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('zoomattendance', $data);
        $this->set_mapping('zoomattendance', $oldid, $newitemid);
    }

    /**
     * After the restore process, add related files.
     */
    protected function after_execute() {
        $this->add_related_files('mod_zoomattendance', 'intro', null);
    }
}
