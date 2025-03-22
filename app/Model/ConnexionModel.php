<?php 

require_once(__DIR__ . '/../Engine/DatabaseConnection.php');

class ConnexionModel extends DatabaseConnection
{
    public function insert(array $connexion)
    {
        $sql = "INSERT INTO connexions (idUtil, dateCnx, Adresse_IP) VALUES (:idUtil, :dateCnx, :Adresse_IP)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($connexion);
        return $this->db->lastInsertId('connexions');
    }
}