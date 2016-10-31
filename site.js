/* https://medium.com/@_jh3y/b30d49f9a0ff */
speeddial = function () {
    var VISIBLE_CLASS = "is-showing-options",
        fab_btn = document.getElementById("fab_btn"),
        fab_ctn = document.getElementById("fab_ctn"),
        showOpts = function (e) {
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
};

hideColumns = function () {
    var tables = document.getElementsByClassName("tny-table");
    for (var t = 0, table; table = tables[t]; t++) {
        for (var i = 0, row; row = table.rows[i]; i++) {
            for (var j = 0, col; col = row.cells[j]; j++) {
                if (j == 0) {
                    col.style.width = "140px";
                }
                if (j == 1) {
                    col.style.width = "180px";
                }
                if (j > 1) {
                    col.className += " mdl-cell--hide-phone";
                }
                if (j > 2) {
                    col.className += " mdl-cell--hide-tablet";
                }
            }
        }
    }
};

speeddial();
hideColumns();

var options = {
        valueNames: ['commands', 'permissions']
    };
var utilsTable = new List('utils-table', options).sort('commands', { order: "asc" });
var rolesTable = new List('roles-table', options).sort('commands', { order: "asc" });
var notifsTable = new List('notifs-table', options).sort('commands', { order: "asc" });