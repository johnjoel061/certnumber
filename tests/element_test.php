<?php
// This file is part of the tool_certificate plugin for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains the version information for the code plugin.
 *
 * @package    certificateelement_certificatenumber
 * @copyright  2025 John Joel Alfabete <example@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use advanced_testcase;
use tool_certificate_generator;
use moodle_url;
use core_text;

/**
 * Unit tests for certificate number element.
 *
 * @package    certificateelement_certificatenumber
 * @group      certificateelement_certificatenumber
 * @group      tool_certificate
 * @covers     \certificateelement_certificatenumber\element
 */
final class element_test extends advanced_testcase {

    /**
     * Test set up.
    */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Get certificate generator
    * @return tool_certificate_generator
    */
    protected function get_generator(): tool_certificate_generator {
        return $this->getDataGenerator()->get_plugin_generator('tool_certificate');
    }

    /**
     * Test render_html for certificate number.
    */
    public function test_render_html_certificatenumber(): void {
        $certificate = $this->get_generator()->create_template((object)['name' => 'Certificate Test']);
        $pageid = $this->get_generator()->create_page($certificate)->get_id();
        $element = $this->get_generator()->create_element($pageid, 'certificatenumber',
            ['display' => \certificateelement_certificatenumber\element::DISPLAY_CERTIFICATENUMBER]);

        // Display should be a numeric certificate number.
        $output = strip_tags($element->render_html());
        $this->assertMatchesRegularExpression('/^\d+$/', $output);
    }

    /**
     * Test unique certificate number generation.
    */
    public function test_generate_certificate_number(): void {
        global $DB;
    
        $certificate = $this->get_generator()->create_template((object)['name' => 'Certificate Test']);
        $issue = $this->get_generator()->issue($certificate, $this->getDataGenerator()->create_user());
    
        // Fetch certificate number
        $certificatenumber = $DB->get_field('tool_certificate_issues', 'certificatenumber', ['id' => $issue->id]);
    
        // Debugging: Log output
        echo "Generated Certificate Number: " . ($certificatenumber ?: 'NULL') . "\n";
    
        // Ensure certificate number is assigned and valid
        $this->assertNotEmpty($certificatenumber, "Certificate number should not be empty.");
        $this->assertIsNumeric($certificatenumber, "Certificate number should be numeric.");
    }
    
    /**
     * Test save_unique_data for certificate number element.
    */
    public function test_save_unique_data(): void {
        global $DB;

        $certificate = $this->get_generator()->create_template((object)['name' => 'Certificate Test']);
        $pageid = $this->get_generator()->create_page($certificate)->get_id();
        $element = $this->get_generator()->new_element($pageid, 'certificatenumber');
        
        $newdata = (object)['display' => \certificateelement_certificatenumber\element::DISPLAY_CERTIFICATENUMBER];
        $expected = json_encode($newdata);
        
        $element->save_form_data($newdata);
        $record = $DB->get_record('tool_certificate_elements', ['id' => $element->get_id()]);
        
        $this->assertEquals($expected, $record->data);
    }
}


