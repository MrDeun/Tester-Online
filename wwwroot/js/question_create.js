"use strict";
function addGroupRow(table_id, input_id){
    var input = document.getElementById(input_id);
    console.log(input.value);
    var input_name = document.getElementById(input.value).textContent;
    console.log(input_name);
    if(input.value != ""){
        var table = document.getElementById(table_id);
        var row = table.insertRow();
        var id = row.insertCell();
        var name = row.insertCell();
        var deleteRow = row.insertCell();
        id.innerHTML = input.value;
        name.innerHTML = "<input type='hidden' name='groups[]' id='group_" + row.sectionRowIndex + "' value='" + input.value + "'>" + input_name;
        deleteRow.innerHTML = "<button type='button' class='w3-button w3-red' onclick='deleteTableRow(this)'>Usuń</button>";
    }

};
function openQuestionToggle(close_id, open_id, el_id){
    var checkbox = document.getElementById(el_id);
    var closeQuestion = document.getElementById(close_id);
    var openQuestion = document.getElementById(open_id);
    if(checkbox.checked == true){
        closeQuestion.classList.replace("w3-show", "w3-hide");
    }
    else{
        closeQuestion.classList.replace("w3-hide", "w3-show");
        
    }
};
function addAnswerRow(table_id, input_id){
    var input = document.getElementById(input_id);
    if(input.value != ""){
        var table = document.getElementById(table_id);
        var row = table.insertRow();
        if(row.sectionRowIndex <= 10){
            var id = row.insertCell();
            var text = row.insertCell();
            var correct = row.insertCell();
            var deleteRow = row.insertCell();
            id.innerHTML = "";
            text.innerHTML = "<td><input type='text' class='w3-input' name='answers_text[]' value='" + input.value + "' readonly></td>";
            correct.innerHTML = "<td><input type='checkbox' class='w3-check' name='answers_correct[]' value='" + row.sectionRowIndex + "'></td>";
            deleteRow.innerHTML = "<button type='button' class='w3-button w3-red' onclick='deleteTableRow(this)'>Usuń</button>";
        }
    }

};