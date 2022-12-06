<!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Report</title>
        <link rel="stylesheet" href="./src/maincss.css"></link>
        <link rel="stylesheet" href="./src/calendar.css"></link>
    </head>
    <body>
        <?php
            include "src/fun.php";
            include 'src/calendar.php';
            $calendar = new Calendar('01-12-2022');
        ?>
        <div id="mainBox">
            <!-- TODO-M rename div.. -->
            <div class="dropdown">
                <button onclick="openMenu()" class="dropbtn"><?php echo $lanData['menu_app'] ?></button>
                <div id="myDropdown" class="dropdown-content">
                    <p id="menuNewItem" onclick="setNewItem()"><?php echo $lanData['new_item'] ?></p>
                    <p id="menuUpadte" onclick="updateList()"><?php echo $lanData['update_item'] ?></p>
                    <p id="menuDelete" onclick="deleteList()"><?php echo $lanData['delete_item'] ?></p>
                    
                    <p id="menuBtnLan" onClick="menuLanguage()"><?php echo $lanData['language'] ?></p>
                </div>
            </div>
            <div id="displayCurrentAmount">
                <?php 
                    echo getFinalamount($lanData);
                ?>
            </div>
            <hr>
            <div id="form">
                <form class="formAmount" action="index.php"  method="GET" > <!-- onsubmit="setTimeout(function(){window.location.reload();},10);" -->
                    <br>
                    <select name="categoryID" required>
                        <option value="" selected disabled><?php echo $lanData['select_options'] ?></option>
                        <?php
                            $categories = getCategories();
                            foreach ($categories as $key => $value) {
                                echo '<option value="'.$value->id.'">'.$value->category.'</option>';
                            }
                        ?>
                    </select>
                    <br>
                    <input type="number" name="amount" placeholder="<?php echo $lanData['amount'] ?>"required>
                    <button type="submit" name="newDataReport" value="data" style="width:100px"><?php echo $lanData['send'] ?></button>
                </form>
            </div>
            <div id="edit_icon">
                <img id="edit_img" onclick="editReport()" src="src/img/edit_icon.png" alt="edit_icon" style="width: 22px; height: 25px;">
                <img id="delete_all_img" onclick="deleteAllReports()" src="src/img/delete_all_icon.png" alt="delete_all_icon" style="width: 22px; height: 25px;">
            </div>
            
            <div id="displayListData">
                <dl>
                <?php
                // TODO-M add filter = https://stackoverflow.com/questions/17714705/how-to-use-checkbox-inside-select-option
                    showList($lanData);
                    // showListByCalnd($lanData,$calendar)
                ?>
                </dl>
            </div>
            <div class="content home">
            </div>
            <!-- TODO-M rename div.. -->
            <!-- The Modal -->
            <div id="myModal" onclick="closeModal(event)" class="modal backGroundModal">

                <!-- Modal content -->
                <div class="modal-content">
                    <span class="close" onclick="closeByX()">&times;</span>
                    <p id="textModal"></p>
                </div>

            </div>
        </div>
        <script src="src/js.js"></script>
    </body>
    <script>
        function deleteAllReports() {
            let res = confirm('<?php echo $lanData['msg_reset_all'] ?>');
            if(res){
                let url = window.location.href.split('?')[0];
                window.location.href = url + '?deleteData=allReports';
            }
        }
        function setNewItem() {
            let val = document.getElementById('menuNewItem').textContent;
            let txt = "<span>"+val+"</span>";
            txt += '<form class="formModal" action="index.php" method="GET">';
            txt += "<input type='text' name='category' placeholder='<?php echo $lanData['category'] ?>' required>";
            txt += "<input type='number' name='inicialAmount' placeholder='<?php echo $lanData['inicial_amount'] ?>' required>";
            txt += "<button type='submit' name='newItem' value='save'><?php echo $lanData['save_config'] ?></button>";
            txt += '</form>';
            openModal(txt);
        }

        function updateList() {
            let data = [];
            let cookiesData = document.cookie.split(';');
            cookiesData.forEach(el => {
                let tempEl = el.split('=');
                if (tempEl[0] == ' dataCategory') {
                    data.push(JSON.parse(decodeURIComponent(tempEl[1])));
                }
            });
            let txt = '<form action="index.php" method="GET"><span class="updateForm">';
            data[0].forEach(el => {
                txt += "<input type='text' name='c_"+el.id+"' value='"+el.category+"' required>";
                txt += "<input type='number' name='ia_"+el.id+"' value='"+el.inicialAmount+"' required>";
            })
            txt += "<input type='hidden' name='type' value='dataCategory'>";
            txt += "</span><button class='updateFormBtn' type='submit' name='updateItem' value='update'><?php echo $lanData['update_item'] ?></button>";
            txt += '</form>';
            openModal(txt);
        }

        function deleteList() {
            let data = [];
            let cookiesData = document.cookie.split(';');
            cookiesData.forEach(el => {
                let tempEl = el.split('=');
                if (tempEl[0] == ' dataCategory') {
                    data.push(JSON.parse(decodeURIComponent(tempEl[1])));
                }
            });
            let txt = '<form action="index.php" method="GET"><span class="updateForm">';
            data[0].forEach(el => {
                txt += "<chkbx><input type='checkbox' name='c_"+el.id+"'></chkbx>";
                txt += "<input type='text' value='"+el.category+"' readonly>";
                txt += "<input type='number' value='"+el.inicialAmount+"' readonly>";
            })
            txt += "<input type='hidden' name='type' value='dataCategory'>";
            txt += "</span><button class='updateFormBtn' type='submit' name='deleteItem' value='delete'><?php echo $lanData['delete_item'] ?></button>";
            txt += '</form>';
            openModal(txt);
        }

        function finishEditDiv(div,origText) {
            //handle your data saving here
            let text = div.querySelector('textarea').value;
            if(div.id.split('_')[1] == 'amount' && !Number(text)){
                alert('<?php echo $lanData['msg_invalid_value'] ?>');
                div.innerHTML = origText;
            } else {
                div.innerHTML = text;
            }
            document.querySelectorAll('.updateVal').forEach(el => el.addEventListener("click", eventHandler));
            updateData(div);
        }
    </script>
</html>