<?php 

require_once(__DIR__ . '/../Engine/DatabaseConnection.php');

class GeneralModel extends DatabaseConnection
{
    public function findFirst()
    {
        $sql = "SELECT * FROM general LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        if ($settings = $stmt->fetch()) {
            return $settings;
        }
        return null;
    }

    public function findOneByCriteria(array $criteria)
    {
        $params = '';
        $i = 0;
        foreach ($criteria as $key => $value) {
            if ($i === 0) {
                $params .= "$key=:$key";
            } else {
                $params .= " AND $key=:$key";
            }
            $i++;
        }

        $sql = "SELECT * FROM general WHERE $params";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($criteria);
        if ($user = $stmt->fetch()) {
            return $user;
        }
        return null;
    }
}