<?php

class Database {
	private $conn;
	
	function __construct() {
		global $wgDbServer, $wgDbUser, $wgDbPass, $wgDbTable;
		$this->conn = mysql_connect($wgDbServer, $wgDbUser, $wgDbPass);
		@mysql_select_db($wgDbTable, $this->conn);
	}
	
	function query( $query ) {
		$result = mysql_query( $query, $this->conn );
		return $result;
	}
	
	function select( $vars, $table, $conds='', $options = array(), $joinparams = array() ) {
        if( is_array( $vars ) ) {
            $vars = mysql_escape_string( implode( ',', $vars ) );
        }
        
        if( !is_array( $options ) ) {
            $options = array( $options );
        }
        
        if( is_array( $table ) ) {
            $from = mysql_escape_string( implode( ' JOIN ', $table ) );
            $from .= " ON " . mysql_escape_string( $joinparams[0] ) . " = " . mysql_escape_string( $joinparams[1] );
        } else {
            $from = mysql_escape_string( $table );
        }
        
        if( isset($options['ORDER BY']) ) {
        	$orderby = "ORDER BY ".mysql_escape_string( $options['ORDER BY'] )." ";
        }
        else {
        	$orderby = '';
        }
        
        if( isset($options['LIMIT']) ) {
        	$limit = "LIMIT ".mysql_escape_string( $options['LIMIT'] );
        }
        else {
        	$limit = '';
        }

        if( !empty( $conds ) ) {
            $sql = "SELECT $vars FROM $from WHERE $conds $orderby $limit;";
        } else {
            $sql = "SELECT $vars FROM $from $orderby $limit;";
        }
		
		return $this->query($sql);
    }
    
    function insert( $table, $vals ) {
    	$cols = array();
    	$v = array();
    	foreach ($vals as $col => $val) {
    		$cols[] = mysql_escape_string($col);
    		$v[] = '\''.mysql_escape_string($val).'\'';
    	}
    	
    	$sql = "INSERT INTO $table (";
    	$sql .= implode(', ',$cols);
    	$sql .= ') VALUES (\'';
    	$sql .= implode('\', \'',$vals);
    	$sql .= '\');';

		return $this->query($sql);
    }
    
    function update( $table, $sets, $conds = '') {
    	$s = array();
    	
    	foreach( $sets as $col => $val ) {
    		$s[] = "$col = ".mysql_escape_string($val);
    	}
    	
    	$sets = implode( ', ',$s );
    	
    	if( !empty( $conds ) ) {
            $sql = "UPDATE $table SET $sets WHERE $conds;";
        } else {
            $sql = "UPDATE $table SET $sets;";
        }
		
		return $this->query($sql);
    }	
	
}
