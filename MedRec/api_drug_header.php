<?php
require_once 'db_connection.php';

// Get the action
$action = $_POST['action'] ?? '';

error_log("Received action: " . $action);

switch ($action) {
    case 'getDrugsBySymptomID':
        getDrugsBySymptomID($conn);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        break;
}

function getDrugsBySymptomID($conn) {
    $symptomID = $_POST['symptomID'] ?? '';

    error_log("Received symptomID: " . $symptomID);

    if (empty($symptomID)) {
        echo json_encode(["status" => "error", "message" => "SymptomID is required"]);
        return;
    }

    $sql = "
        SELECT 
            dd.DrugID,
            dd.BrandName,
            dd.GenericName,
            dd.Active_Ingredient,
            dd.Dosage,
            dd.Dosage_Form,
            dd.Manufacturer,
            dd.Manufacture_Date,
            dd.SideEffects,
            dd.Price,
            dd.DrugImage
        FROM 
            symptoms s
        JOIN 
            drug_header dh ON s.DrugHeaderID = dh.DrugHeaderID
        JOIN 
            drug_details dd ON dh.DrugHeaderID = dd.DrugHeaderID
        WHERE 
            s.SymptomID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $symptomID);
    $stmt->execute();
    $result = $stmt->get_result();

    $drugs = [];
    while ($row = $result->fetch_assoc()) {
        $drugs[] = $row;
    }

    echo json_encode(["status" => "success", "drugs" => $drugs]);
}
?>
