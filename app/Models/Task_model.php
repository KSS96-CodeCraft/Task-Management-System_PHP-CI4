<?php

namespace App\Models;

use CodeIgniter\Model;

class Task_model extends Model
{



    public function insert_to_tb($tableName, $data)
    {
        $this->db->table($tableName)->insert($data);
        return $this->db->insertID(); // Returns the last inserted ID
    }

    public function get_specific_columns($table_name, $columns, $where_condition = null, $orderBy = null, $limit = null)
    {
        $builder = $this->db->table($table_name)->select($columns);

        if ($where_condition) {
            $builder->where($where_condition);
        }

        if ($orderBy) {
            foreach ($orderBy as $col => $dir) {
                $builder->orderBy($col, $dir);
            }
        }

        if ($limit) {
            $builder->limit($limit);
        }

        $query = $builder->get();

        if ($query === false) {

            return [];
        }

        return $query->getResultArray();
    }
    public function updateRecord($table, $data, $conditions)
    {
        $updated = $this->db->table($table)
            ->set($data)
            ->where($conditions)
            ->update();

        if ($updated) {
            return "updated";   // or return true / any custom message
        }

        return "not updated";   // or return false
    }
    public function deleteRecord(string $table, array $conditions): int
    {
        $this->db->table($table)->where($conditions)->delete();
        return $this->db->affectedRows();
    }

    public function getJoinedDataPagination(
        $mainTable,               // Main table name
        $joins = [],              // Array of joins (key = table, value = condition)
        $columns = '*',           // Columns to select
        $conditions = [],         // WHERE conditions
        $returnType = 'array',    // Return type: 'array' or 'row'
        $groupBy = '',              // Group By column(s)
        $orderBy = [],              // Order By column(s)
        $limit = 0,               // Limit for pagination
        $start = 0                // Offset for pagination
    ) {
        // Build query with selected columns
        $builder = $this->db->table($mainTable)->select($columns);

        // ✅ Apply joins dynamically if provided
        if (!empty($joins)) {
            foreach ($joins as $table => $condition) {
                $builder->join($table, $condition, 'left'); // Using LEFT JOIN by default
            }
        }

        // ✅ Apply conditions if provided
        if (!empty($conditions)) {
            $builder->where($conditions);
        }

        // ✅ Apply GROUP BY if provided
        if (!empty($groupBy)) {
            if (is_array($groupBy)) {
                $builder->groupBy(implode(', ', $groupBy));
            } else {
                $builder->groupBy($groupBy);
            }
        }
        if (!empty($orderBy)) {
            foreach ($orderBy as $column => $direction) {
                $builder->orderBy($column, $direction);
            }
        }
        // ✅ Apply limit and offset for pagination
        if ($limit > 0) {
            $builder->limit($limit, $start);
        }

        // ✅ Fetch data based on return type
        $query = $builder->get();

        // Return single row or multiple rows based on returnType
        if ($returnType === 'row') {
            return $query->getRowArray(); // Single row
        } else {
            return $query->getResultArray(); // Multiple rows
        }
    }
    
}
