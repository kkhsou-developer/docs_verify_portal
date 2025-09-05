<header id="navbar" class="">
    <div class="navTogglerBox">
        <div class="close navToggler">&#10006;</div>
        <div class="open navToggler">&#9776;</div>

    </div>
    <div class="container">

        <div class="headBox">
            <img src="assets/kkhsou_logo.png" alt="">
            <h2>Welcome to,<br> KKHSOU Document Verification Portal</h2>
            <h4 class="centerInfo">
                Center Code: <span><?php echo $_SESSION['userid'];?></span>
                <br>
                Center Name : <span><?php echo $_SESSION['center_name'];?></span>
            </h4>
        </div>

        <ul class="navmenu">
            <li class="navLink active"><a href="./?act=1">Dashboard</a></li>
            <li class="navLink"><a href="./documents?status=0&act=2">Unverified Documents</a></li>
            <li class="navLink"><a href="./documents?status=1&act=3">Verified Documents</a></li>
            <li class="navLink"><a href="./documents?status=2&act=4">Re-Verified Documents</a></li>
            <li class="navLink"><a href="./documents?status=99&act=5">Suspicious Documents</a></li>
            <!-- <li class="navLink"><a href="./report?act=6">My Report</a></li> -->
            <li class="">
                <button class="btn logoutBtn">Logout</button>
            </li>
        </ul>
    </div>
</header>