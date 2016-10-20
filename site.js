
/* https://medium.com/@_jh3y/b30d49f9a0ff */
(function () {
    var VISIBLE_CLASS = "is-showing-options",
        fab_btn  = document.getElementById("fab_btn"),
        fab_ctn  = document.getElementById("fab_ctn"),
        showOpts = function(e) {
            var processClick = function (evt) {
                if (e !== evt) {
                    fab_ctn.classList.remove(VISIBLE_CLASS);
                    fab_ctn.IS_SHOWING = false;
                    document.removeEventListener("click", processClick);
                }
            };
            if (!fab_ctn.IS_SHOWING) {
                fab_ctn.IS_SHOWING = true;
                fab_ctn.classList.add(VISIBLE_CLASS);
                document.addEventListener("click", processClick);
            }
        };
    fab_btn.addEventListener("mouseover", showOpts);
    fab_btn.addEventListener("click", showOpts);
}.call(this));

$('#return-to-top').click(function() {
    $('body,html').animate({
        scrollTop : 0
    }, 500);
});