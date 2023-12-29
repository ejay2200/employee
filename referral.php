<?php
include_once("db.php"); 

class Referral {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($data) {
        try {
            $evaluationSql = "
                INSERT INTO referral(EmployeeID,  ReferrerName, ReferrerEmail, ReferralDate, PerformanceID, ReferralStatusID)
                VALUES(:EmployeeID, :ReferrerName, :ReferrerEmail, NOW(), :PerformanceID, :ReferralStatusID)
            ";

            $evaluationStmt = $this->db->getConnection()->prepare($evaluationSql);

            // Bind values to placeholders
            $evaluationStmt->bindParam(':EmployeeID', $data['EmployeeID']);
            $evaluationStmt->bindParam(':ReferrerName', $data['ReferrerName']);
            $evaluationStmt->bindParam(':ReferrerEmail', $data['ReferrerEmail']);
            $evaluationStmt->bindParam(':PerformanceID', $data['PerformanceID']);
            $evaluationStmt->bindParam(':ReferralStatusID', $data['ReferralStatusID']);

            $evaluationStmt->execute();

            if ($evaluationStmt->rowCount() > 0) {
                return $this->db->getConnection()->lastInsertId();
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            throw $e; 
        }
    }

    public function read($id) {
        try {
            $connection = $this->db->getConnection();

            $sql = "SELECT * FROM referral WHERE ReferralID = :id";
            $stmt = $connection->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            $townCityData = $stmt->fetch(PDO::FETCH_ASSOC);

            return $townCityData;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            throw $e; 
        }
    }

    public function update($id, $data) {
        try {
            $sql = "UPDATE referral 
                    SET EmployeeID = :EmployeeID,
                        ReferrerName = :ReferrerName,
                        ReferrerEmail = :ReferrerEmail,
                        ReferralStatusID = :ReferralStatusID,
                        PerformanceID = :PerformanceID
                    WHERE ReferralID = :referral_id";
    
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->bindValue(':referral_id', $id);
            $stmt->bindValue(':EmployeeID', $data['EmployeeID']);
            $stmt->bindValue(':ReferrerName', $data['ReferrerName']);
            $stmt->bindValue(':ReferrerEmail', $data['ReferrerEmail']);
            $stmt->bindValue(':ReferralStatusID', $data['ReferralStatusID']);
            $stmt->bindValue(':PerformanceID', $data['PerformanceID']);
    
            $stmt->execute();
    
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            throw $e; 
        }
    }

    public function delete($id) {
        try {
            $sql = "DELETE FROM referral WHERE evaluation_id = :id";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true; 
            } else {
                return false; 
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            throw $e; 
        }
    }

    public function getAll() {
        try {
            $sql = "SELECT r.ReferralID, r.EmployeeID, CONCAT(e.first_name, ' ', e.last_name) as employee_name, jp.job_category,
                    r.ReferrerName, r.ReferrerEmail, p.performance_name, r.ReferralDate, rs.StatusName
            FROM referral as r
            LEFT JOIN employees AS e ON r.EmployeeID = e.idemployees
            LEFT JOIN service_records AS sr ON e.idemployees = sr.employees_idemployees
            LEFT JOIN job_positions AS jp ON sr.job_positions_idjob_positions = jp.idjob_positions
            LEFT JOIN referralstatus AS rs ON r.ReferralStatusID = rs.StatusID
            LEFT JOIN performance AS p ON r.PerformanceID = p.performance_id";
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e; 
        }
    }
    private function updatePerformanceEvaluation($employeeId) {
        try {
            $existingQuery = "SELECT * FROM performance_evaluation WHERE employee_id = :employee_id";
            $stmtExisting = $this->db->getConnection()->prepare($existingQuery);
            $stmtExisting->bindParam(':employee_id', $employeeId);
            $stmtExisting->execute();
    
            if ($stmtExisting->rowCount() > 0) {
                $updateQuery = "UPDATE performance_evaluation 
                                SET seniors_evaluation = (SELECT overall_performance FROM evaluations WHERE employee_id = :employee_id AND evaluation_type = 'senior'),
                                    self_evaluation = (SELECT overall_performance FROM evaluations WHERE employee_id = :employee_id AND evaluation_type = 'self'),
                                    peer_evaluation = (SELECT overall_performance FROM evaluations WHERE employee_id = :employee_id AND evaluation_type = 'peer'),
                                    overall_performance = (SELECT AVG(score) FROM (
                                        SELECT overall_performance as score FROM evaluations WHERE employee_id = :employee_id AND evaluation_type = 'senior'
                                        UNION ALL
                                        SELECT overall_performance as score FROM evaluations WHERE employee_id = :employee_id AND evaluation_type = 'self'
                                        UNION ALL
                                        SELECT overall_performance as score FROM evaluations WHERE employee_id = :employee_id AND evaluation_type = 'peer'
                                    ) AS subquery)
                                WHERE employee_id = :employee_id";
    
                $stmtUpdate = $this->db->getConnection()->prepare($updateQuery);
                $stmtUpdate->bindParam(':employee_id', $employeeId);
                $stmtUpdate->execute();
            } else {
                $insertQuery = "INSERT INTO performance_evaluation (employee_id, job_position_id, seniors_evaluation, self_evaluation, peer_evaluation, overall_performance)
                                VALUES (:employee_id,
                                        (SELECT jp.idjob_positions FROM job_positions as jp
                                        LEFT JOIN service_records as sr ON jp.idjob_positions = sr.job_positions_idjob_positions
                                        LEFT JOIN employees as e ON sr.employees_idemployees = e.idemployees
                                        WHERE e.idemployees = :employee_id),
                                        (SELECT overall_performance FROM evaluations WHERE employee_id = :employee_id AND evaluation_type = 'senior'),
                                        (SELECT overall_performance FROM evaluations WHERE employee_id = :employee_id AND evaluation_type = 'self'),
                                        (SELECT overall_performance FROM evaluations WHERE employee_id = :employee_id AND evaluation_type = 'peer'),
                                        (SELECT AVG(score) FROM (
                                            SELECT overall_performance as score FROM evaluations WHERE employee_id = :employee_id AND evaluation_type = 'senior'
                                            UNION ALL
                                            SELECT overall_performance as score FROM evaluations WHERE employee_id = :employee_id AND evaluation_type = 'self'
                                            UNION ALL
                                            SELECT overall_performance as score FROM evaluations WHERE employee_id = :employee_id AND evaluation_type = 'peer'
                                        ) AS subquery))";
    
                $stmtInsert = $this->db->getConnection()->prepare($insertQuery);
                $stmtInsert->bindParam(':employee_id', $employeeId);
                $stmtInsert->execute();
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            throw $e;
        }
    }
    
}
?>