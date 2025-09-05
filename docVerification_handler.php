<?php
if (session_status() == PHP_SESSION_NONE):
    session_start();
endif;
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    // bad access;
    http_response_code(401); // Unauthorized
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access. Please log in.']);
    exit();
}

require_once('../dbConnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $startRow = $_GET['start'];          // start position
    $rowPerPage = $_GET['rows'];
    $page = $_GET['page'];
    $searchValue = $_GET['search'];
    $docStatusFilter = $_GET['docStatusFilter'] ?? null;


    $searchQuery = "";
    $params = [];
    if ($searchValue != '') {
        $searchQuery = "AND (oaf.NameinEngish LIKE :search OR egt.enroll LIKE :search OR oaf.mobile1 LIKE :search)";
        $params[':search'] = "%$searchValue%";
    }

    $docStatusFilterQuery = "";
    if (!in_array($docStatusFilter, [null, ""])) {
        $docStatusFilterQuery = "AND EXISTS (SELECT 1 FROM upload_docs_records udr WHERE udr.RegNoFK = oaf.mobile1 AND udr.DocsVerificationStatus = :docStatusFilter)";
        $params[':docStatusFilter'] = (int) $docStatusFilter;
    }

    $joinings = "FROM online_application_form oaf
    INNER JOIN paymenttransactionrecord ptr ON ptr.mobile = oaf.mobile1
    LEFT JOIN enroll_generation_table egt ON egt.mobile_no = oaf.mobile1
    LEFT JOIN qualification_master qm ON qm.QualID = oaf.Course
    WHERE ptr.AuthStatus = '0300' AND oaf.StudyCentreCode = :scCode 
    $searchQuery $docStatusFilterQuery ";

    // get total record count
    $totalRecordsStmt = $pdo->prepare("SELECT COUNT(*) $joinings");
    $totalRecordsStmt->bindValue(':scCode', $_SESSION['userid'], PDO::PARAM_STR);
    // bind search param if present
    if (isset($params[':search'])) {
        $totalRecordsStmt->bindValue(':search', $params[':search'], PDO::PARAM_STR);
    }
    if(isset($params[':docStatusFilter'])) {
        $totalRecordsStmt->bindValue(':docStatusFilter', (string)$params[':docStatusFilter'], PDO::PARAM_STR);
    }
    $totalRecordsStmt->execute();
    $totalRecords = $totalRecordsStmt->fetchColumn();

    // pagination 
    $totalPages = ($totalRecords > $rowPerPage) ? ceil($totalRecords / $rowPerPage) : 1;


    // get actual data
    // query to get the data for application along with enroll no and application no from enroll_generation_table table, and course name from qualification_master table
    $stmt = $pdo->prepare("SELECT 
        oaf.NameinEngish AS name, oaf.major, oaf.mobile1 AS mobile_no,
        egt.enroll, egt.application_no,
        qm.QualDesc AS course_name
        $joinings
        ORDER BY oaf.NameinEngish ASC
        LIMIT :start, :length");

    $stmt->bindValue(':start', (int) $startRow, PDO::PARAM_INT);
    $stmt->bindValue(':length', (int) $rowPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':scCode', $_SESSION['userid'], PDO::PARAM_STR);

    // bind search param if present
    if (isset($params[':search'])) {
        $stmt->bindValue(':search', $params[':search'], PDO::PARAM_STR);
    }
    if(isset($params[':docStatusFilter'])) {
        $stmt->bindValue(':docStatusFilter', $params[':docStatusFilter'], PDO::PARAM_INT);
    }

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);


    if (!empty($data)) {
        // 1. Get all mobile numbers from the results
        $mobileNumbers = array_column($data, 'mobile_no');

        // 2. Prepare a single query to fetch all related documents
        $placeholders = implode(',', array_fill(0, count($mobileNumbers), '?'));
        $docStmt = $pdo->prepare("
            SELECT 
                dm.DocName as docName, 
                udr.RegNoFK, 
                udr.DocFileName as filePath, udr.RecId as docIdPK, 
                udr.DocsVerificationStatus as docStatus, udr.DocsVerificationRemarks as remarks
            FROM upload_docs_records udr
            LEFT JOIN document_master dm ON dm.rid = udr.DocName
            WHERE udr.RegNoFK IN ($placeholders) udr.DocsVerificationStatus = :docStatusFilter
        ");
        $docStmt->execute($mobileNumbers);
        $allDocuments = $docStmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Group documents by their foreign key (mobile_no)
        $docsByMobile = [];
        foreach ($allDocuments as $doc) {
            $docsByMobile[$doc['RegNoFK']][] = $doc;
        }

        // 4. Attach documents to the main data array
        foreach ($data as &$row) {
            $row['documents'] = $docsByMobile[$row['mobile_no']] ?? [];
        }
        unset($row);
    }



    // Response
    echo json_encode([
        "rowPerPage" => $rowPerPage,
        "start" => $startRow,
        "totalPages" => $totalPages,
        'searchInp' => $searchValue,
        'data' => $data,
        'total_records' => $totalRecords,
    ]);


} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $docId = $_POST['docId'];
    $docStatus = $_POST['docStatus'];
    $remark = $_POST['remarks'];

    $remark = htmlspecialchars(trim($_POST['remarks'] ?? ''), ENT_QUOTES, 'UTF-8');

    $allowedStatus = [1, 2, 0, 99];
    if (empty($docId) || !in_array((int) $docStatus, $allowedStatus, true)) {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Invalid or missing required fields.']);
        exit();
    }

    $stmt = $pdo->prepare("UPDATE upload_docs_records SET DocsVerificationStatus = ?, DocsVerificationRemarks = ?, reserved1 = ? WHERE RecId = ?");
    $stmt->execute([$docStatus, $remark, $_SESSION['userid'], $docId]);

    // Response
    echo json_encode([
        'success' => true,
        'message' => 'Verification Details updated successfully.'
    ]);
}

?>