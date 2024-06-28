"use strict";

function addGroupRow(table_id, input_id){
    var input = document.getElementById(input_id);
    console.log(input.value);
    var option = document.getElementById(input.value);
    if(input.value != ""){
        var table = document.getElementById(table_id);
        var row = table.insertRow();
        var id = row.insertCell();
        var name = row.insertCell();
        var questions = row.insertCell();
        var deleteRow = row.insertCell();
        id.innerHTML = input.value;
        name.innerHTML = "<input type='hidden' name='groups[]' value='" + input.value + "'>" + option.textContent;
        questions.innerHTML = "<input type='number' class='w3-input' name='questions_count[]' min='0' max='"+ option.dataset.max +"'>";
        deleteRow.innerHTML = "<button type='button' class='w3-button w3-red' onclick='deleteTableRow(this)'>Usuń</button>";
    }

};