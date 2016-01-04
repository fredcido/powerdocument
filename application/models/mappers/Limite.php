<?php

/** 
 * 
 */
class Model_Mapper_Limite extends App_Model_Mapper_Abstract
{
    
    /**
     *
     * @param stdClass $usuario
     * @param stdClass $limite
     * @return Zend_Db_Table_Row_Abstract 
     */
    public function verificaLimiteUsuario( $usuario, $limite )
    {
	$historicoDb = App_Model_DbTable_Factory::get('Historico');
	$arquivoDb = App_Model_DbTable_Factory::get('Arquivo');
	
	// Define unidade de tempo de limite
	switch ( $limite->unidade_periodo ) {
	    case 'D':
		$periodo = 'DAY';
		break;
	    case 'S':
		$periodo = 'WEEK';
		break;
	    case 'M':
		$periodo = 'MONTH';
		break;
	    default:
		$periodo = 'YEAR';
	}
	
	$select = $historicoDb->select()
			   ->setIntegrityCheck( false )
			   ->from( 
				array( 'h' => $historicoDb ),
				array(
				    'total' => new Zend_Db_Expr('COUNT(1)'),
				    'size'  => new Zend_Db_Expr('IFNULL( SUM( a.tamanho ), 0 )')
				)
			    )
			    ->join( array( 'a' => $arquivoDb ), 'a.id = h.arquivo_id', array() )
			    ->where( 'h.acao = :acao' )
			    ->where( 'h.usuario_id = :usuario' )
			    ->where( 'h.data >= DATE_SUB(STR_TO_DATE(:data, "%Y-%m-%d %H:%i"), INTERVAL :limite '.$periodo.')' );
	
	$bind = array(
	    ':acao'	=> 'D',
	    ':usuario'  => $usuario->id,
	    ':data'	=> Zend_Date::now()->toString('yyyy-MM-dd HH:mm'),
	    ':limite'	=> $limite->periodo
	);
	
	return $historicoDb->fetchRow( $select->bind( $bind ) );
    }
}