<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

// ezSQL Constants
define("EZSQL_VERSION","1.25");
define("OBJECT","OBJECT");
define("ARRAY_A","ARRAY_A");
define("ARRAY_N","ARRAY_N");

// ==================================================================
// The Main Class

class db {

    var $trace = false;
    var $debug_all = false;
    var $show_errors = true;
    var $num_queries = 0;
    var $last_query;
    var $col_info;
    var $debug_called;
    var $vardump_called;

    var $dbh;
    var $result;
    var $last_result = array();
    var $rows_affected = 0;
    var $insert_id = 0;
    var $num_rows = 0;

    // ==================================================================
    // DB Constructor

function __construct($dbuser, $dbpassword, $dbname, $dbhost)
{

    $this->dbh = mysqli_init();
    mysqli_ssl_set($this->dbh, NULL, NULL, "/home/site/wwwroot/includes/php/DigiCertCombined.pem", NULL, NULL);

    if (mysqli_real_connect($this->dbh, $dbhost, $dbuser, $dbpassword, $dbname, 3306, "", MYSQLI_CLIENT_SSL)) {

        $this->result = null;

    } else {
        $this->print_error("Error establishing a database connection: " . mysqli_connect_error());
    }

    $this->select($dbname);
}

    // ==================================================================
    // Select DB

    function select($db)
    {
        if (!@mysqli_select_db($this->dbh, $db))
        {
            $this->print_error("<ol><b>Error selecting database <u>$db</u></b></ol>");
        }
    }

    // ==================================================================
    // Escape string

    function escape($str)
    {
        return mysqli_real_escape_string($this->dbh, stripslashes($str));
    }

    // ==================================================================
    // Print SQL error

function print_error($str = "")
{
    global $EZSQL_ERROR;

    if (!$str) {
        if ($this->dbh) {
            $str = mysqli_error($this->dbh);
        } else {
            $str = "Unknown error";
        }
    }

    $EZSQL_ERROR[] = array(
        "query" => $this->last_query,
        "error_str" => $str
    );

    if ($this->show_errors)
    {
        print "<blockquote><font face=arial size=2 color=ff0000>";
        print "<b>SQL/DB Error --</b> ";
        print "[<font color=000077>$str</font>]";
        print "</font></blockquote>";
    }
    else
    {
        return false;
    }
}

    function show_errors()
    {
        $this->show_errors = true;
    }

    function hide_errors()
    {
        $this->show_errors = false;
    }

    // ==================================================================
    // Flush cached results

    function flush()
    {
        $this->last_result = array();
        $this->col_info = null;
        $this->last_query = null;
    }

    // ==================================================================
    // Query

function query($query)
{
    $return_val = 0;

    $query = trim($query);

    $this->flush();

    $this->func_call = "\$db->query(\"$query\")";

    $this->last_query = $query;

    $this->num_queries++;

    $this->result = mysqli_query($this->dbh, $query);

    if (mysqli_error($this->dbh)) {
        $this->print_error();
        return false;
    }

    // Handle INSERT / UPDATE / DELETE
	if (preg_match("/^\s*(insert|delete|update|replace)/i", $query)) {

	    $this->rows_affected = mysqli_affected_rows($this->dbh);

	    if (preg_match("/^\s*(insert|replace)/i", $query)) {
		$this->insert_id = mysqli_insert_id($this->dbh);
	    }

	    return true;
	}

    // Handle SELECT queries
    if ($this->result instanceof mysqli_result)
    {
        $num_rows = 0;

        while ($row = mysqli_fetch_object($this->result))
        {
            $this->last_result[$num_rows] = $row;
            $num_rows++;
        }

        $this->num_rows = $num_rows;

        return $this->num_rows;
    }

    return false;
}

    // ==================================================================
    // get_var

    function get_var($query = null, $x = 0, $y = 0)
    {
        $this->func_call = "\$db->get_var(\"$query\",$x,$y)";

        if ($query)
        {
            $this->query($query);
        }

        if (isset($this->last_result[$y]))
        {
            $values = array_values(get_object_vars($this->last_result[$y]));
        }

        return (isset($values[$x]) && $values[$x] !== '') ? $values[$x] : null;
    }

    // ==================================================================
    // get_row

    function get_row($query = null, $output = OBJECT, $y = 0)
    {
        $this->func_call = "\$db->get_row(\"$query\",$output,$y)";

        if ($query)
        {
            $this->query($query);
        }

        if ($output == OBJECT)
        {
            return $this->last_result[$y] ?? null;
        }
        elseif ($output == ARRAY_A)
        {
            return isset($this->last_result[$y]) ? get_object_vars($this->last_result[$y]) : null;
        }
        elseif ($output == ARRAY_N)
        {
            return isset($this->last_result[$y]) ? array_values(get_object_vars($this->last_result[$y])) : null;
        }
        else
        {
            $this->print_error("Invalid output type");
        }
    }

    // ==================================================================
    // get_col

function get_col($query = null, $x = 0)
{
    $new_array = array();

    if ($query)
    {
        $this->query($query);
    }

    for ($i = 0; $i < count($this->last_result); $i++)
    {
        $new_array[$i] = $this->get_var(null, $x, $i);
    }

    return $new_array;
}

    // ==================================================================
    // get_results

function get_results($query = null, $output = OBJECT)
{
    $this->func_call = "\$db->get_results(\"$query\", $output)";

    if ($query)
    {
        $this->query($query);
    }

    if ($output == OBJECT)
    {
        return $this->last_result;
    }
    elseif ($output == ARRAY_A || $output == ARRAY_N)
    {
        if ($this->last_result)
        {
            $i = 0;
            foreach ($this->last_result as $row)
            {
                $new_array[$i] = get_object_vars($row);

                if ($output == ARRAY_N)
                {
                    $new_array[$i] = array_values($new_array[$i]);
                }

                $i++;
            }

            return $new_array;
        }
        else
        {
            return null;
        }
    }
}

    // ==================================================================
    // Column metadata

function get_col_info($info_type = "name", $col_offset = -1)
{
    if ($this->col_info)
    {
        if ($col_offset == -1)
        {
            $i = 0;
            foreach ($this->col_info as $col)
            {
                $new_array[$i] = $col->{$info_type};
                $i++;
            }
            return $new_array;
        }
        else
        {
            return $this->col_info[$col_offset]->{$info_type};
        }
    }
}

    // ==================================================================
    // Variable dump

function vardump($mixed = '')
{
    echo "<pre>";
    print_r(($mixed ? $mixed : "No Value / False"));
    echo "</pre>";
}

function dumpvar($mixed)
{
    $this->vardump($mixed);
}

    // ==================================================================
    // Debug

function debug()
{
    echo "<pre>";
    echo "Last Query: ".$this->last_query."\n";
    echo "Rows Returned: ".count($this->last_result)."\n";
    print_r($this->last_result);
    echo "</pre>";
}

}

// ==================================================================
// Create DB instance

$db = new db("clackswebuser","pY74ju!Qben","clacksweb2","clacksweb-mysql01.mysql.database.azure.com");
//$db = new db("clackswebuser","pY74ju!Qben","clacksweb2","clacksweb-mysql01-replica-84.mysql.database.azure.com");

?>