function barcodeScan() {
    document.getElementById("barcode-scan-wrapper").style.display = "block";
    document.getElementById("screen-mask").style.display = "block";
    checkForBarcode();
}

function checkForBarcode() {
    var e, t, n, r;
    e = document.getElementById("scan_results");
    e.innerHTML = "<div style='margin:11.5% 5%;width:77%;background:white;box-shadow:0 1px 3px rgba(0,0,0,.4);border-radius:3px;padding:10px 12px 3px 21px;'><img src='img/barcode.jpg'><p style='color: #0E71DD; text-align: center;'>Scan your barcode to begin...</p></div>";
    n = window.setTimeout(function() {
        e.innerHTML = "<div class='errorbox' style='width:80.9%; margin:5%;'>If your having trouble using this barcode scanner, You can type the barcode into the <a href='javascript:stopScan();'>search box.</a></div>";
    }, 3e4);
    r = window.setInterval(function() {
        t = $.scriptcam.getBarCode();
        if (t != "") {
            window.clearTimeout(n);
            e.innerHTML = "<img src='img/ajax-load.gif' style='margin: 10px 108px;'>";
            window.clearInterval(r);
            checkBarcode(t);
        }
    }, 100);
}

function checkBarcode(e) {
    var t;
    t = document.getElementById("scan_results");
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest;
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            if (xmlhttp.responseText == "0") {
                t.innerHTML = "<div class='errorbox' style='width:80.9%; margin:5%;'>&#10007; No Product matches that barcode in our Database.<br>" + e + "</div>";
                window.setTimeout(function() {
                    t.innerHTML = "";
                    checkForBarcode();
                }, 5e3);
            } else {
                t.innerHTML = xmlhttp.responseText;
            }
        }
    };
    xmlhttp.open("GET", "ajax.php?bc=" + e, true);
    xmlhttp.send();
}

function stopScan() {
    document.getElementById("barcode-scan-wrapper").style.display = "none";
    document.getElementById("screen-mask").style.display = "none";
    $.scriptcam.closeCamera;
}

function goToProduct(e) {
    window.location = "product.php?id=" + e;
}

function showResult(e) {
    if (e.length == 0) {
        document.getElementById("livesearch").innerHTML = "";
        document.getElementById("livesearch").style.border = "0px";
        return;
    }
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest;
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            document.getElementById("livesearch").innerHTML = xmlhttp.responseText;
            document.getElementById("livesearch").style.border = "1px solid rgba(0,0,0,.6)";
        }
    };
    if (window.sort.value === "") {
        xmlhttp.open("GET", "ajax.php?q=" + e + "&type=0", true);
        alert(window.sort.value);
    } else {
        xmlhttp.open("GET", "ajax.php?q=" + e + "&type=" + window.sort.value, true);
        alert(window.sort.value);
    }
    xmlhttp.send();
}

function addSearchFilter(e, t) {
    var n = document.getElementById("search-filter");
    document.getElementById("search-text").style.width = "399px";
    document.getElementById("year-select").style.display = "none";
    if (filterOn === false) {
        setTimeout(function() {
            n.innerHTML = e + "<div id='rm-search-filter'  onclick='rmSearchFilter();'></div>";
            n.style.display = "block";
            n.style.opacity = "1";
        }, 450);
        window.filterOn = true;
    } else {
        n.innerHTML = e + "<div id='rm-search-filter'  onclick='rmSearchFilter();'></div>";
        n.style.display = "block";
        n.style.opacity = "1";
    }
    window.sort.value = t;
    showResult(document.getElementById("search-text").value);
}

function changeRating(e) {
    var t, n, r, i, s, o = document.getElementById(e), u;
    if (e.search("-5-") > 0) {
        i = 5;
    } else {
        if (e.search("-4-") > 0) {
            i = 4;
        } else {
            if (e.search("-3-") > 0) {
                i = 3;
            } else {
                if (e.search("-2-") > 0) {
                    i = 2;
                } else {
                    if (e.search("-1-") > 0) {
                        i = 1;
                    }
                }
            }
        }
    }
    u = "-" + i + "-";
    for (r = i; r > 0; r--) {
        s = e;
        u = "-" + i + "-";
        o = "-" + r + "-";
        s = s.replace(u, o);
        document.getElementById(s).innerHTML = "★";
    }
    for (r = i + 1; r < 6; r++) {
        s = e;
        u = "-" + i + "-";
        o = "-" + r + "-";
        s = s.replace(u, o);
        document.getElementById(s).innerHTML = "☆";
    }
    t = e.substring(4);
    t = t.split("_");
    n = "rate-" + t[0] + "-" + t[1];
    document.getElementById(n).value = i;
}

function rmSearchFilter() {
    document.getElementById("search-filter").style.display = "none";
    document.getElementById("search-text").style.width = "548px";
    window.filterOn = false;
    window.sort.value = 0;
}

function showDropWindow(e) {
    var t, n = document.getElementById(e);
    if (e == "sort-select") {
        t = document.getElementById("product-desc").offsetHeight;
        n.style.top = 390 + t + "px";
    }
    n.style.display = "block";
    n.focus();
    n.onblur = function() {
        this.style.display = "none";
    };
}

function validateNewPass() {
    var e = document.getElementById("np-pass").value, t = document.getElementById("rp-pass").value, n = "", r = document.getElementById("js-error");
    if (e != t) {
        n = "<li>&#10007; Passwords do not match</li>";
    }
    if (e != "") {
        if (e.length < 6) {
            n = n.concat(n, "<li>&#10007; Your new password needs to be greater than 6 characters.</li>");
        }
    } else {
        n = n.concat(n, "<li>&#10007; You cannot have a blank password.</li>");
    }
    if (n == "") {
        document.forms["change-pass-form"].submit();
    } else {
        r.innerHTML = n;
        r.style.display = "block";
        window.scrollTo(0, -110);
        setTimeout(function() {
            r.style.display = "none";
        }, 6e3);
    }
}

function rmFromList(e, t, n, r, i) {
    var s = "";
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest;
    } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            n.style.display = "none";
        }
    };
    if (typeof i != "undefined") {
        s = "&s=" + i;
    }
    xmlhttp.open("GET", "ajax.php?p=" + e + "&u=" + t + "&l=" + r + s, true);
    xmlhttp.send();
}

function submitForm(e) {
    document.forms[e].submit();
}

window.onload = function() {
    var e, t, n;
    e = document.getElementsByClassName("drop-list-item");
    for (t = 0, l = e.length; t < l; t++) {
        if (e[t].id < 4) {
            e[t].onclick = function() {
                addSearchFilter(this.innerHTML, this.id);
            };
        } else {
            if (e[t].id < 6) {
                n = document.URL;
                n = n.replace("&sort=1", "");
                n = n.replace("&sort=2", "");
                if (e[t].id == 4) {
                    e[t].onclick = function() {
                        window.location = n + "&sort=1";
                    };
                } else {
                    if (e[t].id == 5) {
                        e[t].onclick = function() {
                            window.location = n + "&sort=2";
                        };
                    }
                }
            }
        }
    }
    window.sort = document.getElementById("sort-para");
};

var filterOn = false, sort;
