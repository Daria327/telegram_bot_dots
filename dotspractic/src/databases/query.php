<?php

require_once ( __DIR__ . '/../config.php' );

/*
 * You can make beautiful database queries using PDO and prepared queries, but alas,
 * free hosting does not provide access to php ini, so we'll get by with mysqli :(
 */


/*
 * connect to the database or write the error to a file
 */
$db = mysqli_connect(HOST_DB, LOGIN_DB, PASSWORD_DB, DB_NAME)
                  or file_put_contents(ERROR_FILE, mysqli_error($db), FILE_APPEND);

mysqli_set_charset($db, "utf8mb4");


function createUser( string $name, string $chat_id ) : void {
    global $db;
    if ($chat_id === '')
        return ;
    try {
        //escape special characters
        $name = mysqli_real_escape_string($db,$name);
        $chat_id = mysqli_real_escape_string($db,$chat_id);
        $sql = "INSERT IGNORE INTO Users (name, chat_id) VALUES ('$name', '$chat_id')";
        mysqli_query($db,$sql);
    }catch (Exception $e){
        file_put_contents(ERROR_FILE, $e->getMessage(), FILE_APPEND);
    }
}

function selectCity( string $chat_id, string $city ) : void {
    global $db;
    if( $chat_id === '')
        return ;
    try {
        $chat_id = mysqli_real_escape_string($db,$chat_id);
        $city = mysqli_real_escape_string($db,$city);
        $sql = "UPDATE Users SET city = '$city' WHERE chat_id = '$chat_id'";
        mysqli_query($db,$sql);
    }catch (Exception $e){
        file_put_contents(ERROR_FILE, $e->getMessage(), FILE_APPEND);
    }
}

function getCityName( string $chat_id ) : string {
    global $db;
    if ($chat_id === '')
        return '';
    try {
        $chat_id = mysqli_real_escape_string($db,$chat_id);
        $sql = "SELECT `city` FROM `Users` WHERE `chat_id` = '$chat_id'";
        return mysqli_fetch_all(mysqli_query($db,$sql),MYSQLI_ASSOC)[0]['city'] ?? '';
    }catch (Exception $e){
        file_put_contents(ERROR_FILE, $e->getMessage(), FILE_APPEND);
    }
}

function selectCompany( string $chat_id, string $company ) : void {
    global $db;
    if( $chat_id === '')
        return ;
    try {
        $chat_id = mysqli_real_escape_string($db,$chat_id);
        $company = mysqli_real_escape_string($db,$company);
        $sql = "UPDATE Users SET company = '$company' WHERE chat_id = '$chat_id'";
        mysqli_query($db,$sql);
    }catch (Exception $e){
        file_put_contents(ERROR_FILE, $e->getMessage(), FILE_APPEND);
    }
}

function getCompanyName( string $chat_id ) : string {
    global $db;
    if ($chat_id === '')
        return '';
    try {
        $chat_id = mysqli_real_escape_string($db,$chat_id);
        $sql = "SELECT `company` FROM `Users` WHERE `chat_id` = '$chat_id'";
        return mysqli_fetch_all(mysqli_query($db,$sql),MYSQLI_ASSOC)[0]['company'] ?? '';
    }catch (Exception $e){
        file_put_contents(ERROR_FILE, $e->getMessage(), FILE_APPEND);
    }
}

function selectItems(string $chat_id, string $name, string $id): void {
    global $db;
    if ($chat_id === '') {
        return;
    }
    
    try {
        $company = getCompanyName($chat_id);
        $city = getCityName($chat_id);
        $chat_id = mysqli_real_escape_string($db, $chat_id);
        $name = mysqli_real_escape_string($db, $name);
        $id = mysqli_real_escape_string($db, $id);
        $company = mysqli_real_escape_string($db, $company);
        $city = mysqli_real_escape_string($db, $city);
        
        // Getting records
        $existingRecordQuery = "SELECT count FROM Cart WHERE name = '$name' AND chat_id = '$chat_id'";
        $existingRecordResult = mysqli_query($db, $existingRecordQuery);
        
        if (mysqli_num_rows($existingRecordResult) > 0) {
            // Record already exists, update count field
            $existingRecord = mysqli_fetch_assoc($existingRecordResult);
            $count = (int) $existingRecord['count'] + 1;
            
            $updateQuery = "UPDATE Cart SET count = $count WHERE name = '$name' AND chat_id = '$chat_id'";
            mysqli_query($db, $updateQuery);
        } else {
            // Record does not exist, create a new one
            $sql = "INSERT INTO Cart (name, chat_id, itemId, company, city) VALUES ('$name', '$chat_id', '$id', '$company', '$city')";
            mysqli_query($db, $sql);
        }
    } catch (Exception $e) {
        file_put_contents(ERROR_FILE, $e->getMessage(), FILE_APPEND);
    }
}

function getSelectItems( string $chat_id ) : array {
    global $db;
    if ($chat_id === '')
        return [];
    try {
        $chat_id = mysqli_real_escape_string($db,$chat_id);
        $sql = "SELECT `name`,`count`,`company`,`city`,`itemId` FROM `Cart` WHERE chat_id = '$chat_id'";
        return mysqli_fetch_all(mysqli_query($db,$sql),MYSQLI_ASSOC) ?? [];
    }catch (Exception $e){
        file_put_contents(ERROR_FILE, $e->getMessage(), FILE_APPEND);
    }
}

function deleteItems( string $chat_id ) : void {
    global $db;
    if ($chat_id === '')
        return;
    try {
        $chat_id = mysqli_real_escape_string($db,$chat_id);
        $sql = "DELETE FROM `Cart` WHERE chat_id = '$chat_id'";
        mysqli_query($db,$sql);
    }catch (Exception $e){
        file_put_contents(ERROR_FILE, $e->getMessage(), FILE_APPEND);
    }
}