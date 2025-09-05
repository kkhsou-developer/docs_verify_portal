<?php
if (session_status() == PHP_SESSION_NONE):
    session_start();
endif;
if (!isset($_SESSION['userid'])) {
    header("Location: login");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unverified Documents</title>

    <!-- <base href="/proj/docs_verify_portal/"> -->

    <link rel="preload" href="assets/css/base.css" as="style" onload="this.onload=null; this.rel='stylesheet'">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">


    <style>
    </style>
</head>

<body class="d-flex vh-100">

    <?php include "pages/component/_header.php"; ?>

    <main class="d-flex flex-col">
        <h2 class="box heading">Unverified Documents</h2>

        <div class="box">
            <div class="tableContainer">
                <div class="actionBox">
                    <div>
                        <h5>Entry Per Page:</h5>
                        <select name="rowPerPage" id="rowPerPage">
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>

                    <div>
                        <h5>Search:</h5>
                        <input type="text" name="searchBox" class="searchBox" maxlength="100"
                            placeholder="Search By Enroll, Mobile No, Name">
                    </div>
                </div>
                <table id="customTable" class="table_1 th-sticky" width="100%">
                    <div class="loader d-none"></div>

                    <thead class="bg-secondary">
                        <tr class=" text-white">
                            <th>Sl. No.</th>
                            <th>Enroll. No.</th>
                            <th>Name</th>
                            <th>Course Name</th>
                            <th>Mobile No.</th>
                            <th>Admission Form</th>
                            <th>Doc Description</th>
                            <th>File</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <!-- Pagination controls -->
                <div id="paginationContainer ">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-center">
                        </ul>
                    </nav>
                </div>


            </div>
        </div>



        <!-- Models -->

        <!-- file preview and verify modal -->
        <div class="modal fade" id="filePreviewModal" aria-hidden="true" aria-labelledby="filePreviewModalToggleLabel"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="filePreviewModalToggleLabel">Document Preview</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Show a second modal and hide this one with the button below.
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" data-bs-target="#verifyModal"
                            data-bs-toggle="modal">Verify</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="verifyModal" aria-hidden="true" aria-labelledby="verifyModalToggleLabel"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="verifyModalToggleLabel">Update Verification Details</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <form action="handlers/verifyHandler.php" method="POST" class="form_1" id="verifyDocForm">
                            <input type="hidden" name="docId" id="docId" required>
                            <div class="formGroup">
                                <label for="docStatus">Verification Status</label>
                                <select name="docStatus" id="docStatus" required>
                                    <option disabled selected>--Select Status--</option>
                                    <option value="1">Verified</option>
                                    <option value="2">Re Verified</option>
                                    <option value="99">Suspicious</option>
                                    <option value="0">Pending</option>
                                </select>
                            </div>
                            <div class="formGroup">
                                <label for="remarks">Remarks</label>
                                <textarea name="remarks" id="remarks" cols="30" rows="5"
                                    placeholder="Enter Remarks"></textarea>
                            </div>
                        </form>

                                    <?php include_once "pages/instructions.php"; ?>

                    </div>
                    <div class="modal-footer">
                        <!-- <button class="btn btn-primary" data-bs-target="#admsnFrmModalToggle" data-bs-toggle="modal">Back to first</button> -->
                        <button class="btn btn-primary" id="submitVerifyForm">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admission form view modal -->


        <!-- Modal -->
        <div class="modal fade" id="admsnFrmModal" tabindex="-1" aria-labelledby="admsnFrmModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="admsnFrmModalLabel">Admission Form Preview</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="height:100vh">
                        <iframe id="admissionFormIframe" src=""
                            style="width: 100%; height: 100%; border: none;"></iframe>
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-primary">Print</button> -->
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous">
        </script>

    <script src="assets/js/base.js"></script>


    <script>
        $(document).ready(function () {
            const docStatus = new URLSearchParams(window.location.search).get('status') ?? null

            const titles = {
                0: "Pending/Unverified Documents",
                1: "Verified Documents",
                2: "Re-Verified Documents",
                99: "Suspicious Documents"
            };

            document.title = titles[docStatus] ?? "Error - Invalid Status Value";
            $(".heading").text(titles[docStatus] ?? "Error - Invalid Status Value")

            $(".loader").html(`<div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>`)

            function enableLoader() {
                $(".loader").removeClass('d-none')
            }

            function disableLoader() {
                $(".loader").addClass('d-none')
            }


            let currentPage = 1;
            let searchTerm = "";
            let noOfRows = $("#rowPerPage").val();


            // Function to fetch data from the server and render it in the table           
            function fetchData() {
                let start = (currentPage - 1) * noOfRows;
                let tbody = $(".table_1 tbody");
                let pagination = $(".pagination");
                
                enableLoader()
                tbody.empty();
                pagination.empty();

                $.ajax({
                    url: "handlers/docVerification_handler.php",
                    method: "GET",
                    data: {
                        page: currentPage,
                        search: searchTerm,
                        rows: noOfRows,
                        start: start,
                        docStatusFilter: docStatus
                    },
                    dataType: "json",
                    success: function (res) {
                        if(res.data.length === 0){
                            // if no data found
                            disableLoader()
                            tbody.empty().append(`<tr><td colspan="12" class="error">No Data Found !</td></tr>`)
                        }
                        else{
                            setTimeout(() => {
                                renderTable(res.data, res.start);
                                renderPagination(res.totalPages);
                                disableLoader()
                            }, 800);
                        }
                        
                    },
                    error: function (e) {   
                        disableLoader()
                        let row = $(`<tr><td colspan="12" class="error">Internal Server Error !</td></tr>`);
                        tbody.empty().append(row);
                    }
                });
            }

            function renderTable(data, start) {
                let tbody = $(".table_1 tbody");
                
                // const start = data.start
                data.forEach((item, idx) => {
                    let slno = parseInt(start) + idx + 1

                    item.documents.forEach((doc, index) => {
                        let row = $("<tr></tr>");

                        // Merge student cells with rowspan
                        if (index === 0) {
                            row.append(
                                `<td rowspan="${item.documents.length}">${slno}</td>`);
                            row.append(
                                `<td rowspan="${item.documents.length}">${item.enroll}</td>`);
                            row.append(`<td rowspan="${item.documents.length}">${item.name}</td>`);
                            row.append(
                                `<td rowspan="${item.documents.length}">
                                ${item.course_name} 
                                ${item.major ? `(${item.major})` : ""}
                            </td>`
                            );
                            row.append(
                                `<td rowspan="${item.documents.length}">${item.mobile_no}</td>`);
                            row.append(
                                `<td rowspan="${item.documents.length}"><a class="" data-bs-toggle="modal" data-bs-target="#admsnFrmModal" data-mobile="${item.mobile_no}">Admission Form</a></td>`
                            );
                        }

                        let staus = icon = "";
                        switch (doc.docStatus) {
                            case '1':
                                 status = "Verified"
                                 icon = "✅"
                                break;
                            case '2':
                                 status = "Re Verified"
                                 icon = "🔁"
                                break;
                            case '0':
                                 status = "Pending"
                                 icon = "⏳"
                                break;
                            case '99':
                                 status = "Suspicious"
                                 icon = "⚠️"
                                break;                        
                            default:
                                status = "Unknown"
                                break;
                        }

                        row.append(`<td>${doc.docName}</td>`);
                        row.append(
                            `<td><a href="../UploadedFiles/${doc.filePath}" data-doc-id="${doc.docIdPK}" data-remark="${doc.remarks}" data-status="${doc.docStatus}" data-bs-target="#filePreviewModal" data-bs-toggle="modal">Click Here</a></td>`
                        );


                        row.append(`<td>${icon} ${status}</td>`);
                        row.append(`<td>${doc.remarks}</td>`);
                        tbody.append(row);

                    });
                });
            }

            function renderPagination(totalPages) {
                // handle pagination
                let pagination = $(".pagination");
                pagination.empty();

                let maxPagesToShow = 5; // Maximum number of pages to display at once
                let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
                let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

                if (endPage - startPage + 1 < maxPagesToShow) {
                    startPage = Math.max(1, endPage - maxPagesToShow + 1);
                }

                // Previous button
                pagination.append(
                    `<li class="page-item ${currentPage === 1 ? "disabled" : ""}">
                        <a class="page-link" href="#" aria-label="Previous" data-page="${currentPage - 1}">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>`
                );

                // Page numbers
                for (let i = startPage; i <= endPage; i++) {
                    pagination.append(
                        `<li class="page-item ${i === currentPage ? "active" : ""} ${totalPages == 1 ? "disabled" : ""}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>`
                    );
                }

                // Next button
                pagination.append(
                    `<li class="page-item ${currentPage === totalPages ? "disabled" : ""}">
                        <a class="page-link" href="#" aria-label="Next" data-page="${currentPage + 1}">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>`
                );

                // Add click event listener to pagination links
                pagination.find(".page-link").on("click", function (e) {
                    e.preventDefault();
                    let newPage = parseInt($(this).data("page"));
                    if (newPage > 0 && newPage <= totalPages) {
                        currentPage = newPage;
                        fetchData();
                    }
                });
            }

            // Search handler
            let typingTimer;
            let doneTypingInterval = 500;
            $(".searchBox").on("keyup", function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(function () {
                    searchTerm = $(".searchBox").val();
                    currentPage = 1;
                    fetchData();
                }, doneTypingInterval);
            });

            // Row per page handler
            $("#rowPerPage").on("change", function () {
                noOfRows = $(this).val();
                currentPage = 1;
                fetchData();
            })


            

            // When the file preview modal is about to be shown
            $('#filePreviewModal').on('show.bs.modal', function (event) {
                const triggerLink = $(event.relatedTarget); // Link that triggered the modal
                const docId = triggerLink.data('doc-id');
                const remarks = triggerLink.data('remark');
                const status = triggerLink.data('status');
                const filePath = triggerLink.attr('href');

                const modal = $(this);

                // Update the modal's body to show the document preview in an iframe 
                modal.find('.modal-body').html(
                    //`<iframe src="${filePath}" style="width:50px; height: 400px; border: none;" title="Document Preview"></iframe>`
                    `<img src="${filePath}" style="object-fit:fill;width:90vh; height:60vh; border: none;" title="Document Preview">`
                );

                // set data to the 'Verify' button within this modal
                const verifyButton = modal.find('[data-bs-target="#verifyModal"]');
                verifyButton.data('doc-id', docId).data('remark', remarks).data('status', status);
            });

            // When the verification modal is about to be shown
            $('#verifyModal').on('show.bs.modal', function (event) {
                const triggerButton = $(event.relatedTarget); // Button that triggered the modal
                const docId = triggerButton.data('doc-id');
                const remark = triggerButton.data('remark');
                const status = triggerButton.data('status');

                $('#verifyDocForm')[0].reset(); // Reset form to clear previous data

                const modal = $(this);

                modal.find('#docId').val(docId);
                modal.find('#remarks').val(remark);
                modal.find('#docStatus').val(status);

            });


            // Handle the verification form submission
            $('#submitVerifyForm').on('click', function (e) {
                e.preventDefault();
                const formData = $('#verifyDocForm').serialize();
                
                $.ajax({
                    url: "handlers/docVerification_handler.php",
                    method: "POST",
                    data: formData,
                    dataType: "json",
                    success: function (res) {
                        $('#verifyModal').modal('hide');
                        fetchData();
                    },
                    error: function (e) {
                        console.log(e)
                    }

                });
            });



            function loadAdmissionForm() {
                const modal = $('#admsnFrmModal');
                const iframe = modal.find('#admissionFormIframe')[0];

                modal.on('show.bs.modal', function (event) {
                    const triggerButton = $(event.relatedTarget);
                    const mobile = triggerButton.data('mobile');
                    const url = `https://lmskkhsou.in/admission/SessionJuly2025/ui/receipt_newP.php?m=${mobile}`;

                    iframe.src = url;

                });
                modal.on('hidden.bs.modal', function (event) {
                    iframe.src = '';
                });
            }




            // Initial load
            fetchData();
            loadAdmissionForm();

        })
    </script>

</body>

</html>