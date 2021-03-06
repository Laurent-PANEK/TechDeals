<?php

use techdeals as PHP;

require_once "../includes/Session.php";
require_once "../includes/init.php";
require_once "../includes/Validator.php";
require_once "../includes/User.php";
require_once "../includes/Util.php";
require_once "../includes/UserRestrict.php";

$restrict = new PHP\UserRestrict();

$restrict->restrict();

$session = PHP\Session::getInstance();
$bdd = PHP\Database::getDatabase();
$user = PHP\User::getInstance();
$util = PHP\Util::class;
$validator = new PHP\Validator( $session );

if( !empty( $_GET ) ) {
	$validator->setData( $_GET );
	
	$selector = !$validator->isEmpty( 'selector' ) ? ' ' . htmlspecialchars( $_GET['selector'] ) . ' ' : ' AND ';
	
	$status = !$validator->isEmpty( 'status' ) ? htmlspecialchars( $_GET['status'] ) : null;
	
	if( $status != null ) {
		switch( $status ) {
			case 'SUPER_ADMIN':
				$validStatus = array(
					'USER',
					'PARTNER',
					'ADMIN',
				);
				break;
			case 'ADMIN':
				$validStatus = array(
					'USER',
					'PARTNER',
				);
				break;
			default:
				$validStatus = array( $status );
				break;
		}
		die( json_encode( $validStatus ) );
	}
	
	if( !$validator->isEmpty( 'cols' ) && !$validator->isEmpty( 'table' ) ) {
		
		$cols = htmlspecialchars( $_GET['cols'] );
		$table = htmlspecialchars( $_GET['table'] );
		$joinString = '';
		$whereString = '';
		$whereValue = array();
		
		if( !$validator->isEmpty( 'where' ) ) {
			
			$where = explode( ',', $_GET['where'] );
			
			$whereString = ' WHERE ';
			
			foreach( $where as $condition ) {
				if( !$validator->isEmpty( $condition ) ) {
					$selectorC = !$validator->isEmpty( 'selector_' . $condition ) ? ' ' . $_GET['selector_' . $condition] . ' ' : $selector;
					if( htmlspecialchars( $_GET[$condition] ) == 'NULL' ) {
						$whereString .= $condition . ' is NULL ' . $selector;
					} elseif(htmlspecialchars($_GET[$condition]) == 'NOTNULL') {
						$whereString .= $condition . ' is NOT NULL ' . $selector;
					} else {
						$whereString .= $condition . ' = :' . $condition . $selectorC;
						$whereValue[':' . $condition] = htmlspecialchars( $_GET[$condition] );
					}
					$lastSelector = $selectorC != $selector ? $selectorC : $selector;
				} else {
					die( 'ERROR : Value for ' . $condition . ' not found !' );
				}
			}
			
			$whereString = preg_replace( '/' . $lastSelector . '$/', '', $whereString );
		}
		
		if( !$validator->isEmpty( 'join' ) ) {
			
			$joins = explode( ',', $_GET['join'] );
			
			foreach( $joins as $join ) {
				if( !$validator->isEmpty( $join ) ) {
					$joinCol = htmlspecialchars( $_GET[$join] );
					$joinString .= ' LEFT JOIN ' . $join . ' ON ' . $table . '.' . $joinCol . ' = ' . $join . '.' . $joinCol;
				} else {
					die( 'ERROR : Colonne to join for ' . $join . ' not found !' );
				}
			}
		}
		
		echo json_encode( $bdd->query( 'SELECT ' . $cols . ' FROM ' . $table . $joinString . $whereString, $whereValue )->fetchAll( \PDO::FETCH_ASSOC ) );
		
		
	} else {
		echo 'ERROR : Colonnes Value not Found AND/OR Table Value not found';
	}
	
} else {
	echo 'ERROR : Empty Parameters !';
}