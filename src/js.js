// Get the modal
var _countEditIcon = true;
var _editList = false;
let backGroundModal = document.getElementsByClassName("backGroundModal")[0];

function setLaguageBody() {
    let cookiesData = document.cookie.split(';');
    cookiesData.forEach(el => {
        let tempEl = el.split('=');
        if (tempEl[0] == ' setLang') {
            if (tempEl[1] == 1) {
                // TODO-M change name-id
                document.getElementsByClassName("dropdown-content")[0].style.cssText = 'direction: rtl; padding-left: 20px; padding-right: 5px;';
                document.getElementsByTagName('DL')[0].style.cssText = 'direction: rlt;'
            } else {
                document.getElementsByClassName("dropdown-content")[0].style.cssText = 'direction: ltr; padding-left: 5px; padding-right: 20px;';
                document.getElementsByTagName('DL')[0].style.cssText = 'direction: ltr;'
            }
        }
    })
}

function resetLan(id) {
    setCookie('setLang', id, 365)
    window.location.href = window.location.href.split('?')[0];
}

function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString() + ";";
    }
    document.cookie = name + "=" + (value || "") + expires + "path=/";
}

function menuLanguage() {
    let val = document.getElementById('menuBtnLan').textContent;
    let txt = "<div class='formModal'><span>" + val + "</span>";
    //TODO-M use form style to this "span" (formModal)
    lanData.forEach(element => {
        txt += '<button onclick="resetLan(' + element.id + ')" name="defLanID" value="' + element.id + '">' + element.name + '</button>';
    });
    txt += '</div>';
    openModal(txt);
}

function openMenu() {
    // TODO-M change name-id
    document.getElementById("myDropdown").style.display = 'block';
}

// When the user clicks the button, open the modal 
function openModal(txt) {
    backGroundModal.style.display = "block";
    document.getElementById('textModal').innerHTML = txt;
}

// When the user clicks on <span> (x), close the modal
function closeByX() {
    backGroundModal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
function closeModal(event) {
    if (event.target == backGroundModal) {
        backGroundModal.style.display = "none";
    }
}

function checkAvalibleDeleteAll(){
    let cookiesData = document.cookie.split(';');
    cookiesData.forEach(el => {
        let tempEl = el.split('=');
        if (tempEl[0] == ' dataReport') {
            if(tempEl[1]){
                document.getElementById('delete_all_img').style.display = 'inline-block';
            }
        } 
    });   
}

function editReport(){
    if(_countEditIcon){
        document.getElementById('edit_img').style.cssText = "border: 2px solid #12be12; background-color: #cbf8cb;width: 22px; height: 25px;";
        document.querySelectorAll('.updateVal').forEach(el => el.style.cssText = 'background-color: #cbf8cb');
        document.querySelectorAll('.deleteReport').forEach(el => el.style.cssText = 'display: inline-block');
        checkAvalibleDeleteAll();
        _editList = true;
        _countEditIcon = false;
    } else {
        document.getElementById('edit_img').style.cssText = "border: none; background-color: transparent; width: 22px; height: 25px;";
        document.getElementById('delete_all_img').style.display = 'none';
        document.querySelectorAll('.updateVal').forEach(el => el.style.cssText = 'background-color: transparent');
        document.querySelectorAll('.deleteReport').forEach(el => el.style.display = 'none');

        _editList = false;
        _countEditIcon = true;
    }
}

function deleteReport(id){
    let url = window.location.href.split('?')[0];
    window.location.href = url + '?deleteData=r_'+id;
}
// Close the dropdown if the user clicks outside of it
window.onclick = function (event) {
    if (!event.target.matches('.dropbtn')) {
        // TODO-M change name-id
        document.getElementById("myDropdown").style.display = 'none';
    }
}
let eventHandler = function (e) { e.preventDefault(); editDiv(this); };
document.querySelectorAll('.updateVal').forEach(el => el.addEventListener("click", eventHandler));

function editDiv(div) {
    if(_editList){
        let text = div.innerText,
        textarea = document.createElement("TEXTAREA");
        textarea.value = text;
            
        div.innerHTML = "";
        div.append(textarea);
        textarea.focus();
        textarea.addEventListener("focusout", function (e) {
            finishEditDiv(div,text);
        });
        
        div.removeEventListener("click", eventHandler);
        
    }
}

function updateData(div) {
    let updateObject = {'id':'','category':'','amount':'','type':''};
    updateObject.id = div.id.split('_')[2];

    // get id & type
    // if(div.id.split('_')[0] == "c"){
    //     // data from category \\
    //     updateObject.type = 'dataCategory'; 
    //     if(div.id.split('_')[1] == "name"){
    //         // get category \\
    //         updateObject.category = div.outerText
    //         updateObject.amount = div.parentNode.outerText.split(' ')[2].replace(/\n/,'');
    //     }else{
    //         // get amount \\
    //         updateObject.category = div.parentNode.outerText.split(':')[0];
    //         updateObject.amount = div.outerText;
    //     }
    // } else {
        // data from report \\
        updateObject.type = 'dataReport'; 
        updateObject.amount = div.outerText;
    // }

    let url = window.location.href.split('?')[0];
    window.location.href = url + '?updateData='+JSON.stringify(updateObject);
}

setLaguageBody();