<?php

namespace personioIntegration;

/**
 * Handler for logging in this plugin.
 */
class Log {
    // database-object
    private $_wpdb;

    // name for own database-table.
    private string $_tableName;

    /**
     * Constructor for Logging-Handler.
     */
    public function __construct() {
        global $wpdb;

        // get the db-connection
        $this->_wpdb = $wpdb;

        // set the table-name
        $this->_tableName = $this->_wpdb->prefix . 'personio_import_logs';
    }

    /**
     * Create the logging-table in the database.
     *
     * @return void
     */
    public function createTable() {
        $charset_collate = $this->_wpdb->get_charset_collate();

        // table for import-log
        $sql = "CREATE TABLE $this->_tableName (
            `id` mediumint(9) NOT NULL AUTO_INCREMENT,
            `time` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            `log` text DEFAULT '' NOT NULL,
            `state` varchar(40) DEFAULT '' NOT NULL,
            UNIQUE KEY id (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    /**
     * Add a single log-entry.
     *
     * @param $log
     * @param $state
     * @return void
     */
    public function addLog( $log, $state ) {
        $this->_wpdb->insert($this->_tableName, [
            'time' => date('Y-m-d H:i:s'),
            'log' => $log,
            'state' => $state
        ]);
        $this->cleanLog();
    }

    /**
     * Delete all entries which are older than X days.
     *
     * @return void
     */
    public function cleanLog() {
        $sql = sprintf("DELETE FROM `".$this->_tableName."` WHERE `time` < DATE_SUB(NOW(), INTERVAL %d DAY)", get_option('personioIntegrationMaxAgeLogEntries', 50));
        $this->_wpdb->query($sql);
    }
}