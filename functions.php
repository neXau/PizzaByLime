<?php
// Подключение файла config.php
require_once 'configs/config.php';
require_once 'checkdb.php';

/**
 * Функция проверяет существует ли таблица
 *
 * @param string $table Название таблицы для проверки
 *
 * @return boolean Возращает значение в зависимости от существования таблицы
 */
function db_table_exist($table)
{
    global $config; // Используем массив $config, объявленный в файле config.php
    $db = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
    $result = $db->query("SHOW TABLES LIKE '" . $table . "'");
    if ($result->num_rows == 1) {
        return true;
    } else {
        return false;
    }
}

/**
 * Функция создает таблицы в базе данны с указаными значениями
 *
 * @param string $table Название таблицы
 * @param array $columns Ассоциативный массив, название колонки и значение
 *
 * @return string Возращает информацию о статусе
 */
function db_table_create($table, $columns)
{
    global $config;
    $db = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

    $sql_columns = array();
    foreach ($columns as $name => $type) {
        $sql_columns[] = "`$name` $type";
    }

    $sql = "CREATE TABLE IF NOT EXISTS `$table` (" . implode(", ", $sql_columns) . ");";
    if ($db->query($sql) === TRUE) {
        return "Таблица $table создана!";
    } else {
        return "Ошибка создания: " . $db->error;
    }
}

/**
 * Функция добавления данных в таблицу
 *
 * @param string $table Название таблицы
 * @param array $data Массив который должен соотвествовать структуре таблицы
 *
 * @return string Возращает информацию о статусе
 */
function db_add($table, $data)
{
    global $config;
    $db = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

    $sql_columns = array();
    $sql_values = array();
    foreach ($data as $name => $value) {
        $sql_columns[] = "`$name`";
        $sql_values[] = "'" . $db->real_escape_string($value) . "'";
    }

    $sql = "INSERT INTO `$table` (" . implode(", ", $sql_columns) . ") VALUES (" . implode(", ", $sql_values) . ")";
    if ($db->query($sql) === TRUE) {
        return "Запись добавлена успешно!";
    } else {
        return "Ошибка добавления : " . $db->error;
    }
}

/**
 * Функция чтения по id с базы
 *
 * @param string $table Название таблицы
 * @param number $id Запись в таблице по id
 *
 * @return array Ассоциативный массив с значениями как в таблице
 */
function db_read($table, $id)
{
    global $config;
    $db = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

    $id = $db->real_escape_string($id);
    $sql = "SELECT * FROM `$table` WHERE `id` = $id";
    $result = $db->query($sql);
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        return $row;
    } else {
        return array();
    }
}

/**
 * Функция обновляет значение в таблицк
 *
 * @param string $table Название таблицы
 * @param number $id Номер записи в таблице
 * @param array $new_data Новые данные записи
 *
 * @return boolean Возрат для проверки
 */
function db_upd($table, $id, $new_data)
{
    global $config;
    $db = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

    $id = $db->real_escape_string($id);
    $set_clause = '';
    foreach ($new_data as $column => $value) {
        $column = $db->real_escape_string($column);
        $value = $db->real_escape_string($value);
        $set_clause .= "`$column` = '$value', ";
    }
    $set_clause = rtrim($set_clause, ', ');
    $sql = "UPDATE `$table` SET $set_clause WHERE `id` = $id";
    $result = $db->query($sql);
    return $result;
}