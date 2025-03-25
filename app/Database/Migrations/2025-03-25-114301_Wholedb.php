<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Wholedb extends Migration
{
    public function up()
    {
        // Run the SQL file directly
        $sql = file_get_contents(APPPATH . 'Database/Migrations/wholedb.sql');
        
        // Split SQL by ; to execute each statement separately
        $queries = explode(';', $sql);
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $this->db->query($query);
            }
        }
    }

    public function down()
    {
        // Drop tables in reverse order to avoid foreign key constraints
        $this->forge->dropTable('user_server_permissions', true);
        $this->forge->dropTable('command_logs', true);
        $this->forge->dropTable('commands', true);
        $this->forge->dropTable('servers', true);
        $this->forge->dropTable('users', true);
    }
}
