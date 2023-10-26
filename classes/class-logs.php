<?php

namespace personioIntegration;

use WP_List_Table;

/**
 * Handler for log-output in backend.
 */
class Logs extends WP_List_Table {

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

        // call parent constructor
        parent::__construct();
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return array
     */
    public function get_columns(): array
    {
        return array(
            'state' => __('state', 'personio-integration-light'),
            'date' => __('date', 'personio-integration-light'),
            'log' => __('log', 'personio-integration-light')
        );
    }

    /**
     * Get the table data
     *
     * @return array
     */
    private function table_data(): array
    {
        $sql = '
            SELECT `state`, `time` AS `date`, `log`
            FROM `'.$this->_tableName.'`
            ORDER BY `time` DESC';
        return $this->_wpdb->get_results( $sql, ARRAY_A );
    }

    /**
     * Get the log-table for the table-view.
     *
     * @return void
     */
    public function prepare_items(): void
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = [$columns, $hidden, $sortable];
        $this->items = $this->table_data();
    }

    /**
     * Define which columns are hidden
     *
     * @return array
     */
    public function get_hidden_columns(): array
    {
        return [];
    }

    /**
     * Define the sortable columns
     *
     * @return array
     */
    public function get_sortable_columns(): array
    {
        return ['date' => ['date', false]];
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return mixed
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection PhpSwitchCanBeReplacedWithMatchExpressionInspection
     */
    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'date':
                return Helper::get_format_date_time($item[ $column_name ]);

            case 'state':
                return $item[ $column_name ];

            case 'log':
                return nl2br($item[ $column_name ]);

            default:
                return print_r( $item, true ) ;
        }
    }

}
