$(document).ready(() => {

    $(".logoutBtn").click((e) => {
        e.preventDefault()
        if (confirm("Are you sure you want to logout?")) {
            window.location.href = "logout";
        }

    })

    const activeNavLink = new URLSearchParams(window.location.search).get('act');

    if (activeNavLink) {
        $("#navbar .navLink").removeClass("active");
        $(`#navbar .navLink:nth-child(${activeNavLink})`).addClass("active");
    }


    $(".navToggler").click(() => {
        $("#navbar").toggleClass("hide")
    })
})