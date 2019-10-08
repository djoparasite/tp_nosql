const input = document.querySelector('input');

input.addEventListener('input', updateValue);

function updateValue(e) {
    const input = document.querySelector('input');
    if (input.value.length >= 3) {
        submitForm(e)
    } else  {
        document.getElementById("values").innerHTML = '';
    }
}

function submitForm(e)
{
    var xhr = new XMLHttpRequest();
    var values = document.getElementById('values');

    xhr.open('GET', '../../src//scripts/handle.php?search=' + e.target.value);
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById("values").innerHTML = '';
            var data = JSON.parse(xhr.response);
            for (var i = 0; i < data.cursor.length; i++) {
                var obj = data.cursor[i];
                for (var key in obj){
                    var value = obj[key];
                    if ('_id' === key) {
                        var id = value;
                        var elemA = document.createElement("a");
                        elemA.id = id;
                        elemA.href = "src/scripts/show.php?id=" + id;
                        document.getElementById("values").appendChild(elemA);
                    }
                    if ('url' === key) {
                        var elem = document.createElement("img");
                        elem.src = value;
                        elem.alt = "img";
                        elem.classList.add("img-loaded");
                        document.getElementById(id).appendChild(elem);
                    }
                }
            }

            document.getElementById("clear-db").innerHTML ="Count documents: " + data.countDocuments;
        }
        else {
            alert('Request failed.  Returned status of ' + xhr.status);
        }
    };
    xhr.send();
}

function deleteAll() {
    var xhr = new XMLHttpRequest();
    var values = document.getElementById('values');

    xhr.open('GET', '../../src/scripts/clearDb.php');
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById("clear-db").innerHTML = '';
            var data = JSON.parse(xhr.response);
            document.getElementById("clear-db").innerHTML ="Count documents: " + data;
        }
        else {
            alert('Request failed.  Returned status of ' + xhr.status);
        }
    };
    xhr.send();
}