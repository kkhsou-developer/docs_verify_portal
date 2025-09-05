<?php
if (session_status() == PHP_SESSION_NONE):
    session_start();
endif;
if (!isset($_SESSION['userid'])) {
    header("Location: login");
    exit();
}



require_once("dbConnect.php");

function getRecordCount($docStatusFilter = null)
{
    if (!in_array($docStatusFilter, [0, 1, 2, 99, 1000])) {
        return 0;
    }

    $docStatusFilterQuery = "";
    if (in_array((int) $docStatusFilter, [1])) {
        // STRICT: For Verified - ALL documents must match the status.
        $docStatusFilterQuery = "
                AND EXISTS (SELECT 1 FROM upload_docs_records udr_any WHERE udr_any.RegNoFK = oaf.mobile1)
                AND NOT EXISTS (
                    SELECT 1 FROM upload_docs_records udr_mismatch
                    WHERE udr_mismatch.RegNoFK = oaf.mobile1 AND udr_mismatch.DocsVerificationStatus != :docStatusFilter
                )";
    }
    else if($docStatusFilter == 1000) {
        // Get total admitted student count
        $docStatusFilterQuery = "AND EXISTS (SELECT 1 FROM upload_docs_records udr WHERE udr.RegNoFK = oaf.mobile1)";
    }
    else {
        // LENIENT: For Pending, Rejected, Re-verified - AT LEAST ONE document must match the status.
        $docStatusFilterQuery = "AND EXISTS (SELECT 1 FROM upload_docs_records udr WHERE udr.RegNoFK = oaf.mobile1 AND udr.DocsVerificationStatus = :docStatusFilter)";
    }

    $joinings = "FROM online_application_form oaf
    INNER JOIN paymenttransactionrecord ptr ON ptr.mobile = oaf.mobile1
    LEFT JOIN enroll_generation_table egt ON egt.mobile_no = oaf.mobile1
    LEFT JOIN qualification_master qm ON qm.QualID = oaf.Course
    WHERE ptr.AuthStatus = '0300' AND oaf.StudyCentreCode = :scCode 
    $docStatusFilterQuery ";

    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) $joinings");
    $stmt->bindValue(':scCode', $_SESSION['userid'], PDO::PARAM_STR);
    if (!in_array($docStatusFilter, [null, "1000",1000])) {
        $stmt->bindValue(':docStatusFilter', (string) $docStatusFilter, PDO::PARAM_STR);
    }
    $stmt->execute();
    $recordCount = $stmt->fetchColumn();
    return $recordCount;
}

$verifiedApplication = getRecordCount('1');
$pandingApplication = getRecordCount('0');
$reVerifiedApplication = getRecordCount('2');
$rejectedApplication = getRecordCount('99');
$totalApplication = getRecordCount('1000');



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deshboard</title>

    <!-- <base href="/proj/docs_verify_portal/"> -->

    <link rel="preload" href="assets/css/base.css" as="style" onload="this.onload=null; this.rel='stylesheet'">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">


    <style>
        .detailsCont {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 2rem;

            >.box {
                min-width: 20rem;
                width: 30%;
                padding: 1.5rem;
                text-decoration: none;

                h5 {
                    color: var(--accent-color);
                }

                h4 {
                    color: var(--text-secondary);
                }
            }
        }
    </style>
</head>

<body class="d-flex vh-100">
    <?php include "pages/component/_header.php"; ?>

    <main class="d-flex flex-col">
        <h2 class="box heading">Dashboard</h2>

        <div class="detailsCont">
            <div class="box">
                <h5>Total Applications</h5>
                <h4><?php echo $totalApplication; ?></h4>
            </div>
            <a href="./documents?status=0&act=2" class="box">
                <h5>Unverified Applications</h5>
                <h4><?php echo $pandingApplication; ?></h4>
            </a>
            <a href="./documents?status=1&act=3" class="box">
                <h5>Verified Applications</h5>
                <h4><?php echo $verifiedApplication; ?></h4>
            </a>
            <a href="./documents?status=2&act=4" class="box">
                <h5>Re-Verified Applications</h5>
                <h4><?php echo $reVerifiedApplication; ?></h4>
            </a>
            <a href="./documents?status=99&act=5" class="box">
                <h5>Suspicious Applications</h5>
                <h4><?php echo $rejectedApplication; ?></h4>
            </a>
        </div>
        
        <?php include_once "pages/instructions.php"; ?>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous">
        </script>

    <script src="assets/js/base.js"></script>

</body>

</html>