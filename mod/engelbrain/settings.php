<?php
// This file is part of Moodle - http://moodle.org/
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
 * Plugin administration pages are defined here.
 *
 * @package     mod_engelbrain
 * @category    admin
 * @copyright   2025 Panomity GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    // Header for the settings page.
    $settings->add(new admin_setting_heading(
        'mod_engelbrain/settings_header', 
        get_string('settings_header', 'mod_engelbrain'),
        ''
    ));

    // School API Key setting.
    $settings->add(new admin_setting_configtext(
        'mod_engelbrain/school_api_key',
        get_string('school_api_key', 'mod_engelbrain'),
        get_string('school_api_key_desc', 'mod_engelbrain'),
        '',
        PARAM_TEXT
    ));

    // API Endpoint setting.
    $settings->add(new admin_setting_configtext(
        'mod_engelbrain/api_endpoint',
        get_string('api_endpoint', 'mod_engelbrain'),
        get_string('api_endpoint_desc', 'mod_engelbrain'),
        get_string('default_api_endpoint', 'mod_engelbrain'),
        PARAM_URL
    ));
} 