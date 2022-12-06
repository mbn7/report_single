<?php
    date_default_timezone_set("Israel");
    $dateInput = date("d-m-Y H:i:s");

    function setupCookieLan(){
        $COOKIE_SET = [
            'expires'  => time() + (10 * 365 * 24 * 60 * 60),
            'path'     => '/'
           ];
           
       $COOKIE_NAME    = "setLang";
       $COOKIE_VALUE   = 1;
        //   set cookie-language for the 1st time \\ 
       if(!isset($_COOKIE[$COOKIE_NAME])){
            setcookie($COOKIE_NAME, $COOKIE_VALUE, $COOKIE_SET);
            echo "<script> window.location.href = window.location.href.split('?')[0]; </script>";
            exit;
         }
       else{
           return $_COOKIE[$COOKIE_NAME];
       }
    }

    function addCookieData($category,$inicialAmount){
        $COOKIE_SET = [
            'expires'  => time() + (10 * 365 * 24 * 60 * 60),
            'path'     => '/'
           ];
           
        $COOKIE_NAME    = "dataCategory";
        //   set cookie-data_category for the 1st time \\ 
        if(!isset($_COOKIE[$COOKIE_NAME])){
            $data_category = [];
            array_push($data_category,['id'=> 1,'category' => $category, 'inicialAmount' => $inicialAmount]);
            setcookie($COOKIE_NAME, json_encode($data_category), $COOKIE_SET);
        } else {
            // add value on current cookie json \\
            $base = json_decode($_COOKIE[$COOKIE_NAME]);
            $newId = @$base[end(array_keys($base))]->id + 1;
            $newData = ['id'=> $newId,'category'=> $category ,'inicialAmount'=> $inicialAmount];
            array_push($base,$newData);
            setcookie($COOKIE_NAME, json_encode($base), 0, "/");
        }
    }

    function addCookieReport($categoryID,$inicialAmount,$dateInput){
        $COOKIE_SET = [
            'expires'  => time() + (10 * 365 * 24 * 60 * 60),
            'path'     => '/'
           ];
           
        $COOKIE_NAME    = "dataReport";
        //   set cookie-data_report for the 1st time \\ 
        if(!isset($_COOKIE[$COOKIE_NAME])){
            $data_report = [];
            array_push($data_report,['id'=> 1,'categoryID' => $categoryID, 'amount' => $inicialAmount,'date' => $dateInput]);
            setcookie($COOKIE_NAME, json_encode($data_report), $COOKIE_SET);
        } else {
            // add value on current cookie json \\
            $base = json_decode($_COOKIE[$COOKIE_NAME]);
            $newId = @$base[end(array_keys($base))]->id + 1;
            $newData = ['id'=> $newId,'categoryID' => $categoryID, 'amount' => $inicialAmount,'date' => $dateInput];
            array_push($base,$newData);
            setcookie($COOKIE_NAME, json_encode($base), $COOKIE_SET);
        }
    }

    function updateCookieData($updateData){
        $COOKIE_SET = [
            'expires'  => time() + (10 * 365 * 24 * 60 * 60),
            'path'     => '/'
        ];

        // get data from cookie-relative \\
        $base = json_decode($_COOKIE[$updateData[0]->type]);
        //TODO-M maybe global fun\\ 
        foreach ($base as $k => $mainBlock) {
            foreach ($mainBlock as $key => $value) {
                foreach ($updateData as $updateKey => $updateValue) {
                    if($key == 'id' && $value == $updateValue->id){
                        if($updateValue->category != '') {
                            $mainBlock->category = $updateValue->category;
                            $mainBlock->inicialAmount = $updateValue->inicialAmount;
                        } else {
                            $mainBlock->amount = $updateValue->amount;
                        }
                    }
                }
            }
        }
        // set updated data
        setcookie($updateData[0]->type, json_encode($base), $COOKIE_SET);
    }

    function deleteCookieData($item,$COOKIE_NAME){
        $COOKIE_SET = [
            'expires'  => time() + (10 * 365 * 24 * 60 * 60),
            'path'     => '/'
           ];

        $i = explode('_',$item);
        switch ($i[0]) {
            case 'allReports': setcookie($COOKIE_NAME,null, -1, '/'); break;
            default          : $res = removeDataByID($i[1],$COOKIE_NAME);
                                setcookie($COOKIE_NAME,json_encode($res),$COOKIE_SET);
                                break; 
        }
    }

    function removeDataByID($id,$COOKIE_NAME){
        $base = json_decode($_COOKIE[$COOKIE_NAME]);
        $tempArr = [];
        foreach ($base as $item) {
            if($item->id != $id){
                array_push($tempArr,$item);
            }
        }
        return $tempArr;
    }

    function getDataToUpdate($obj){
        $data = [];
        $count = 0;
        foreach ($obj as $key => $value) {
            if($key != 'type' && $key != 'updateItem'){
                $tempArrC = explode('_',$key);
                if($count == 0){
                    $tempData = (object) array('id'=> $tempArrC[1],'category' => $value,'type' => $obj['type']);        
                    $count++;
                } else {
                    $tempData->inicialAmount = $value;
                    array_push($data,$tempData);
                    $count = 0;
                }
            }
        }
        updateCookieData($data);
        // update reportList \\
    }

    function setLanguageData(){

        $defaultLanID = setupCookieLan();

        $tempD = json_decode(file_get_contents("src/lang_data.json"), true);

        $side = '';
        if($defaultLanID == 1){
            $side = 'ltr';
        } else {
            $side = 'rtl';
        }
        echo "<script> ".
                "document.body.dir = '".$side."'".
                "</script>";
        echo "<script>let lanData = []</script>";
        foreach ($tempD['type'] as $name => $id) {
            echo '<script>lanData.push({"id":'.$id.',"name":"'.$name.'"});</script>';
        }

        //  return data-language \\
        return $tempD["data"][array_search($defaultLanID,$tempD["type"])];
    }

    function getNameByid($id){
        $dataCategory = json_decode($_COOKIE['dataCategory']);
        foreach ($dataCategory as $key => $block) {
            if($block->id == $id){
                return $block->category;
            }
        }
    }

    function calcAmount($valBaseData,$dataReport) {
        $tempAmnt = intval($valBaseData->inicialAmount);
        if(!empty($dataReport )){
            foreach ($dataReport as $key => $block) {
                if($valBaseData->id == $block->categoryID)
                $tempAmnt -= intval($block->amount);
            }
        } 
        return $tempAmnt;
    }

    function getFinalamount($lanData) {
        $txt = '';
        if(isset($_COOKIE['dataCategory'])){
            $dataCategory = (array)json_decode($_COOKIE['dataCategory']);

            if(isset($_COOKIE['dataReport'])){
                $dataReport = (array)json_decode($_COOKIE['dataReport']);
            } else {
                $dataReport = 0;
            }

            foreach($dataCategory as $index => $data){
                $txt .= '<div id="c_'.$data->id.'" class="div_ini_amnt"><c_name id="c_name_'.$data->id.'">' . $data->category . '</c_name>: ';
                $finalAmount = calcAmount($data,$dataReport);
                $txt .= $lanData['currency'] ." <c_amount id='c_amount_".$data->id."'>". $finalAmount . '</c_amount><br></div>';
            }
        } else {
            $txt = '<div class="div_ini_amnt">'.$lanData['msg_no_data'].'</div>';
        }
        return $txt;
    }

    function showList($lanData) {
        $selected = [1,3,5];
        if(isset($_COOKIE['dataReport'])){
            $dataReport = json_decode($_COOKIE['dataReport']);
            // TODO-M set a select option when update report category
            foreach ($dataReport as $key => $block) {
                if(array_intersect($selected,$block->categoryID)){
                        $name = getNameByid($block->categoryID);
                        echo "<dt>" . $block->date ."</dt><dd>". $name . ": ". $lanData['currency'] ." <r_amount  id='r_amount_".$block->id."' class='updateVal'>". $block->amount ."</r_amount><span class='deleteReport' onclick='deleteReport(".$block->id.")'><img src='src/img/delete_icon.png' alt='delete_icon' style='width: 20px; height: 20px'></span></dd>"; 
                }
            }
        }
    }

    function showListByCalnd($lanData,$calendar) {
        if(isset($_COOKIE['dataReport'])){
            $dataReport = json_decode($_COOKIE['dataReport']);
            // TODO-M set a select option when update report category
            foreach ($dataReport as $key => $block) {
                    $name = getNameByid($block->categoryID);
                    $date = explode(' ',$block->date)[0];                                               //-> green / blue / red \\
                    $calendar->add_event($name . " ". $lanData['currency'] . $block->amount, $date, 1, 'green');
            }
        }
    }

    function getCategories(){
        if(isset($_COOKIE['dataCategory'])){
            return json_decode($_COOKIE['dataCategory']);
        }
    }

    $lanData = setLanguageData();

    // get the data & update \\ 
    if (isset($_GET['newDataReport'])) {
        if ($_GET['categoryID'] != '' && $_GET['amount'] != '') {
            $categoryID = $_GET['categoryID'];
            $amount = $_GET['amount'];
            addCookieReport($categoryID,$amount,$dateInput);
            echo "<script> window.location.href = window.location.href.split('?')[0]; </script>";
        }
    }

    if (isset($_GET['deleteData'])) {
        $item = $_GET['deleteData'];
        deleteCookieData($item,'dataReport');
        echo "<script> window.location.href = window.location.href.split('?')[0]; </script>";
    }
    if (isset($_GET['deleteItem'])) {
        //! TODO-M do foreach to get final cookies-dataCategory before setcookie()
        foreach ($_GET as $key => $value) {
            if($key != 'type' && $key != 'deleteItem'){
                deleteCookieData($key,'dataCategory');
            }
        }
        echo "<script> window.location.href = window.location.href.split('?')[0]; </script>";
    }
    if (isset($_GET['newItem'])) {
        $category = $_GET['category'];
        $inicialAmount = $_GET['inicialAmount'];
        addCookieData($category,$inicialAmount);
        echo "<script> window.location.href = window.location.href.split('?')[0]; </script>";
    }
    if (isset($_GET['updateData'])) {
        // need to be on array to run the 'updateCookieData()' \\
        $obj = [json_decode($_GET['updateData'])];
        updateCookieData($obj);
        echo "<script> window.location.href = window.location.href.split('?')[0]; </script>";
    }
    if (isset($_GET['updateItem'])) {
        getDataToUpdate($_GET);
        echo "<script> window.location.href = window.location.href.split('?')[0]; </script>";
    }
    
?>